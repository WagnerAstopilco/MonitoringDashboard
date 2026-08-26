<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected const TYPE_INCOME = 'income';
    protected const TYPE_EXPENSE = 'expense';

    /**
     * Genera el reporte completo para el dashboard: periodo, KPIs, serie mensual,
     * desglose por usuario y detalle paginado de transacciones.
     *
     * $filters admite: date_from, date_to, user_ids (array).
     */
    public function generateReport(array $filters, int $perPage = 50): array
    {
        [$dateFrom, $dateTo] = $this->resolvePeriod($filters);

        return [
            'period' => [
                'from' => $dateFrom->toDateString(),
                'to'   => $dateTo->toDateString(),
            ],
            'kpis'           => $this->getKpis($filters, $dateFrom, $dateTo),
            'monthly_series' => $this->getMonthlySeries($filters, $dateFrom, $dateTo),
            'daily_series'   => $this->getDailySeries($filters, $dateFrom, $dateTo),
            'by_user'        => $this->getByUser($filters, $dateFrom, $dateTo),
            'data'           => $this->getDetail($filters, $dateFrom, $dateTo, $perPage),
        ];
    }

    /**
     * Si no se especifican fechas, usa el mes en curso por defecto.
     */
    protected function resolvePeriod(array $filters): array
    {
        $dateFrom = !empty($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $dateTo = !empty($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : Carbon::now()->endOfMonth();

        return [$dateFrom, $dateTo];
    }

    protected function baseQuery(array $filters, Carbon $dateFrom, Carbon $dateTo): Builder
    {
        $query = Transaction::query()
            ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if (!empty($filters['user_ids'])) {
            $query->whereIn('user_id', $filters['user_ids']);
        }

        return $query;
    }

    /**
     * KPIs principales: ingresos, egresos, utilidad neta, gasto promedio
     * y crecimiento respecto al periodo anterior de igual duración.
     *
     * La utilidad neta usa la ganancia real de los ingresos
     * (Σ (precio - costo) × cantidad, columna `profit`), no el monto bruto.
     * Los egresos se restan por su monto completo, ya que no tienen margen
     * de costo/precio asociado.
     */
    public function getKpis(array $filters, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : $this->resolvePeriod($filters);

        $sums = $this->sumsByType($filters, $dateFrom, $dateTo);

        $income        = $sums[self::TYPE_INCOME]['total'] ?? 0.0;
        $incomeProfit  = $sums[self::TYPE_INCOME]['total_profit'] ?? 0.0;
        $expenses      = $sums[self::TYPE_EXPENSE]['total'] ?? 0.0;
        $expenseCount  = $sums[self::TYPE_EXPENSE]['count'] ?? 0;
        $netProfit     = $incomeProfit - $expenses;

        // Periodo anterior de la misma duración (p. ej. mes anterior si el periodo es un mes completo)
        $periodLengthDays = $dateFrom->diffInDays($dateTo) + 1;
        $previousDateTo   = $dateFrom->copy()->subDay()->endOfDay();
        $previousDateFrom = $previousDateTo->copy()->subDays($periodLengthDays - 1)->startOfDay();

        $previousSums = $this->sumsByType($filters, $previousDateFrom, $previousDateTo);

        $previousIncome       = $previousSums[self::TYPE_INCOME]['total'] ?? 0.0;
        $previousIncomeProfit = $previousSums[self::TYPE_INCOME]['total_profit'] ?? 0.0;
        $previousExpenses     = $previousSums[self::TYPE_EXPENSE]['total'] ?? 0.0;
        $previousNetProfit    = $previousIncomeProfit - $previousExpenses;

        return [
            'income'                   => round($income, 2),
            'expenses'                 => round($expenses, 2),
            'net_profit'               => round($netProfit, 2),
            'average_expense'          => $expenseCount > 0 ? round($expenses / $expenseCount, 2) : 0.0,
            'income_growth_percentage' => $this->calculateGrowthPercentage($previousIncome, $income),
            'profit_growth_percentage' => $this->calculateGrowthPercentage($previousNetProfit, $netProfit),
            'previous_period'          => [
                'from' => $previousDateFrom->toDateString(),
                'to'   => $previousDateTo->toDateString(),
            ],
        ];
    }

    /**
     * Suma y conteo agrupado por transaction_type dentro de un rango de fechas.
     * `total` es el monto bruto (amount = Σ precio × cantidad) y
     * `total_profit` es la ganancia real (profit = Σ (precio - costo) × cantidad),
     * calculada a nivel de transacción (ver TransactionController::store/update).
     */
    protected function sumsByType(array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        return $this->baseQuery($filters, $dateFrom, $dateTo)
            ->select(
                'transaction_type',
                DB::raw('SUM(amount) as total'),
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('transaction_type')
            ->get()
            ->keyBy('transaction_type')
            ->map(fn ($row) => [
                'total'        => (float) $row->total,
                'total_profit' => (float) $row->total_profit,
                'count'        => (int) $row->count,
            ])
            ->all();
    }

    protected function calculateGrowthPercentage(float $previous, float $current): float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : ($current < 0 ? -100.0 : 0.0);
        }

        return round((($current - $previous) / abs($previous)) * 100, 2);
    }

    /**
     * Serie mensual de ingresos/egresos/utilidad, lista para graficar (línea o barras).
     * La utilidad usa la ganancia real de los ingresos (profit), no el monto bruto.
     */
    public function getMonthlySeries(array $filters, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : $this->resolvePeriod($filters);

        $rows = $this->baseQuery($filters, $dateFrom, $dateTo)
            ->select(
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month"),
                'transaction_type',
                DB::raw('SUM(amount) as total'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->groupBy('month', 'transaction_type')
            ->orderBy('month')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row->month]['income'] ??= 0.0;
            $grouped[$row->month]['income_profit'] ??= 0.0;
            $grouped[$row->month]['expenses'] ??= 0.0;

            if ($row->transaction_type === self::TYPE_INCOME) {
                $grouped[$row->month]['income'] = (float) $row->total;
                $grouped[$row->month]['income_profit'] = (float) $row->total_profit;
            } elseif ($row->transaction_type === self::TYPE_EXPENSE) {
                $grouped[$row->month]['expenses'] = (float) $row->total;
            }
        }

        $series = [];

        foreach ($grouped as $month => $values) {
            $income        = $values['income'] ?? 0.0;
            $incomeProfit  = $values['income_profit'] ?? 0.0;
            $expenses      = $values['expenses'] ?? 0.0;

            $series[] = [
                'month'      => $month,
                'income'     => round($income, 2),
                'expenses'   => round($expenses, 2),
                'net_profit' => round($incomeProfit - $expenses, 2),
            ];
        }

        return $series;
    }

    /**
     * Serie diaria de ingresos/egresos/utilidad, usada por el gráfico
     * "Avance del periodo" del dashboard. La utilidad usa la ganancia real
     * de los ingresos (profit), no el monto bruto.
     */
    public function getDailySeries(array $filters, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : $this->resolvePeriod($filters);

        $rows = $this->baseQuery($filters, $dateFrom, $dateTo)
            ->select(
                DB::raw('DATE(transaction_date) as day'),
                'transaction_type',
                DB::raw('SUM(amount) as total'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->groupBy('day', 'transaction_type')
            ->orderBy('day')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row->day]['income'] ??= 0.0;
            $grouped[$row->day]['income_profit'] ??= 0.0;
            $grouped[$row->day]['expenses'] ??= 0.0;

            if ($row->transaction_type === self::TYPE_INCOME) {
                $grouped[$row->day]['income'] = (float) $row->total;
                $grouped[$row->day]['income_profit'] = (float) $row->total_profit;
            } elseif ($row->transaction_type === self::TYPE_EXPENSE) {
                $grouped[$row->day]['expenses'] = (float) $row->total;
            }
        }

        $series = [];

        foreach ($grouped as $day => $values) {
            $income       = $values['income'] ?? 0.0;
            $incomeProfit = $values['income_profit'] ?? 0.0;
            $expenses     = $values['expenses'] ?? 0.0;

            $series[] = [
                'date'       => $day,
                'income'     => round($income, 2),
                'expenses'   => round($expenses, 2),
                'net_profit' => round($incomeProfit - $expenses, 2),
            ];
        }

        return $series;
    }

    /**
     * Desglose de ingresos/egresos/utilidad por usuario responsable.
     * La utilidad usa la ganancia real de los ingresos (profit), no el monto bruto.
     */
    public function getByUser(array $filters, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): array
    {
        [$dateFrom, $dateTo] = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : $this->resolvePeriod($filters);

        $rows = $this->baseQuery($filters, $dateFrom, $dateTo)
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->select(
                'transactions.user_id',
                'users.name as user_name',
                'transactions.transaction_type',
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('SUM(transactions.profit) as total_profit')
            )
            ->groupBy('transactions.user_id', 'users.name', 'transactions.transaction_type')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row->user_id]['user_id']   = $row->user_id;
            $grouped[$row->user_id]['user_name'] = $row->user_name;
            $grouped[$row->user_id]['income'] ??= 0.0;
            $grouped[$row->user_id]['income_profit'] ??= 0.0;
            $grouped[$row->user_id]['expenses'] ??= 0.0;

            if ($row->transaction_type === self::TYPE_INCOME) {
                $grouped[$row->user_id]['income'] = (float) $row->total;
                $grouped[$row->user_id]['income_profit'] = (float) $row->total_profit;
            } elseif ($row->transaction_type === self::TYPE_EXPENSE) {
                $grouped[$row->user_id]['expenses'] = (float) $row->total;
            }
        }

        return collect($grouped)
            ->map(function ($item) {
                $item['net_profit'] = round(($item['income_profit'] ?? 0) - ($item['expenses'] ?? 0), 2);
                $item['income']     = round($item['income'] ?? 0, 2);
                $item['expenses']   = round($item['expenses'] ?? 0, 2);
                unset($item['income_profit']);

                return $item;
            })
            ->sortByDesc('income')
            ->values()
            ->all();
    }

    /**
     * Detalle paginado de transacciones (para tabla/exportación), con relaciones cargadas.
     */
    public function getDetail(array $filters, ?Carbon $dateFrom = null, ?Carbon $dateTo = null, int $perPage = 50)
    {
        [$dateFrom, $dateTo] = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : $this->resolvePeriod($filters);

        return $this->baseQuery($filters, $dateFrom, $dateTo)
            ->with(['client', 'user', 'promotion', 'transactionDetails.service', 'transactionPayments.paymentMethod'])
            ->latest('transaction_date')
            ->paginate($perPage);
    }
}
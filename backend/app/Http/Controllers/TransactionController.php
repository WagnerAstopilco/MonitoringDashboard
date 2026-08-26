<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Service;
use App\Services\TransactionService;
use App\Exports\TransactionsReportExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['client', 'user', 'promotion', 'transactionDetails.service', 'transactionPayments.paymentMethod'])
            ->latest()
            ->paginate(50);

        return TransactionResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreTransactionRequest $request)
    // {    
    //     $validated = $request->validated();

    //     $transaction = DB::transaction(function () use ($validated) {

    //         $transaction = Transaction::create([
    //             'client_id'        => $validated['client_id'] ?? null,
    //             'user_id'          => Auth::user()->id,
    //             'promotion_id'     => $validated['promotion_id'] ?? null,
    //             'transaction_date' => $validated['transaction_date'],
    //             'transaction_type' => $validated['transaction_type'],
    //             'delivery_date'    => $validated['delivery_date'] ?? null,
    //             'responsible'      => $validated['responsible'],
    //             'amount'           => 0,
    //             'profit'           => 0,
    //             'status'           => 'pending',
    //         ]);

    //         $total = 0;
    //         $totalProfit = 0;

    //         foreach ($validated['details'] as $detail) {

    //             $service = Service::findOrFail($detail['service_id']);

    //             $subtotal = $this->calculateSubtotal(
    //                 $service,
    //                 $detail['quantity']
    //             );

    //             $profit = $this->calculateProfit(
    //                 $service,
    //                 $detail['quantity']
    //             );

    //             $transaction->transactionDetails()->create([
    //                 'service_id'   => $service->id,
    //                 'promotion_id' => $detail['promotion_id'] ?? null,
    //                 'unit_price'   => $service->price,
    //                 'unit_cost'    => $service->cost ?? 0,
    //                 'quantity'     => $detail['quantity'],
    //                 'subtotal'     => $subtotal,
    //                 'profit'       => $profit,
    //             ]);

    //             $total += $subtotal;
    //             $totalProfit += $profit;
    //         }

    //         $transaction->amount = $total;
    //         // La ganancia real solo aplica a transacciones de tipo ingreso.
    //         // Un egreso es un gasto, no una venta con margen sobre costo/precio.
    //         $transaction->profit = $validated['transaction_type'] === 'income' ? $totalProfit : 0;
    //         $transaction->status = 'pending';

    //         $transaction->save();

    //         return $transaction;
    //     });

    //     return new TransactionResource(
    //         $transaction->load([
    //             'client',
    //             'user',
    //             'promotion',
    //             'transactionDetails.service',
    //             'transactionPayments.paymentMethod'
    //         ])
    //     );
    // }

    public function store(StoreTransactionRequest $request)
{
    $validated = $request->validated();

    $transaction = DB::transaction(function () use ($validated) {

        $transaction = Transaction::create([
            'client_id'        => $validated['client_id'] ?? null,
            'user_id'          => Auth::user()->id,
            'promotion_id'     => $validated['promotion_id'] ?? null,
            'transaction_date' => $validated['transaction_date'],
            'transaction_type' => $validated['transaction_type'],
            'delivery_date'    => $validated['delivery_date'] ?? null,
            'responsible'      => $validated['responsible'],
            'amount'           => 0,
            'profit'           => 0,
            'status'           => 'pending',
        ]);

        $total = 0;
        $totalProfit = 0;

        foreach ($validated['details'] ?? [] as $detail) {

            $service = Service::findOrFail($detail['service_id']);

            $subtotal = $this->calculateSubtotal(
                $service,
                $detail['quantity']
            );

            $profit = $this->calculateProfit(
                $service,
                $detail['quantity']
            );

            $transaction->transactionDetails()->create([
                'service_id'   => $service->id,
                'promotion_id' => $detail['promotion_id'] ?? null,
                'unit_price'   => $service->price,
                'unit_cost'    => $service->cost ?? 0,
                'quantity'     => $detail['quantity'],
                'subtotal'     => $subtotal,
                'profit'       => $profit,
            ]);

            $total += $subtotal;
            $totalProfit += $profit;
        }


        // Ingreso: monto calculado a partir de los servicios
        if ($validated['transaction_type'] === 'income') {

            $transaction->amount = $total;
            $transaction->profit = $totalProfit;

        }


        // Egreso: monto ingresado manualmente
        if ($validated['transaction_type'] === 'expense') {

            $transaction->amount = $validated['amount'];
            $transaction->profit = 0;

        }


        $transaction->status = 'pending';

        $transaction->save();

        return $transaction;
    });

    return new TransactionResource(
        $transaction->load([
            'client',
            'user',
            'promotion',
            'transactionDetails.service',
            'transactionPayments.paymentMethod'
        ])
    );
}
    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load([
            'client',
            'user',
            'promotion',
            'transactionDetails.service',
            'transactionPayments.paymentMethod'
        ]);

        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $transaction) {

            $transaction->update([
                'client_id'        => $validated['client_id'] ?? null,
                'promotion_id'     => $validated['promotion_id'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'transaction_type' => $validated['transaction_type'],
                'delivery_date'    => $validated['delivery_date'] ?? null,
                'responsible'      => $validated['responsible'],
            ]);

            $existingIds = $transaction->transactionDetails()
                ->pluck('id')
                ->toArray();

            $receivedIds = [];

            $total = 0;
            $totalProfit = 0;

            foreach ($validated['details'] as $detail) {

                $service = Service::findOrFail($detail['service_id']);

                $subtotal = $this->calculateSubtotal($service, $detail['quantity']);
                $profit   = $this->calculateProfit($service, $detail['quantity']);

                $detailData = [
                    'service_id'   => $service->id,
                    'promotion_id' => $detail['promotion_id'] ?? null,
                    'unit_price'   => $service->price,
                    'unit_cost'    => $service->cost ?? 0,
                    'quantity'     => $detail['quantity'],
                    'subtotal'     => $subtotal,
                    'profit'       => $profit,
                ];

                if (!empty($detail['id'])) {

                    $transactionDetail = $transaction
                        ->transactionDetails()
                        ->findOrFail($detail['id']);

                    $transactionDetail->update($detailData);

                    $receivedIds[] = $transactionDetail->id;
                } else {

                    $newDetail = $transaction
                        ->transactionDetails()
                        ->create($detailData);

                    $receivedIds[] = $newDetail->id;
                }

                $total += $subtotal;
                $totalProfit += $profit;
            }

            $idsToDelete = array_diff($existingIds, $receivedIds);

            if (!empty($idsToDelete)) {

                $transaction
                    ->transactionDetails()
                    ->whereIn('id', $idsToDelete)
                    ->delete();
            }

            $paid = $transaction->transactionPayments()->sum('amount');

            $transaction->amount = $total;
            // Igual que en store(): la ganancia real solo aplica a ingresos.
            $transaction->profit = $validated['transaction_type'] === 'income' ? $totalProfit : 0;
            $transaction->status = $this->calculateStatus(
                $transaction->amount,
                $paid
            );

            $transaction->save();
        });

        return new TransactionResource(
            $transaction->fresh()->load([
                'client',
                'user',
                'promotion',
                'transactionDetails.service',
                'transactionPayments.paymentMethod'
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Transacción eliminada correctamente.'
        ]);
    }

    /**
     * Subtotal cobrado por un servicio dentro de la transacción: precio × cantidad.
     * Es lo que se muestra al cliente/usuario en las vistas de transacciones.
     */
    private function calculateSubtotal(Service $service, int $quantity)
    {
        return $service->price * $quantity;
    }

    /**
     * Ganancia neta real de un servicio dentro de la transacción:
     * (precio - costo) × cantidad. Si el servicio no tiene costo, cost = 0
     * y la ganancia equivale al 100% del subtotal.
     */
    private function calculateProfit(Service $service, int $quantity)
    {
        $cost = $service->cost ?? 0;

        return ($service->price - $cost) * $quantity;
    }

    private function calculateStatus(float $amount,float $paid) 
    {
        if ($paid <= 0) {
            return 'pending';
        }

        if ($paid >= $amount) {
            return 'paid';
        }

        return 'partially_paid';
    }

    /**
     * Change the delivery status of the specified resource.
     */
    public function changeDeliveryStatus(Transaction $transaction)
    {
        if ($transaction->delivery_status === false) {
            $transaction->delivery_status = true;
        }

        $transaction->save();

        $transaction->refresh();

        return new TransactionResource($transaction);
    }

    /**
     * Reporte / dashboard: KPIs (ingresos, egresos, utilidad, crecimiento, gasto promedio),
     * serie mensual para gráficas, desglose por usuario y detalle paginado.
     */
    // public function reports(Request $request)
    // {
    //     $validated = $request->validate([
    //         'date_from'   => ['nullable', 'date'],
    //         'date_to'     => ['nullable', 'date', 'after_or_equal:date_from'],
    //         'user_ids'    => ['nullable', 'array'],
    //         'user_ids.*'  => ['integer', 'exists:users,id'],
    //         'per_page'    => ['nullable', 'integer', 'min:1', 'max:200'],
    //     ]);

    //     $perPage = $validated['per_page'] ?? 50;

    //     $report = $this->transactionService->generateReport($validated, $perPage);

    //     return response()->json([
    //         'period'         => $report['period'],
    //         'kpis'           => $report['kpis'],
    //         'monthly_series' => $report['monthly_series'],
    //         'by_user'        => $report['by_user'],
    //         'data'           => TransactionResource::collection($report['data']),
    //     ]);
    // }

    public function reports(Request $request)
    {
        $validated = $request->validate([
            'date_from'   => ['nullable', 'date'],
            'date_to'     => ['nullable', 'date', 'after_or_equal:date_from'],
            'user_ids'    => ['nullable', 'array'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);
 
        $perPage = $validated['per_page'] ?? 50;
 
        $report = $this->transactionService->generateReport($validated, $perPage);
 
        return response()->json([
            'period'         => $report['period'],
            'kpis'           => $report['kpis'],
            'monthly_series' => $report['monthly_series'],
            'daily_series'   => $report['daily_series'],
            'by_user'        => $report['by_user'],
            'data'           => TransactionResource::collection($report['data']),
        ]);
    }
    /**
     * Exporta el reporte de transacciones del periodo indicado a Excel.
     * Usado tanto por el botón "Exportar general" (mes actual) como por
     * "Exportar por periodo" (fechas personalizadas) del dashboard.
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to'   => ['required', 'date', 'after_or_equal:date_from'],
        ]);

        $filename = "reporte-transacciones-{$validated['date_from']}-a-{$validated['date_to']}.xlsx";

        return Excel::download(
            new TransactionsReportExport($validated['date_from'], $validated['date_to']),
            $filename
        );
    }
}
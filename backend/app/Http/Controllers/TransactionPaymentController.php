<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionPaymentRequest;
use App\Http\Requests\UpdateTransactionPaymentRequest;
use App\Http\Resources\TransactionPaymentResource;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\DB;

class TransactionPaymentController extends Controller
{
    public function index()
    {
        $transactionPayments = TransactionPayment::with([
            'paymentMethod',
            'transaction'
        ])->get();

        return TransactionPaymentResource::collection($transactionPayments);
    }

    public function store(StoreTransactionPaymentRequest $request)
    {
        $validated = $request->validated();
        
        $validated['payment_date'] = now();

        $payment = DB::transaction(function () use ($validated) {

            $payment = TransactionPayment::create($validated);

            $this->refreshTransactionStatus($payment->transaction);

            return $payment;
        });

        return new TransactionPaymentResource(
            $payment->load([
                'paymentMethod',
                'transaction'
            ])
        );
    }

    public function show(TransactionPayment $transactionPayment)
    {
        $transactionPayment->load([
            'paymentMethod',
            'transaction'
        ]);

        return new TransactionPaymentResource($transactionPayment);
    }

    public function update(UpdateTransactionPaymentRequest $request, TransactionPayment $transactionPayment)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $transactionPayment) {

            $transactionPayment->update($validated);

            $this->refreshTransactionStatus(
                $transactionPayment->transaction
            );
        });

        return new TransactionPaymentResource(
            $transactionPayment->fresh()->load([
                'paymentMethod',
                'transaction'
            ])
        );
    }

    public function destroy(TransactionPayment $transactionPayment)
    {
        DB::transaction(function () use ($transactionPayment) {

            $transaction = $transactionPayment->transaction;

            $transactionPayment->delete();

            $this->refreshTransactionStatus($transaction);
        });

        return response()->json([
            'message' => 'Pago eliminado correctamente.'
        ]);
    }

    /**
     * Recalcula el estado de la transacción según el monto pagado.
     */
    private function refreshTransactionStatus(Transaction $transaction): void
    {
        $paid = $transaction->transactionPayments()->sum('amount');

        $transaction->status = $this->calculateStatus(
            $transaction->amount,
            $paid
        );

        $transaction->save();
    }

    /**
     * Determina el estado de la transacción.
     */
    private function calculateStatus(float $amount, float $paid): string
    {
        if ($paid <= 0) {
            return 'pending';
        }

        if ($paid >= $amount) {
            return 'paid';
        }

        return 'partially_paid';
    }
}

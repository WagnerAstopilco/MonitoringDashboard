<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $transactions= Transaction::with(['user','client','transactionPayments','promotion','transactionDetails'])->get();
        // return TransactionResource::collection($transactions);
        $transactions = Transaction::with(['client', 'user', 'promotion', 'transactionDetails.service', 'transactionPayments.paymentMethod'])->latest()->get();

        return TransactionResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        $transaction = DB::transaction(function () use ($validated) {

            $transaction = Transaction::create([
                'client_id'        => $validated['client_id'] ?? null,
                'user_id'          => Auth::id(),
                'promotion_id'     => $validated['promotion_id'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'transaction_type' => $validated['transaction_type'],
                'responsible'      => $validated['responsible'],
                'amount'           => 0,
                'status'           => 'pending',
            ]);

            $total = 0;

            foreach ($validated['details'] as $detail) {

                $service = Service::findOrFail($detail['service_id']);

                $subtotal = $this->calculateSubtotal(
                    $service,
                    $detail['quantity']
                );

                $transaction->transactionDetails()->create([
                    'service_id'   => $service->id,
                    'promotion_id' => $detail['promotion_id'] ?? null,
                    'unit_price'   => $service->price,
                    'quantity'     => $detail['quantity'],
                    'subtotal'     => $subtotal,
                ]);

                $total += $subtotal;
            }

            $transaction->amount = $total;
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
                'responsible'      => $validated['responsible'],
            ]);

            $existingIds = $transaction->transactionDetails()
                ->pluck('id')
                ->toArray();

            $receivedIds = [];

            $total = 0;

            foreach ($validated['details'] as $detail) {

                $service = Service::findOrFail($detail['service_id']);

                $subtotal = $this->calculateSubtotal($service, $detail['quantity']);

                $detailData = [
                    'service_id'   => $service->id,
                    'promotion_id' => $detail['promotion_id'] ?? null,
                    'unit_price'   => $service->price,
                    'quantity'     => $detail['quantity'],
                    'subtotal'     => $subtotal,
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
            $paid = $transaction
                ->transactionPayments()
                ->sum('amount');

            $transaction->status = $this->calculateStatus(
                $transaction->amount,
                $paid
            );

            $transaction->save();

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

    private function calculateSubtotal(Service $service, int $quantity)
    {
        return $service->price * $quantity;
    }

    private function calculateStatus(
        float $amount,
        float $paid
    ) {
        if ($paid <= 0) {
            return 'pending';
        }

        if ($paid >= $amount) {
            return 'paid';
        }

        return 'partially_paid';
    }
}

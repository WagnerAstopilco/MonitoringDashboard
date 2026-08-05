<?php

namespace App\Http\Controllers;

use App\Models\TransactionPayment;
use App\Http\Resources\TransactionPaymentResource;
use App\Http\Requests\StoreTransactionPaymentRequest;
use App\Http\Requests\UpdateTransactionPaymentRequest;

class TransactionPaymentController extends Controller
{
    public function index()
    {
        $transactionPayments = TransactionPayment::with('paymentMethod', 'transaction')->get();
        return TransactionPaymentResource::collection($transactionPayments);
    }

    public function store(StoreTransactionPaymentRequest $request)
    {
        $validatedData = $request->validated();
        $transactionPayment = TransactionPayment::create($validatedData);
        return new TransactionPaymentResource($transactionPayment);
    }
    
    public function show(TransactionPayment $transactionPayment)
    {
        $transactionPayment->load('paymentMethod', 'transaction');
        return new TransactionPaymentResource($transactionPayment);
    }

    public function update(UpdateTransactionPaymentRequest $request, TransactionPayment $transactionPayment)
    {
        $validatedData = $request->validated();
        $transactionPayment->update($validatedData);
        return new TransactionPaymentResource($transactionPayment);
    }

    public function destroy(TransactionPayment $transactionPayment)
    {
        $transactionPayment->delete();
        return response()->json(['message'=> 'Pago eliminado correctamente'], 200);
    }
}


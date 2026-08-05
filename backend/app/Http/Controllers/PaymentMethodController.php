<?php

namespace App\Http\Controllers;


use App\Models\PaymentMethod;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;


class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::get();
        return PaymentMethodResource::collection($paymentMethods);
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $validatedData = $request->validated();
        $paymentMethod = PaymentMethod::create($validatedData);
        return new PaymentMethodResource($paymentMethod);
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return new PaymentMethodResource($paymentMethod);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $validatedData = $request->validated();
        $paymentMethod->update($validatedData);
        return new PaymentMethodResource($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return response()->json(['message'=> 'Medio de pago eliminado correctamente'], 200);
    }
}

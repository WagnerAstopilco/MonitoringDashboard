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
        return (new PaymentMethodResource($paymentMethod))->response()->setStatusCode(201);
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
        if ($paymentMethod->transactions()->exists()) {
            return response()->json(['message' => 'No se puede eliminar el medio de pago porque tiene transacciones asociadas'], 400);
        }
        $paymentMethod->delete();
        return response()->json(['message'=> 'Medio de pago eliminado correctamente'], 200);
    
    }
}

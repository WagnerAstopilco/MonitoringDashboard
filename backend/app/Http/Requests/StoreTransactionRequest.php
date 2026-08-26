<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id',],
            'promotion_id' => ['nullable','exists:promotions,id',],
            'transaction_date' => ['required','date',],
            'transaction_type' => ['required','in:income,expense',],
            'annotations'=>['nullable','string'],
            'amount'=>['nullable','numeric','min:0'],
            'profit'=>['nullable','numeric','min:0'],
            'responsible' => ['required','in:edgar,jorge',],
            'delivery_date' => ['nullable','date','after_or_equal:transaction_date',],
            'status' => ['sometimes', 'in:paid, pending, partially_paid'],
            'delivery_status'=>['sometimes','boolean'],


            'details' => ['nullable','array'],
            'details.*.service_id' => ['required','exists:services,id',],
            'details.*.promotion_id' => ['nullable','exists:promotions,id',],
            'details.*.unit_price' => ['required','numeric','min:0',],
            'details.*.quantity' => ['required','integer','min:1',],



            'payments' => ['nullable','array',],
            'payments.*.payment_method_id' => ['required','exists:payment_methods,id',],
            'payments.*.amount' => ['required','numeric','min:0',],
            'payments.*.payment_type' => ['required','in:advance,full,balance',],
            'payments.*.payment_date' => ['required','date',],
        ];
    }
}

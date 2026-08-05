<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
            'responsible' => ['required','in:edgar,jorge',],
            'delivery_date' => ['required','date','after_or_equal:transaction_date',],

            'details' => ['required','array','min:1',],
            'details.*.id' => ['nullable','exists:transaction_details,id',],
            'details.*.service_id' => ['required','exists:services,id',],
            'details.*.promotion_id' => ['nullable','exists:promotions,id',],
            'details.*.unit_price' => ['required','numeric','min:0',],
            'details.*.quantity' => ['required','integer','min:1',],
        ];
    }
}

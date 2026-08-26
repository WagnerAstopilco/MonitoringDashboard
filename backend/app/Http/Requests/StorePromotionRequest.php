<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
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
            'name'=> ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string', 'max:255'],
            'discount_type'=> ['required', 'in:percentage,fixed'],
            'discount_value'=> ['required', 'numeric', 'min:0'],
            'start_date'=> ['required', 'date'],
            'end_date'=> ['required', 'date', 'after_or_equal:start_date'],
            'status'=> ['nullable', 'in:active,inactive'],
            'promotion_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:4096'],
            
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'name'=> ['sometimes', 'string', 'max:255'],
            'description'=> ['sometimes', 'string', 'max:255'],
            'price'=> ['sometimes', 'numeric', 'min:0'],
            'cost'=> ['sometimes', 'numeric', 'min:0'],
            'profit'=> ['sometimes', 'numeric', 'min:0'],
            'status'=> ['sometimes', 'in:active,inactive'],
            'service_image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

            'promotions' => ['nullable', 'array'],
            'promotions.*' => ['exists:promotions,id'],
        ];
    }
}

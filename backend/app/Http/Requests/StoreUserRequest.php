<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'digits:8', 'unique:users,dni'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['sometimes', Password::default()],
            'role' => ['required', 'in:admin,employee,visit'],
            'status' => ['sometimes', 'in:active,inactive'],
            'must_change_password' => ['sometimes', 'boolean'],
        ];
    }
}

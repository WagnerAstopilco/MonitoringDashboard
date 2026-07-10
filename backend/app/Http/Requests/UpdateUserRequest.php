<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'digits:8', Rule::unique('users', 'dni')->ignore($user->id)],
            'password' => ['nullable', Password::default(), 'confirmed'],
            'role' => ['required', 'in:admin,employee,visit'],
            'status' => ['required', 'in:active,inactive'],
            'must_change_password' => ['required', 'boolean'],
        ];
    }
}

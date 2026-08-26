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
            'name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'dni' => ['sometimes', 'digits:8', Rule::unique('users', 'dni')->ignore($user->id)],
            'username' => ['sometimes', 'string', 'max:50', 'unique:users,username'],
            'password' => ['nullable', Password::default()],
            'role' => ['sometimes', 'in:admin,employee,visit'],
            'status' => ['sometimes', 'in:active,inactive'],
            'must_change_password' => ['sometimes', 'boolean'],
        ];
    }
}

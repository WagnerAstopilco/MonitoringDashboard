<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo requiere estar autenticado (el middleware de la ruta ya lo exige),
        // no depende de ningún parámetro de ruta.
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            // 'confirmed' exige que venga también 'password_confirmation'
            // con el mismo valor, si no, falla la validación automáticamente.
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
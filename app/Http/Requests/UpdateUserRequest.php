<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'], // OPCIONAL en editar
            'tipo_persona' => ['required', 'string', 'in:cliente,empleado,repartidor'],
            'id_persona' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'tipo_persona.required' => 'Debe seleccionar un tipo de persona.',
            'tipo_persona.in' => 'El tipo de persona debe ser cliente, empleado o repartidor.',
            'id_persona.required' => 'Debe seleccionar una persona específica.',
            'id_persona.integer' => 'El ID de persona debe ser un número válido.',
        ];
    }
}
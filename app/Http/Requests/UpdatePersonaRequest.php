<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonaRequest extends FormRequest
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
        $method = $this->method();
        if ($method == 'PUT') {
            return [
                // 'nombre' => ['required'],
                // 'paterno' => ['required'],
                // 'telefono' => ['required'],
                // 'correo' => ['required'],
            ];
        } else {
            return [
                'nombre' => ['sometimes', 'required'],
                'paterno' => ['sometimes', 'required'],
                'telefono' => ['sometimes', 'required'],
                'correo' => ['sometimes', 'required'],
            ];
        }
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoMenuRequest extends FormRequest
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
        //PUT = Le tenemos que darle todos los datos
        //PATCH = Le podemos dar algunos valores, no oblitario todos
        $method = $this->method();
        if ($method == 'PUT') {
            return [
                'nombre' => ['required'],
                // En caso que haya mas campos ponerlos aquí
            ];
        } else {
            return [
                'nombre' => ['sometimes', 'required'], //Sometime = a veces, en caso que no lo manden el campo
                // En caso que haya mas campos ponerlos aquí
            ];
        }
    }
}

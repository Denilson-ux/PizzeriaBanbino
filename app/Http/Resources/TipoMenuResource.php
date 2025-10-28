<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // Obtener todos los atributos
        $allAttributes = $this->resource->toArray();

        // Agregar manualmente otros campos que se desea incluir
        $additionalFields = [
            'extra_field' => 'value_extra',
        ];

        // Combinar ambos conjuntos de campos
        $mergedFields = array_merge($allAttributes, $additionalFields);
        return $mergedFields;
    }
}

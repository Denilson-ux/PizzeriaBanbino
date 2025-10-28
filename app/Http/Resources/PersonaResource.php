<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $allAttributes = $this->resource->toArray();

        $additionalFields = [
            // 'extra_field' => 'value_extra',
        ];

        $mergedFields = array_merge($allAttributes, $additionalFields);
        return $mergedFields;
    }
}

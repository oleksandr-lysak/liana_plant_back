<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $masters
 */
class SpecialityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => __('data.specialities.' . $this->name),
            'masters_count' => $this->masters->count(),
        ];
    }
}

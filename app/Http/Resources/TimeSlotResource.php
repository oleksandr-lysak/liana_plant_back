<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeSlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'is_booked' => $this->is_booked,
            'client_name' => $this->client_name,
            'source' => $this->source,
            'duration' => $this->duration,
            'client_phone' => $this->client_phone,
            'service' => [
                'id'=>$this->id,
                'name'=>__('data.specialities.' . $this->service->name),
            ],
        ];
    }
}

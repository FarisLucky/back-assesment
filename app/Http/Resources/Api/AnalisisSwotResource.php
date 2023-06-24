<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AnalisisSwotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'id_pk' => $this->id_pk,
            'kelebihan' => $this->kelebihan,
            'kekurangan' => $this->kekurangan,
            'kesempatan' => $this->kesempatan,
            'ancaman' => $this->ancaman,
        ];
    }
}

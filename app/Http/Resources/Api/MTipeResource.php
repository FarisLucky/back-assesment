<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MTipeResource extends JsonResource
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
            'nama' => $this->nama,
            'tipe' => $this->tipe,
            'check' => $this->check,
            'relationship' => [
                'm_penilaian' => MPenilaianResource::collection($this->whenLoaded('penilaian')),
            ]
        ];
    }
}

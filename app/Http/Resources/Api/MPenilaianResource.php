<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MPenilaianResource extends JsonResource
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
            'id_tipe' => $this->id_tipe,
            'id_tipe' => $this->id_tipe,
            'sub_count' => $this->sub_count,
            'relationship' => [
                'tipe_penilaian' => new MTipeResource($this->whenLoaded('mTipe')),
                'sub_penilaian' => MSubPenilaianResource::collection($this->whenLoaded('subPenilaian'))
            ]
        ];
    }
}

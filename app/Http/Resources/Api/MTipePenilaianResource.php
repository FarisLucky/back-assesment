<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\MTipeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MTipePenilaianResource extends JsonResource
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
            'id_tipe' => $this->id_tipe,
            'id_jabatan' => $this->id_jabatan,
            'tipe_nama' => $this->tipe_nama,
            'relationship' => [
                'jabatan' => new JabatanResource($this->whenLoaded('jabatan')),
                'tipe_penilaian_by_tipe' => MTipePenilaianResource::collection($this->whenLoaded('tipePenilaianByTipe')),
            ]
        ];
    }
}

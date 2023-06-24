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
            'level' => $this->level,
            'relationship' => [
                'jabatan' => new JabatanResource($this->whenLoaded('jabatan')),
                'sub_penilaian' => MSubPenilaianResource::collection($this->whenLoaded('subPenilaian'))
            ]
        ];
    }
}

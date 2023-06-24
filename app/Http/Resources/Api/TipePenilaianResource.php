<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class TipePenilaianResource extends JsonResource
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
            'id_detail' => $this->id_detail,
            'nama_tipe' => $this->nama_tipe,
            'tipe_pk' => $this->tipe_pk,
            'catatan' => $this->catatan,
            'catatan' => $this->catatan,
            'updated_by' => $this->updated_by,
            'relationship' => [
                'sub' => SubPenilaianKaryawanResource::collection($this->whenLoaded('subPenilaian')),
                'detail' => DetailPenilaianResource::collection($this->whenLoaded('detail'))
            ],
        ];
    }
}

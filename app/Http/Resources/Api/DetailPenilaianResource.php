<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailPenilaianResource extends JsonResource
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
            'id_tipe_pk' => $this->id_tipe_pk,
            'nama_penilaian' => $this->nama_penilaian,
            'ttl_nilai' => $this->ttl_nilai,
            'rata_nilai' => $this->rata_nilai_desc,
            'id_penilai' => $this->id_penilai,
            'nama_penilai' => $this->nama_penilai,
            'jabatan_penilai' => $this->jabatan_penilai,
            'catatan' => $this->catatan,
            'bobot' => $this->bobot,
            'updated_by' => $this->updated_by,
            'relationship' => [
                'sub' => SubPenilaianKaryawanResource::collection($this->whenLoaded('subPenilaian'))
            ],
        ];
    }
}

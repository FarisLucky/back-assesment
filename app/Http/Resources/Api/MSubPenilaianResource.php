<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MSubPenilaianResource extends JsonResource
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
            'id_penilaian' => $this->id_penilaian,
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'kategori_desc' => $this->kategoriDesc,
            'id_jabatan_penilai' => $this->id_jabatan_penilai,
            'id_jabatan_kinerja' => $this->id_jabatan_kinerja,
            'id_unit_penilai' => $this->id_unit_penilai,
            'id_parent' => $this->id_parent,
            'penilaian_nama' => $this->penilaian_nama,
            'penilaian_tipe' => $this->penilaian_tipe,
            'relationship' => [
                'penilaian' => new MPenilaianResource($this->whenLoaded('penilaian')),
                'jabatan_penilai' => new JabatanResource($this->whenLoaded('jabatanPenilai')),
                'jabatan_kinerja' => new JabatanResource($this->whenLoaded('jabatanKinerja')),
                'unit_penilai' => new UnitResource($this->whenLoaded('unitPenilai')),
            ]
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PenilaianKaryawanResource extends JsonResource
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
            'id_karyawan' => $this->id_karyawan,
            'nama_karyawan' => $this->nama_karyawan,
            'jabatan' => $this->jabatan,
            'id_penilai' => $this->id_penilai,
            'nama_penilai' => $this->nama_penilai,
            'jabatan_penilai' => $this->jabatan_penilai,
            'tgl_nilai' => $this->tgl_nilai->isoFormat('L'),
            'ttl_nilai' => $this->ttl_nilai,
            'rata_nilai' => $this->rata_nilai,
            'tipe' => $this->tipe,
            'status' => $this->status,
            'validasi_by' => $this->validasi_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'relationship' => [
                'tipe_penilaian' => DetailPenilaianResource::collection($this->whenLoaded('detail')),
                'detail' => DetailPenilaianResource::collection($this->whenLoaded('detail'))
            ]
        ];
    }
}
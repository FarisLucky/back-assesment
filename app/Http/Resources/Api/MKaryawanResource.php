<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MKaryawanResource extends JsonResource
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
            'nip' => $this->nip,
            'nama' => $this->nama,
            'sex' => $this->sex,
            'alamat' => $this->alamat,
            'pendidikan' => $this->pendidikan,
            'tgl_lulus' => $this->tgl_lulus,
            'status' => $this->status,
            'id_jabatan' => $this->id_jabatan,
            'id_unit' => $this->id_unit,
            'jabatan_nama' => $this->jabatan_nama,
            'relationship' => [
                'jabatan' => new JabatanResource($this->whenLoaded('jabatan')),
                'penilaian_karyawan' => PenilaianKaryawanResource::collection($this->whenLoaded('penilaianKaryawan')),
                'pk_umum' => PenilaianKaryawanResource::collection($this->whenLoaded('penilaianKaryawanUmum')),
                'pk_khusus' => PenilaianKaryawanResource::collection($this->whenLoaded('penilaianKaryawanKhusus')),
            ]
        ];
    }
}

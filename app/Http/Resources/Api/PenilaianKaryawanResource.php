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
            'ttl_nilai' => $this->ttl_nilai_desc,
            'rata_nilai' => $this->rata_nilai_desc,
            'tipe' => $this->tipe,
            'status' => $this->status,
            'kategori' => $this->kategori,
            'validasi_by' => $this->validasi_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => optional($this->created_at)->isoFormat('L'),
            'updated_at' => optional($this->updated_at)->isoFormat('L'),
            'relationship' => [
                'tipe_penilaian' => TipePenilaianResource::collection($this->whenLoaded('tipePenilaian')),
                'analisis_swot' => new AnalisisSwotResource($this->whenLoaded('analisisSwot')),
                'karyawan' => new MKaryawanResource($this->whenLoaded('karyawan')),
                'comment' => new CommentResource($this->whenLoaded('comment')),
            ]
        ];
    }
}

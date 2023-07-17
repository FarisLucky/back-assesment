<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MValidPenilaiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_sub' => $this->id_sub,
            'id_jabatan_penilai' => $this->id_jabatan_penilai,
            'relationship' => [
                'm_sub_penilaian' => new MSubPenilaianResource($this->whenLoaded('mSubPenilaian')),
                'jabatan_penilai' => new JabatanResource($this->whenLoaded('jabatanPenilai')),
            ]
        ];
    }
}

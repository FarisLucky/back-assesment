<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class JabatanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $relationship = $this->jabatan != null ?  $this->jabatan->nama . ' > ' : '';
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nama_with_parent' =>  $relationship . $this->nama,
            'level' =>  $this->level,
            'id_parent' => $this->id_parent,
            'relationship' => [
                'parent' => new JabatanResource($this->whenLoaded('parent'))
            ],
            'created_at' => optional($this->created_at)->isoFormat('L'),
            'updated_at' => optional($this->updated_at)->isoFormat('L'),
        ];
    }
}

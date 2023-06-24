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
                'jabatan' => new JabatanResource($this->whenLoaded('jabatan'))
            ]
        ];
    }
}

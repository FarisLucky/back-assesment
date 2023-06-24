<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetailPenilaianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_pk' => ['required', 'exists:penilaian_karyawan,id'],
            'nama_penilaian' => ['required'],
            'ttl_nilai' => ['required'],
            'rata_nilai' => ['required'],
            'id_penilai' => ['required', 'exists:m_karyawan,id'],
            'nama_penilai' => ['required'],
            'jabatan_penilai' => ['required'],
            'catatan' => ['required'],
            'updated_by' => ['required'],
        ];
    }
}

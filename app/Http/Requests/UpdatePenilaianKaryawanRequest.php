<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenilaianKaryawanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_karyawan' => ['required', 'exists:m_karyawan,id'],
            'nama_karyawan' => ['required'],
            'jabatan' => ['required'],
            'id_penilai' => ['required', 'exists:m_karyawan,id'],
            'nama_penilai' => ['required'],
            'jabatan_penilai' => ['required'],
            'tgl_nilai' => ['required'],
            'ttl_nilai' => ['required'],
            'rata_nilai' => ['required'],
            'tipe' => ['required'],
            'status' => ['required'],
            'validasi_by' => ['required'],
            'created_by' => ['required'],
            'updated_by' => ['required'],
        ];
    }
}

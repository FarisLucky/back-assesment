<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMSubPenilaianRequest extends FormRequest
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
            'nama' => ['required'],
            'id_penilaian' => ['required', 'exists:m_penilaian,id'],
            'id_jabatan_penilai' => ['required', 'exists:m_jabatan,id'],
            'id_jabatan_kinerja' => ['required', 'exists:m_jabatan,id'],
            'id_unit_penilai' => ['required', 'exists:m_unit,id'],
            'id_parent' => ['exists:m_sub_penilaian,id'],
        ];
    }
}

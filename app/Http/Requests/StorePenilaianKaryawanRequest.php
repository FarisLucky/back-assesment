<?php

namespace App\Http\Requests;

use App\Models\MPenilaian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenilaianKaryawanRequest extends FormRequest
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
            'id_karyawan' => ['required', 'exists:m_karyawan,id'],
            'penilaians' => ['required'],
            'tipe' => ['required', Rule::in(MPenilaian::TIPE)],
            'bulan_nilai' => ['required'],
            'tahun_nilai' => ['required'],
        ];
    }
}

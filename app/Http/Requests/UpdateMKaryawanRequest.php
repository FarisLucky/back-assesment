<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMKaryawanRequest extends FormRequest
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
            'nip' => ['required'],
            'nama' => ['required'],
            'sex' => ['required'],
            'tgl_lahir' => ['required'],
            'alamat' => ['required'],
            'pendidikan' => ['required'],
            'tgl_lulus' => ['required'],
            'status' => ['required'],
            'id_jabatan' => ['required'],
            'id_unit' => ['required'],
        ];
    }
}

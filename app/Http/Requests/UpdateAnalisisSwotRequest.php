<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnalisisSwotRequest extends FormRequest
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
            'id_pk' => ['required'],
            'kelebihan' => ['required'],
            'kekurangan' => ['required'],
            'kesempatan' => ['required'],
            'ancaman' => ['required'],
        ];
    }
}

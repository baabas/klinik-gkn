<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LokasiValidationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_lokasi' => ['required', 'in:1,2'],
            // rules lainnya
        ];
    }

    public function messages()
    {
        return [
            'id_lokasi.in' => 'Lokasi yang dipilih tidak valid. Hanya bisa memilih GKN 1 atau GKN 2.',
        ];
    }
}
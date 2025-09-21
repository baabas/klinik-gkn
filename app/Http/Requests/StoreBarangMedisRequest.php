<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBarangMedisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_obat' => ['required', 'string'],
            'tipe' => ['required', Rule::in(['OBAT', 'ALKES'])],
            'satuan_dasar' => ['required', 'string'],
            'kemasan' => ['required', 'array', 'min:1'],
            'kemasan.*.nama_kemasan' => ['required', 'string', Rule::in(['Box', 'Strip', 'Botol', 'Rol', 'Pcs']), 'distinct'],
            'kemasan.*.isi_per_kemasan' => ['required', 'integer', 'min:1'],
            'kemasan.*.is_default' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_obat.required' => 'Nama barang wajib diisi.',
            'tipe.required' => 'Tipe barang wajib dipilih.',
            'tipe.in' => 'Tipe barang tidak valid.',
            'satuan_dasar.required' => 'Satuan dasar wajib diisi.',
            'kemasan.required' => 'Definisi kemasan minimal harus ada satu.',
            'kemasan.array' => 'Format definisi kemasan tidak valid.',
            'kemasan.min' => 'Minimal satu kemasan harus diisi.',
            'kemasan.*.nama_kemasan.required' => 'Nama kemasan wajib dipilih.',
            'kemasan.*.nama_kemasan.in' => 'Nama kemasan tidak valid.',
            'kemasan.*.nama_kemasan.distinct' => 'Nama kemasan tidak boleh sama dalam satu barang.',
            'kemasan.*.isi_per_kemasan.required' => 'Isi per kemasan wajib diisi.',
            'kemasan.*.isi_per_kemasan.integer' => 'Isi per kemasan harus berupa angka.',
            'kemasan.*.isi_per_kemasan.min' => 'Isi per kemasan minimal 1.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $kemasan = collect($this->input('kemasan', []));
            $defaultCount = $kemasan->filter(function ($item) {
                return !empty($item['is_default']);
            })->count();

            if ($defaultCount !== 1) {
                $validator->errors()->add('kemasan', 'Tepat satu kemasan harus ditandai sebagai default.');
            }
        });
    }
}

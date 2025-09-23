<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
            'satuan_dasar' => ['required', Rule::in(['kaplet', 'tablet', 'kapsul', 'pcs'])],
            'kemasan' => ['required', 'array', 'min:1'],
            'kemasan.*.nama_kemasan' => ['required', 'string', 'max:100'],
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
            'satuan_dasar.in' => 'Satuan dasar hanya boleh kaplet, tablet, kapsul, atau pcs.',
            'kemasan.*.isi_per_kemasan.required' => 'Isi per kemasan wajib diisi.',
            'kemasan.*.isi_per_kemasan.integer' => 'Isi per kemasan harus berupa angka.',
            'kemasan.*.isi_per_kemasan.min' => 'Isi per kemasan minimal 1.',
        ];
    }

    public function withValidator($validator): void
    {
        $kemasanPayload = request('kemasan', []);

        $validator->after(function ($validator) use ($kemasanPayload) {
            $kemasan = collect($kemasanPayload);
            $defaultCount = $kemasan->filter(function ($item) {
                return ! empty($item['is_default']);
            })->count();

            if ($defaultCount !== 1) {
                $validator->errors()->add('kemasan', 'Tepat satu kemasan harus ditandai sebagai default.');
            }

            $duplicates = $kemasan
                ->map(fn ($item) => Str::lower(trim($item['nama_kemasan'] ?? '')))
                ->filter()
                ->duplicates();

            if ($duplicates->isNotEmpty()) {
                $validator->errors()->add('kemasan', 'Nama kemasan tidak boleh duplikat.');
            }
        });
    }
}

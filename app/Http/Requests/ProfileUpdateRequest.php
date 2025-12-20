<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\NonKaryawan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        
        return [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
                Rule::unique(Karyawan::class, 'email')->ignore($user->nip, 'nip'),
            ],
            'no_hp' => [
                'nullable', 
                'string', 
                'max:20',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value) {
                        // Cek di tabel karyawan (kecuali user sendiri)
                        $existsInKaryawan = Karyawan::where('no_hp', $value)
                            ->where('nip', '!=', $user->nip)
                            ->exists();
                        
                        // Cek di tabel non_karyawan
                        $existsInNonKaryawan = NonKaryawan::where('no_hp', $value)->exists();
                        
                        if ($existsInKaryawan || $existsInNonKaryawan) {
                            $fail('Nomor HP ini sudah terdaftar oleh user lain.');
                        }
                    }
                },
            ],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'alergi' => ['nullable', 'string', 'max:500'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah digunakan oleh user lain.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // [DIKEMBALIKAN] Validasi untuk NIP dengan Max Length Validation
        return [
            'nip' => ['required', 'string', 'digits:18', 'max:18'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
            'nip.max' => 'NIP terlalu panjang (maksimal 18 karakter).',
            'password.required' => 'Password wajib diisi.',
            'password.max' => 'Password terlalu panjang (maksimal 255 karakter).',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // [DIKEMBALIKAN] Coba login menggunakan NIP dan password
        if (! Auth::attempt($this->only('nip', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nip' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        // [DIKEMBALIKAN] Throttle key berdasarkan NIP
        return Str::transliterate(Str::lower($this->input('nip')).'|'.$this->ip());
    }
    
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}
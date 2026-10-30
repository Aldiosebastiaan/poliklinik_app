<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'different:current_password'],
            'password_confirmation' => ['required_with:password', 'same:password'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai dengan akun Anda.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru harus terdiri dari minimal 8 karakter.',
            'password.different' => 'Password baru harus berbeda dari password lama.',
            'password_confirmation.required_with' => 'Konfirmasi password baru wajib diisi.',
            'password_confirmation.same' => 'Konfirmasi password baru tidak cocok.',
        ];
    }
}

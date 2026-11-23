<?php

namespace App\Http\Requests\Stok;

use App\Models\User;
use App\Services\StokService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && in_array($user->role, [User::ROLE_ADMIN, User::ROLE_KARYAWAN], true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mode' => ['required', 'in:set,tambah,kurang'],
            'nilai' => ['required', 'numeric', 'min:0'],
            'alasan' => ['required', 'string', 'max:120', Rule::in(StokService::alasanOptions())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nilai.required' => 'Jumlah stok wajib diisi.',
            'nilai.numeric' => 'Jumlah stok harus berupa angka.',
            'nilai.min' => 'Jumlah stok tidak boleh negatif.',
            'alasan.required' => 'Alasan pembaruan stok wajib dipilih.',
            'alasan.in' => 'Alasan pembaruan stok tidak valid.',
        ];
    }
}

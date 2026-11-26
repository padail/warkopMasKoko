<?php

namespace App\Http\Requests\Stok;

use App\Models\User;
use App\Services\StokService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuBarangRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'string', 'max:50'],
            'harga_jual' => ['nullable', 'integer', 'min:0'],
            'stok' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
            'is_dijual' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'alasan' => ['nullable', 'string', 'max:120', Rule::in(StokService::alasanOptions())],
        ];
    }
}

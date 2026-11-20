<?php

namespace App\Http\Requests\Kasir;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProsesPembayaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'metode_pembayaran' => ['required', 'in:tunai,qris,gopay'],
            'nominal_diterima' => ['nullable', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_barang_id' => ['required', 'integer', 'exists:menu_barang,id'],
            'items.*.jumlah' => ['required', 'numeric', 'min:0.001'],
        ];
    }
}

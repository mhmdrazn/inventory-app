<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'borrower_name' => ['required', 'string', 'max:255'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after:borrowed_at'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'borrower_name' => 'nama peminjam',
            'borrowed_at' => 'tanggal pinjam',
            'due_at' => 'tanggal jatuh tempo',
            'notes' => 'catatan',
            'items' => 'daftar barang',
            'items.*.product_id' => 'barang',
            'items.*.quantity' => 'jumlah',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'due_at.after' => 'Tanggal jatuh tempo harus setelah tanggal pinjam.',
            'items.required' => 'Minimal harus ada satu barang yang dipinjam.',
            'items.min' => 'Minimal harus ada satu barang yang dipinjam.',
        ];
    }
}

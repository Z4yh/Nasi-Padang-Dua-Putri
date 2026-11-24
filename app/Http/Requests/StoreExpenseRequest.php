<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => [
                'required',
                'string',
                Rule::in(Expense::CATEGORIES),
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:1000000000',
            ],
            'note' => [
                'nullable',
                'string',
                'max:200',
                'required_if:category,lain_lain',
            ],
            'fund_source' => [
                'required',
                'string',
                Rule::in(Expense::FUND_SOURCES),
            ],
            'occurred_at' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
        ];
    }

    /**
     * Pesan kesalahan khusus dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Kategori pengeluaran wajib dipilih.',
            'category.in' => 'Kategori pengeluaran tidak valid.',
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.integer' => 'Nominal pengeluaran harus berupa bilangan bulat rupiah tanpa desimal.',
            'amount.min' => 'Nominal pengeluaran harus lebih dari 0.',
            'amount.max' => 'Nominal pengeluaran melebihi batas wajar (maksimal Rp 1.000.000.000).',
            'note.required_if' => 'Catatan rincian belanja wajib diisi untuk kategori Lain-lain.',
            'note.max' => 'Catatan rincian belanja maksimal 200 karakter.',
            'fund_source.required' => 'Sumber dana pengeluaran wajib dipilih.',
            'fund_source.in' => 'Sumber dana pengeluaran tidak valid.',
            'occurred_at.date' => 'Format waktu pengeluaran tidak valid.',
            'occurred_at.before_or_equal' => 'Waktu pengeluaran tidak boleh berada di masa depan.',
        ];
    }
}

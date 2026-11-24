<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateExpenseRequest extends FormRequest
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
                'sometimes',
                'string',
                Rule::in(Expense::CATEGORIES),
            ],
            'amount' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000000000',
            ],
            'note' => [
                'nullable',
                'string',
                'max:200',
            ],
            'fund_source' => [
                'sometimes',
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
     * Validasi tambahan setelah aturan dasar.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $expenseId = $this->route('id');
            $existingExpense = Expense::whereNull('deleted_at')->find($expenseId);

            if (! $existingExpense) {
                return;
            }

            $targetCategory = $this->input('category', $existingExpense->category);
            $targetNote = $this->has('note') ? $this->input('note') : $existingExpense->note;

            if ($targetCategory === 'lain_lain' && empty(trim((string) $targetNote))) {
                $v->errors()->add('note', 'Catatan rincian belanja wajib diisi untuk kategori Lain-lain.');
            }
        });
    }

    /**
     * Pesan kesalahan khusus dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'category.in' => 'Kategori pengeluaran tidak valid.',
            'amount.integer' => 'Nominal pengeluaran harus berupa bilangan bulat rupiah tanpa desimal.',
            'amount.min' => 'Nominal pengeluaran harus lebih dari 0.',
            'amount.max' => 'Nominal pengeluaran melebihi batas wajar (maksimal Rp 1.000.000.000).',
            'note.max' => 'Catatan rincian belanja maksimal 200 karakter.',
            'fund_source.in' => 'Sumber dana pengeluaran tidak valid.',
            'occurred_at.date' => 'Format waktu pengeluaran tidak valid.',
            'occurred_at.before_or_equal' => 'Waktu pengeluaran tidak boleh berada di masa depan.',
        ];
    }
}

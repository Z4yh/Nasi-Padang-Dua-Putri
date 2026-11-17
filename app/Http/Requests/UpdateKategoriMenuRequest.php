<?php

namespace App\Http\Requests;

use App\Models\KategoriMenu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kategori = $this->route('kategori_menu');
        $kategoriId = $kategori instanceof KategoriMenu ? $kategori->id_kategori_menu : $kategori;

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tp_md_kategori_menu', 'nama_kategori')->ignore($kategoriId, 'id_kategori_menu'),
            ],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Custom message validation in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori menu wajib diisi.',
            'nama_kategori.string'   => 'Nama kategori menu harus berupa teks.',
            'nama_kategori.max'      => 'Nama kategori menu tidak boleh lebih dari 255 karakter.',
            'nama_kategori.unique'   => 'Nama kategori menu sudah digunakan, silakan gunakan nama lain.',
            'urutan.integer'         => 'Urutan tampilan harus berupa angka bilangan bulat.',
            'urutan.min'             => 'Urutan tampilan tidak boleh bernilai negatif (minimal 0).',
        ];
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_kategori' => 'Nama Kategori',
            'urutan'        => 'Urutan Tampilan',
        ];
    }
}

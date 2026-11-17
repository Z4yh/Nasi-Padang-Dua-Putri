<?php

namespace App\Http\Requests;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
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
        $menu = $this->route('menu');
        $menuId = $menu instanceof Menu ? $menu->id_menu : $menu;

        return [
            'id_kategori_menu' => ['required', 'exists:tigaputri_md_kategori_menu,id_kategori_menu'],
            'nama_menu'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('tigaputri_md_menu', 'nama_menu')->ignore($menuId, 'id_menu'),
            ],
            'harga'       => ['required', 'numeric', 'min:0'],
            'deskripsi'   => ['nullable', 'string'],
            'status_stok' => ['required', 'in:tersedia,habis'],
            'foto_menu'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
            'id_kategori_menu.required' => 'Kategori menu wajib dipilih.',
            'id_kategori_menu.exists'   => 'Kategori menu yang dipilih tidak valid atau tidak ditemukan.',
            'nama_menu.required'        => 'Nama menu masakan wajib diisi.',
            'nama_menu.string'          => 'Nama menu masakan harus berupa teks.',
            'nama_menu.max'             => 'Nama menu masakan tidak boleh lebih dari 255 karakter.',
            'nama_menu.unique'          => 'Nama menu masakan sudah terdaftar, silakan gunakan nama lain.',
            'harga.required'            => 'Harga menu wajib diisi.',
            'harga.numeric'             => 'Harga menu harus berupa angka nominal yang valid.',
            'harga.min'                 => 'Harga menu tidak boleh bernilai negatif (minimal Rp 0).',
            'deskripsi.string'          => 'Deskripsi menu harus berupa teks.',
            'status_stok.required'      => 'Status ketersediaan stok menu wajib dipilih.',
            'status_stok.in'            => 'Status ketersediaan stok hanya boleh bernilai "tersedia" atau "habis".',
            'foto_menu.image'           => 'Berkas foto menu harus berupa file gambar.',
            'foto_menu.mimes'           => 'Format foto menu harus berformat: JPG, JPEG, PNG, atau WEBP.',
            'foto_menu.max'             => 'Ukuran foto menu maksimal adalah 2 MB (2048 KB).',
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
            'id_kategori_menu' => 'Kategori Menu',
            'nama_menu'        => 'Nama Menu',
            'harga'            => 'Harga Menu',
            'deskripsi'        => 'Deskripsi Menu',
            'status_stok'      => 'Status Stok',
            'foto_menu'        => 'Foto Menu',
        ];
    }
}

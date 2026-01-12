<?php

namespace App\Http\Requests;

class KategoriRequest extends BaseRequest
{
    public function rules(): array
    {
        $kategoriId = $this->route('kategori');
        
        return [
            'nama' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:tbl_kategori,slug,' . $kategoriId,
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.max' => 'Nama kategori maksimal 100 karakter',
            'slug.required' => 'Slug wajib diisi',
            'slug.unique' => 'Slug sudah digunakan',
        ];
    }
}

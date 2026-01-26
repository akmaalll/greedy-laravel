<?php

namespace App\Http\Requests;

class LayananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'kategori_id' => 'required|exists:tbl_kategori,id',
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:fotografi,videografi',
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih',
            'kategori_id.exists' => 'Kategori tidak valid',
            'nama.required' => 'Nama layanan wajib diisi',
            'tipe.required' => 'Tipe layanan wajib dipilih',
            'tipe.in' => 'Tipe layanan harus fotografi atau videografi',
        ];
    }
}

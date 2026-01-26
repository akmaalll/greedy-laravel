<?php

namespace App\Http\Requests;

class PaketLayananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'layanan_id' => 'required|exists:tbl_layanan,id',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tipe_durasi' => 'required|in:jam,foto,menit',
            'nilai_durasi' => 'required|integer|min:1',
            'harga_dasar' => 'required|numeric|min:0',
            'harga_overtime' => 'nullable|numeric|min:0',
            'satuan_overtime' => 'nullable|in:jam,foto,menit',
            'fitur' => 'nullable|array',
            'is_aktif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'layanan_id.required' => 'Layanan wajib dipilih',
            'layanan_id.exists' => 'Layanan tidak valid',
            'nama.required' => 'Nama paket wajib diisi',
            'tipe_durasi.required' => 'Tipe durasi wajib dipilih',
            'nilai_durasi.required' => 'Nilai durasi wajib diisi',
            'nilai_durasi.min' => 'Nilai durasi minimal 1',
            'harga_dasar.required' => 'Harga dasar wajib diisi',
            'harga_dasar.min' => 'Harga dasar minimal 0',
        ];
    }
}

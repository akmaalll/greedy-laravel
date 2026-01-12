<?php

namespace App\Http\Requests;

class JadwalPesananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pesanan_id' => 'required|exists:tbl_pesanan,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => ['required', 'regex:/^([0-9]{2}:[0-9]{2})(:[0-9]{2})?$/'],
            'waktu_selesai' => ['required', 'regex:/^([0-9]{2}:[0-9]{2})(:[0-9]{2})?$/', 'after:waktu_mulai'],
            'kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }
}

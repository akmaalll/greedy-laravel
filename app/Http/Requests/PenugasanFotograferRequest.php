<?php

namespace App\Http\Requests;

class PenugasanFotograferRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pesanan_id' => 'required|exists:tbl_pesanan,id',
            'fotografer_id' => 'required|exists:users,id',
            'status' => 'required|in:ditugaskan,diterima,ditolak,selesai',
            'catatan' => 'nullable|string',
        ];
    }
}

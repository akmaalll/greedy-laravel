<?php

namespace App\Http\Requests;

class PembayaranRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pesanan_id' => 'required|exists:tbl_pesanan,id',
            'waktu_bayar' => 'nullable|date',
            'jumlah' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:transfer,tunai,ewallet',
            'status' => 'nullable|in:menunggu,lunas,gagal,dikembalikan',
            'bukti_gambar' => 'nullable|string',
            'catatan' => 'nullable|string',
        ];
    }
}

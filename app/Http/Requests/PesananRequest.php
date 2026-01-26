<?php

namespace App\Http\Requests;

class PesananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'tanggal_acara' => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'date',
                $this->isMethod('POST') ? 'after_or_equal:today' : ''
            ],
            'waktu_mulai_acara' => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'regex:/^([0-9]{2}:[0-9]{2})(:[0-9]{2})?$/'
            ],
            'lokasi_acara' => ($this->isMethod('POST') ? 'required' : 'sometimes') . '|string|max:255',
            'deskripsi_acara' => 'nullable|string',
            'status' => 'nullable|in:menunggu,dikonfirmasi,berlangsung,selesai,dibatalkan',
            'catatan' => 'nullable|string',
            'items' => $this->isMethod('POST') ? 'required|array|min:1' : 'nullable|array',
            'items.*.paket_layanan_id' => 'required|exists:tbl_paket_layanan,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_acara.required' => 'Tanggal acara wajib diisi',
            'tanggal_acara.after_or_equal' => 'Tanggal acara tidak boleh di masa lalu',
            'waktu_mulai_acara.required' => 'Waktu mulai acara wajib diisi',
            'lokasi_acara.required' => 'Lokasi acara wajib diisi',
            'items.required' => 'Minimal pilih satu paket layanan',
            'items.*.paket_layanan_id.exists' => 'Paket layanan tidak valid',
            'items.*.jumlah.min' => 'Jumlah minimal 1',
        ];
    }
}

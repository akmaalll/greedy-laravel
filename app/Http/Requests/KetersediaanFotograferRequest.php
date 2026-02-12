<?php

namespace App\Http\Requests;

class KetersediaanFotograferRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => ['required', 'regex:/^([0-9]{2}:[0-9]{2})(:[0-9]{2})?$/'],
            'waktu_selesai' => ['required', 'regex:/^([0-9]{2}:[0-9]{2})(:[0-9]{2})?$/', 'after:waktu_mulai'],
            'status' => 'required|in:tersedia,tidak_tersedia,dipesan',
            'catatan' => 'nullable|string',
        ];
    }
}

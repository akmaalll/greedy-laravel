<?php

namespace App\Http\Requests;

class RatingFotograferRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pesanan_id' => 'required|exists:tbl_pesanan,id',
            'fotografer_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string',
        ];
    }
}

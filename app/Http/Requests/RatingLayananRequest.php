<?php

namespace App\Http\Requests;

class RatingLayananRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pesanan_id' => 'required|exists:tbl_pesanan,id|unique:tbl_rating_layanan,pesanan_id',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RatingFotograferRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pesanan_id' => [
                'required',
                'exists:tbl_pesanan,id',
                Rule::unique('tbl_rating_fotografer')->where(function ($query) {
                    return $query->where('fotografer_id', $this->fotografer_id);
                }),
            ],
            'fotografer_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'pesanan_id.unique' => 'Anda sudah memberikan penilaian untuk fotografer ini pada pesanan ini.',
        ];
    }
}

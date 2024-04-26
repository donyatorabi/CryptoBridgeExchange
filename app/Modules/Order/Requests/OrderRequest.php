<?php

namespace App\Modules\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'src_coin_id' => ['required', 'exists:coins,id', 'integer', 'different:dest_coin_id'],
            'dest_coin_id' => ['required', 'exists:coins,id', 'integer', 'different:src_coin_id'],
            'price' => ['required', 'digits_between:1,10']
        ];
    }
}

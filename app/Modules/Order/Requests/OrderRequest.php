<?php

namespace App\Modules\Order\Requests;

use App\Requests\BaseRequest;

class OrderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'src_coin_id' => ['required', 'exists:coins,id', 'integer', 'different:dest_coin_id'],
            'dest_coin_id' => ['required', 'exists:coins,id', 'integer', 'different:src_coin_id'],
            'price' => ['required', 'digits_between:1,10'],
            'quantity' => ['nullable', 'integer'],
        ];
    }
}

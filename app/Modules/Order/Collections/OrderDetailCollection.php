<?php

namespace App\Modules\Order\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'srcCoin' => $this->srcCoin->name_en,
            'destCoin' => $this->destCoin->name_en,
            'quantity' => $this->order->quantity,
            'srcCoinPrice' => $this->order->src_coin_price,
            'destCoinPrice' => $this->order->dest_coin_price,
        ];
    }

}

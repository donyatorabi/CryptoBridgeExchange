<?php

namespace App\Modules\Order\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderDetailCollection extends ResourceCollection
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
            'srcCoin' => $this->collection->get('order')['src_coin']['name_en'],
            'srcCoinPrice' => $this->collection->get('order')['src_coin']['price'],
            'destCoin' => $this->collection->get('order')['dest_coin']['name_en'],
            'destCoinPrice' => $this->collection->get('order')['dest_coin']['price'],
            'quantity' => $this->collection->get('order')['quantity'],
            'email' => $this->collection->get('order')['user_email'],
        ];
    }
}

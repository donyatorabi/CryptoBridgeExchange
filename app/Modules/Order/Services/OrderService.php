<?php

namespace App\Modules\Order\Services;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\OrderRepository;
use JetBrains\PhpStorm\ArrayShape;

class OrderService
{
    public function __construct(readonly public OrderRepository $orderRepository)
    {
    }

    public function store(array $data)
    {
        $data = $this->_prepareDataForInsert($data);
        $this->orderRepository->create(data: $data);
    }

    #[ArrayShape(['src_coin_id' => "integer", 'dest_coin_id' => "integer", 'user_email' => "string", 'src_coin_price' => "integer"])] private function _prepareDataForInsert(array $data): array
    {
        return [
            'src_coin_id' => $data['src_coin_id'],
            'dest_coin_id' => $data['dest_coin_id'],
            'user_email' => $data['email'],
            'src_coin_price' => $data['price'],
        ];
    }
}

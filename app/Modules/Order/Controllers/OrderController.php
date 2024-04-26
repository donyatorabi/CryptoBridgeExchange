<?php

namespace App\Modules\Order\Controllers;

use App\Modules\Order\Requests\OrderRequest;
use App\Modules\Order\Services\OrderService;

class OrderController
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = app(OrderService::class);
    }

    public function store(OrderRequest $request)
    {

    }
}

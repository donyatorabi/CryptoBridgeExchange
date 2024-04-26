<?php

namespace App\Modules\Order\Controllers;

use App\DTOs\BaseResponseDto;
use App\Modules\Order\Requests\OrderRequest;
use App\Modules\Order\Services\OrderService;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = app(OrderService::class);
    }

    public function store(OrderRequest $request)
    {
        $this->orderService->store($request->all());

        return response()->json([
            'status' => BaseResponseDto::SUCCESS,
            'code' => Response::HTTP_OK,
        ]);
    }
}

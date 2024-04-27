<?php

namespace App\Modules\Order\Controllers;

use App\DTOs\BaseResponseDto;
use App\Modules\Order\Collections\OrderDetailCollection;
use App\Modules\Order\Requests\OrderRequest;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = app(OrderService::class);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $trackerId = $this->orderService->store($request->all());

        return response()->json([
            'status' => BaseResponseDto::SUCCESS,
            'code' => Response::HTTP_OK,
            'tracker_id' => $trackerId,
        ]);
    }

    public function get(string $trackerId)
    {
        $data = $this->orderService->getOrderByTrackerId(trackerId: $trackerId);

        return OrderDetailCollection::collection($data);
    }
}

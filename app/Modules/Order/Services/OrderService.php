<?php

namespace App\Modules\Order\Services;

use App\DTOs\BaseResponseDto;
use App\Modules\Acc\Services\TransactionService;
use App\Modules\Coin\Jobs\UpdateCoinsJob;
use App\Modules\Coin\Models\Coin;
use App\Modules\Order\Exceptions\ApiOrderErrorException;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\OrderRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class OrderService
{
    private TransactionService $transactionService;

    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = app(OrderRepository::class, ['model' => app(Order::class)]);
        $this->transactionService = app(TransactionService::class);
    }

    public function store(array $data): ?string
    {
        DB::beginTransaction();
        try {
            // here I am calculating destination coin price
            if (! Cache::has('coins')) {
                dispatch_sync(new UpdateCoinsJob());
            }

            $this->checkSrcCoinPrice($data['src_coin_id'], $data['price']);

            $destCoinPrice = $this->getDestCoinPrice($data['dest_coin_id']);

            // throw exception if destination code is not found in cache
            $this->checkDestCoinPrice($destCoinPrice);

            $data['dest_coin_price'] = $destCoinPrice;
            $data = $this->_prepareDataForInsert($data);

            $order = $this->orderRepository->create(data: $data);
            $transaction = $this->transactionService->create([
                'order_id' => $order->id,
                'amount' => $destCoinPrice * $order->quantity,
                'tracker_id' => $this->createTrackerId(),
            ]);

            DB::commit();

            return $transaction->tracker_id;
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error('an error occurred in creating an order: '.$exception->getMessage());

            $this->throwException($exception->responseDto->messages);
        }
    }

    #[ArrayShape(['src_coin_id' => 'integer',
        'dest_coin_id' => 'integer',
        'user_email' => 'string',
        'src_coin_price' => 'integer',
        'dest_coin_price' => 'integer',
        'status' => 'string',
        'quantity' => 'integer'])]
    private function _prepareDataForInsert(array $data): array
    {
        return [
            'src_coin_id' => $data['src_coin_id'],
            'dest_coin_id' => $data['dest_coin_id'],
            'user_email' => $data['email'],
            'src_coin_price' => $data['price'],
            'dest_coin_price' => $data['dest_coin_price'],
            'status' => Order::STATUSES['ACCEPTED'],
            'quantity' => $data['quantity'] ?? 1,
        ];
    }

    private function getDestCoinPrice(int $destCoinId): ?int
    {
        return Coin::query()->find($destCoinId)?->price;
    }

    private function checkDestCoinPrice(?int $destCoinPrice): void
    {
        if (! $destCoinPrice) {
            $baseResponse = new BaseResponseDto(
                status: BaseResponseDto::FAILED,
                code: Response::HTTP_INTERNAL_SERVER_ERROR,
                messages: [__('orders.error-in-fetching-destination-coin-price')]);

            throw new ApiOrderErrorException('', 0, null, $baseResponse);
        }
    }

    private function throwException(array $errorMessages = [])
    {
        $baseResponse = new BaseResponseDto(
            status: BaseResponseDto::FAILED,
            code: Response::HTTP_INTERNAL_SERVER_ERROR,
            messages: [implode(', ', $errorMessages) ?? __('orders.error-occurred-in-creating-an-order')]);

        throw new ApiOrderErrorException('', 0, null, $baseResponse);
    }

    private function createTrackerId(): string
    {
        return Str::random(1).rand(10000, 99999);
    }

    private function checkSrcCoinPrice(int $srcCoinId, int $srcCoinPrice)
    {
        $srcCoin = Coin::query()->find($srcCoinId);

        if ($srcCoin->price != $srcCoinPrice) {
            $baseResponse = new BaseResponseDto(
                status: BaseResponseDto::FAILED,
                code: Response::HTTP_INTERNAL_SERVER_ERROR,
                messages: [__('orders.coin-price-has-changed')]);

            throw new ApiOrderErrorException('', 0, null, $baseResponse);
        }
    }
}

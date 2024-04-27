<?php

namespace App\Modules\Order\Services;

use App\DTOs\BaseResponseDto;
use App\Exceptions\ApiErrorException;
use App\Modules\Acc\Services\TransactionService;
use App\Modules\Coin\Jobs\UpdateCoinsJob;
use App\Modules\Coin\Models\Coin;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\OrderRepository;
use Exception;
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
                $this->coinsNotFound();
            }

            $this->checkSrcCoinPrice($data['src_coin_id'], $data['price']);

            $destCoinPrice = $this->getDestCoinPrice($data['dest_coin_id']);

            // throw exception if destination code is not found
            $this->checkDestCoinPrice($destCoinPrice);

            $data['dest_coin_price'] = $destCoinPrice;
            $data = $this->prepareDataForInsert($data);

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

            $this->throwException($exception->getMessage());
        }
    }

    private function coinsNotFound()
    {
        throw new \Exception(__('orders.destination-coin-not-found'), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[ArrayShape(['src_coin_id' => 'integer',
        'dest_coin_id' => 'integer',
        'user_email' => 'string',
        'src_coin_price' => 'integer',
        'dest_coin_price' => 'integer',
        'status' => 'string',
        'quantity' => 'integer'])]
    private function prepareDataForInsert(array $data): array
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
            throw new ApiErrorException(__('orders.error-in-fetching-destination-coin-price'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function throwException(string $errorMessages = null)
    {
        $baseResponse = new BaseResponseDto(
            status: BaseResponseDto::FAILED,
            code: Response::HTTP_INTERNAL_SERVER_ERROR,
            messages: [ $errorMessages ?? __('orders.error-occurred-in-creating-an-order')]);

        throw new ApiErrorException('', 0, null, $baseResponse);
    }

    private function createTrackerId(): string
    {
        return Str::uuid();
    }

    private function checkSrcCoinPrice(int $srcCoinId, int $srcCoinPrice)
    {
        $srcCoin = Coin::query()->find($srcCoinId);

        if ($srcCoin->price != $srcCoinPrice) {
            throw new Exception(__('orders.coin-price-has-changed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getOrderByTrackerId(string $trackerId): array
    {
        $data = $this->orderRepository->getByTrackerId(trackerId: $trackerId);

        if (!$data) {
            $baseResponse = new BaseResponseDto(
                status: BaseResponseDto::FAILED,
                code: Response::HTTP_INTERNAL_SERVER_ERROR,
                messages: [__('orders.tracker-id-doesnt-exist')]);

            throw new ApiErrorException('', 0, null, $baseResponse);
        }

        return $data;
    }
}

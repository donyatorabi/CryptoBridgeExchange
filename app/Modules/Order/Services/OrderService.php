<?php

namespace App\Modules\Order\Services;

use App\DTOs\BaseResponseDto;
use App\Modules\Acc\Services\TransactionService;
use App\Modules\Order\Exceptions\ApiOrderErrorException;
use App\Modules\Order\Repositories\OrderRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class OrderService
{
    private TransactionService $transactionService;
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = app(OrderRepository::class);
        $this->transactionService = app(TransactionService::class);
    }

    public function store(array $data): void
    {
        DB::beginTransaction();
        try {
            // here I am calculating destination coin price
            $coins = Cache::get('coins');
            $destCoinPrice = 0;
            $destCoinPrice = $this->getDestCoinPrice($coins, $data['dest_coin_id'], $destCoinPrice);

            // throw exception if destination code is not found in cache
            $this->checkDestCoinPrice($destCoinPrice);

            $data = $this->_prepareDataForInsert($data);

            $order = $this->orderRepository->create(data: $data);
            $this->transactionService->create([
                'order_id' => $order->id,
                'amount' => $destCoinPrice * $order->quantity,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            logger()->error('an error occurred in creating an order: ' . $exception->getMessage());

            $this->throwException();
        }
    }

    #[ArrayShape(['src_coin_id' => 'integer', 'dest_coin_id' => 'integer', 'user_email' => 'string', 'src_coin_price' => 'integer'])]
    private function _prepareDataForInsert(array $data): array
    {
        return [
            'src_coin_id' => $data['src_coin_id'],
            'dest_coin_id' => $data['dest_coin_id'],
            'user_email' => $data['email'],
            'src_coin_price' => $data['price'],
        ];
    }

    private function getDestCoinPrice(mixed $coins, $dest_coin_id, mixed $destCoinPrice): mixed
    {
        foreach ($coins as $coin) {
            if ($coin['name_en'] === $dest_coin_id) {
                $destCoinPrice = $coin['price'];
                break; // Exit the loop since we found the destination coin price
            }
        }
        return $destCoinPrice;
    }

    private function checkDestCoinPrice(mixed $destCoinPrice): void
    {
        if (!$destCoinPrice) {
            $baseResponse = new BaseResponseDto(
                status: BaseResponseDto::FAILED,
                code: Response::HTTP_INTERNAL_SERVER_ERROR,
                messages: [__('error-in-fetching-destination-coin-price')]);

            DB::commit();
            throw new ApiOrderErrorException('', 0, null, $baseResponse);
        }
    }

    private function throwException()
    {
        $baseResponse = new BaseResponseDto(
            status: BaseResponseDto::FAILED,
            code: Response::HTTP_INTERNAL_SERVER_ERROR,
            messages: [__('error-occurred-in-creating-an-order')]);

        throw new ApiOrderErrorException('', 0, null, $baseResponse);
    }
}

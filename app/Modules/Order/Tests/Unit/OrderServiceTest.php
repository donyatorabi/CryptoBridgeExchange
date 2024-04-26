<?php

namespace App\Modules\Order\Tests\Unit;

use App\Modules\Order\Exceptions\ApiOrderErrorException;
use App\Modules\Order\Services\OrderService;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function throw_exception_if_coin_doesnt_exist_in_cache()
    {
        $this->expectException(ApiOrderErrorException::class);

        $service = new OrderService();
        $service->store([
            'src_coin_id',
            'dest_coin_id',
            'email',
            'price'
        ]);

        $this->expectExceptionMessage('خطا در گرفتن قیمت ارز مقصد!');
        $this->expectExceptionCode(500);
    }
}

<?php

namespace App\Modules\Coin\DTOs;

class CoinResponseDTO
{
    public function __construct(
        readonly public bool $status,
        readonly public ?string $message = null,
        readonly public ?array $data = []
    ) {
    }

    public function fetchFailed(): bool
    {
        return ! $this->status;
    }
}

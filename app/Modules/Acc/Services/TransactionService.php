<?php

namespace App\Modules\Acc\Services;

use App\Modules\Acc\Models\Transaction;
use App\Modules\Acc\Repositories\TransactionRepository;

class TransactionService
{
    private TransactionRepository $transactionRepository;

    public function __construct()
    {
        $this->transactionRepository = app(TransactionRepository::class);
    }

    public function create(array $data)
    {
        Transaction::query()->create($data);
    }
}

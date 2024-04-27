<?php

namespace App\Modules\Acc\Services;

use App\Modules\Acc\Models\Transaction;
use App\Modules\Acc\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\Model;

class TransactionService
{
    private TransactionRepository $transactionRepository;

    public function __construct()
    {
        $this->transactionRepository = app(TransactionRepository::class, ['model' => app(Transaction::class)]);
    }

    public function create(array $data): ?Model
    {
        return Transaction::query()->create($data);
    }
}

<?php

namespace App\Modules;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    public function __construct(readonly public Model $model)
    {
    }

    public function getAll(): ?array
    {
        return $this->model::all()->toArray();
    }

    public function insert(array $data): bool
    {
        return $this->model::query()->insert($data);
    }

    public function create(array $data): Model
    {
        return $this->model::query()->create($data);
    }
}

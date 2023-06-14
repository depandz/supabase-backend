<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface SupaBaseContract
{
    public function fetchAll(): Collection;

    public function findBy(string $column, string $value): Collection;

    public function findByLike(string $column, string $value): Collection;
}

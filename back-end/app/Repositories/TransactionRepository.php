<?php

namespace App\Repositories;

use Closure;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    public function run(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}
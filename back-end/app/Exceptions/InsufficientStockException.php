<?php

namespace App\Exceptions;

class InsufficientStockException extends ApiException
{
    protected int $statusCode = 422;

    public function __construct(int $available)
    {
        parent::__construct(
            "Estoque insuficiente. Disponível: {$available} unidade(s)."
        );
    }
}
<?php

namespace App\Exceptions;

class EmptyCartException extends ApiException
{
    protected int $statusCode = 422;

    public function __construct()
    {
        parent::__construct('O carrinho está vazio.');
    }
}
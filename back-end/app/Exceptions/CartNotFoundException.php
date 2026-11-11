<?php

namespace App\Exceptions;

class CartNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Carrinho não encontrado.');
    }
}
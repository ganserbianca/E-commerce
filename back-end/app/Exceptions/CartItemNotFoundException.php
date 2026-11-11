<?php

namespace App\Exceptions;

class CartItemNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Item do carrinho não encontrado.');
    }
}
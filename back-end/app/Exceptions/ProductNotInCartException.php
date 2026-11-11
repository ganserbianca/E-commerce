<?php

namespace App\Exceptions;

class ProductNotInCartException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Produto não encontrado no carrinho.');
    }
}
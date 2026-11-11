<?php

namespace App\Exceptions;

class OrderNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Pedido não encontrado.');
    }
}
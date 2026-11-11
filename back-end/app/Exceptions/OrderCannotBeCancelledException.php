<?php

namespace App\Exceptions;

class OrderCannotBeCancelledException extends ApiException
{
    protected int $statusCode = 422;

    public function __construct()
    {
        parent::__construct('Este pedido não pode ser cancelado.');
    }
}
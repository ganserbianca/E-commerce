<?php

namespace App\Exceptions;

class OrderNotPendingException extends ApiException
{
    protected int $statusCode = 409;

    public function __construct()
    {
        parent::__construct('Este pedido não está aguardando pagamento.');
    }
}
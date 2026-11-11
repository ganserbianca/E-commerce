<?php

namespace App\Exceptions;

class PaymentNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Pagamento não encontrado.');
    }
}
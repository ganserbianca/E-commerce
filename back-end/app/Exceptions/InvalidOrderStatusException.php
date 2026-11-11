<?php

namespace App\Exceptions;

class InvalidOrderStatusException extends ApiException
{
    protected int $statusCode = 409;

    public function __construct()
    {
        parent::__construct('Transição de status inválida.');
    }
}
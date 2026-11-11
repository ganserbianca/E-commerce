<?php

namespace App\Exceptions;

class AddressForbiddenException extends ApiException
{
    protected int $statusCode = 403;

    public function __construct()
    {
        parent::__construct('Você não pode acessar este endereço.');
    }
}
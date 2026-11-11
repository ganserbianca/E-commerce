<?php

namespace App\Exceptions;

class AddressNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Endereço não encontrado.');
    }
}
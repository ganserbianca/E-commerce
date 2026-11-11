<?php

namespace App\Exceptions;

class ProductUnavailableException extends ApiException
{
    protected int $statusCode = 422;

    public function __construct()
    {
        parent::__construct('Produto indisponível.');
    }
}
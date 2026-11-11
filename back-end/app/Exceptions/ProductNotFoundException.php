<?php

namespace App\Exceptions;

class ProductNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Produto não encontrado.');
    }
}
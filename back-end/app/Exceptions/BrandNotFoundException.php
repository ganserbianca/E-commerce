<?php

namespace App\Exceptions;

class BrandNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Marca não encontrada.');
    }
}
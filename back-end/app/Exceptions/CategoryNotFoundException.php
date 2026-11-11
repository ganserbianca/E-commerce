<?php

namespace App\Exceptions;

class CategoryNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Categoria não encontrada.');
    }
}
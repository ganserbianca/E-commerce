<?php

namespace App\Exceptions;

class UserNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Usuário não encontrado.');
    }
}
<?php

namespace App\Exceptions;

class InvalidCredentialsException extends ApiException
{
    protected int $statusCode = 401;

    public function __construct()
    {
        parent::__construct('E-mail ou senha incorretos.');
    }
}
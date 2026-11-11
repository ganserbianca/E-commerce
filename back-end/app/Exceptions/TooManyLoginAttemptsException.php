<?php

namespace App\Exceptions;

class TooManyLoginAttemptsException extends ApiException
{
    protected int $statusCode = 429;

    public function __construct()
    {
        parent::__construct(
            'Muitas tentativas de login. Tente novamente mais tarde.'
        );
    }
}
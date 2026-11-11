<?php

namespace App\Exceptions;

class InvalidCurrentPasswordException extends ApiException
{
    protected int $statusCode = 422;

    public function __construct()
    {
        parent::__construct('A senha atual está incorreta.');
    }
}
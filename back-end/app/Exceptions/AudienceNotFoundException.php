<?php

namespace App\Exceptions;

class AudienceNotFoundException extends ApiException
{
    protected int $statusCode = 404;

    public function __construct()
    {
        parent::__construct('Público-alvo não encontrado.');
    }
}
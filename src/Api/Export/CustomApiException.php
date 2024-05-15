<?php

declare(strict_types=1);

namespace App\Api\Export;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomApiException extends HttpException
{
    public function __construct(
        string $message,
        int $statusCode = 400,
        ?\Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}

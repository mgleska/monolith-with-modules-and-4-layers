<?php

declare(strict_types=1);

namespace App\Api\Export;

class FailResponseDto
{
    // https://github.com/omniti-labs/jsend

    public string $status;
    public mixed $data;

    public function __construct(string $message)
    {
        $this->status = ResponseStatus::FAIL->value;
        $this->data = ['message' => $message];
    }
}

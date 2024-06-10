<?php

declare(strict_types=1);

namespace App\Api\_2_Export\Dto;

use App\Api\_2_Export\ResponseStatusEnum;

class FailResponseDto
{
    // https://github.com/omniti-labs/jsend

    public string $status;
    public mixed $data;

    public function __construct(string $message)
    {
        $this->status = ResponseStatusEnum::FAIL->value;
        $this->data = ['message' => $message];
    }
}

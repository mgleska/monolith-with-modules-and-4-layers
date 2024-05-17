<?php

declare(strict_types=1);

namespace App\Api\Export;

class SuccessResponseDto
{
    // https://github.com/omniti-labs/jsend

    public string $status;
    public mixed $data;

    public function __construct(mixed $data = null)
    {
        $this->status = ResponseStatus::SUCCESS->value;
        $this->data = $data;
    }
}

<?php

declare(strict_types=1);

namespace App\Api\Export\Dto;

use App\Api\Export\ResponseStatusEnum;

class SuccessResponseDto
{
    // https://github.com/omniti-labs/jsend

    public string $status;
    public mixed $data;

    public function __construct(mixed $data = null)
    {
        $this->status = ResponseStatusEnum::SUCCESS->value;
        $this->data = $data;
    }
}

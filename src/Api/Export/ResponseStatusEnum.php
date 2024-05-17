<?php

declare(strict_types=1);

namespace App\Api\Export;

enum ResponseStatusEnum: string
{
    // https://github.com/omniti-labs/jsend

    case SUCCESS = 'success';
    case FAIL = 'fail';
}

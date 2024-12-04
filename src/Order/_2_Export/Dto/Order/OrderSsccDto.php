<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\Order;

use OpenApi\Attributes as OA;

class OrderSsccDto
{
    public readonly int $id;

    #[OA\Property(minLength: 18, maxLength:18, example: '001000000000034593')]
    public readonly string $code;

    public function __construct(int $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
    }
}

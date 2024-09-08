<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\Order;

use App\Order\_4_Infrastructure\Entity\OrderSsccEntity;
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

    public static function fromEntity(OrderSsccEntity $entity): self
    {
        return new self($entity->getId(), $entity->getCode());
    }
}

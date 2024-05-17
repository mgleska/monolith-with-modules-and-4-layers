<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use App\Order\Entity\OrderSscc;

class OrderSsccDto
{
    public int $id;
    public string $code;

    public static function fromEntity(OrderSscc $entity): self
    {
        $dto = new self();
        $dto->id = $entity->getId();
        $dto->code = $entity->getCode();

        return $dto;
    }
}

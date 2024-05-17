<?php

declare(strict_types=1);

namespace App\Order\Dto\Order;

use App\Order\Entity\OrderLine;

class OrderLineDto
{
    public int $id;
    public int $quantity;
    // pallet length in [cm]
    public int $length;
    // pallet width in [cm]
    public int $width;
    // pallet height in [cm]
    public int $height;
    // pallet weight in [kg] multiplied by 100
    public int $weightOnePallet;
    // total weight of all pallets of order line, in [kg] multiplied by 100
    public int $weightTotal;
    public string $goodsDescription;

    public static function fromEntity(OrderLine $entity): self
    {
        $dto = new self();
        $dto->id = $entity->getId();
        $dto->quantity = $entity->getQuantity();
        $dto->length = $entity->getLength();
        $dto->width = $entity->getWidth();
        $dto->height = $entity->getHeight();
        $dto->weightOnePallet = $entity->getWeightOnePallet();
        $dto->weightTotal = $entity->getWeightTotal();
        $dto->goodsDescription = $entity->getGoodsDescription();

        return $dto;
    }
}

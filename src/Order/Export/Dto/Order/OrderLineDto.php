<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use App\Order\Entity\OrderLine;
use OpenApi\Attributes as OA;

class OrderLineDto
{
    public int $id;
    public int $quantity;

    #[OA\Property(description: 'pallet length in [cm]')]
    public int $length;

    #[OA\Property(description: 'pallet width in [cm]')]
    public int $width;

    #[OA\Property(description: 'pallet height in [cm]')]
    public int $height;

    #[OA\Property(description: 'pallet weight in [kg]')]
    public float $weightOnePallet;

    #[OA\Property(description: 'total weight of all pallets of order line, in [kg]')]
    public float $weightTotal;

    #[OA\Property(minLength: 1, maxLength:250, example: 'computers')]
    public string $goodsDescription;

    public function __construct(
        int $id,
        int $quantity,
        int $length,
        int $width,
        int $height,
        float $weightOnePallet,
        float $weightTotal,
        string $goodsDescription
    ) {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->weightOnePallet = $weightOnePallet;
        $this->weightTotal = $weightTotal;
        $this->goodsDescription = $goodsDescription;
    }

    public static function fromEntity(OrderLine $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getQuantity(),
            $entity->getLength(),
            $entity->getWidth(),
            $entity->getHeight(),
            $entity->getWeightOnePallet() / 100.0,
            $entity->getWeightTotal() / 100.0,
            $entity->getGoodsDescription()
        );
    }
}

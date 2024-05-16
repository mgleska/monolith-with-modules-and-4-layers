<?php

declare(strict_types=1);

namespace App\Order\Dto;

use App\Order\Entity\Order;
use App\Order\Entity\OrderLine;
use App\Order\Enum\OrderStatusEnum;

class OrderDto
{
    public int $id;
    public string $number;
    public OrderStatusEnum $status;
    public int $quantityTotal;
    public OrderAddressDto $loadingAddress;
    public OrderAddressDto $deliveryAddress;
    /**
     * @var OrderLine[]
     */
    public array $lines;

    /**
     * @param OrderLine[] $lines
     */
    public static function fromEntity(Order $entity, array $lines): self
    {
        $dto = new self();
        $dto->id = $entity->getId();
        $dto->number = $entity->getNumber();
        $dto->status = $entity->getStatus();
        $dto->quantityTotal = $entity->getQuantityTotal();
        $dto->loadingAddress = new OrderAddressDto(
            $entity->getLoadingNameCompanyOrPerson(),
            $entity->getLoadingAddress(),
            $entity->getLoadingCity(),
            $entity->getLoadingZipCode(),
            $entity->getLoadingContactPerson(),
            $entity->getLoadingContactPhone(),
            $entity->getLoadingContactEmail(),
        );
        $dto->deliveryAddress = new OrderAddressDto(
            $entity->getDeliveryNameCompanyOrPerson(),
            $entity->getDeliveryAddress(),
            $entity->getDeliveryCity(),
            $entity->getDeliveryZipCode(),
            $entity->getDeliveryContactPerson(),
            $entity->getDeliveryContactPhone(),
            $entity->getDeliveryContactEmail(),
        );
        $dto->lines = $lines;

        return $dto;
    }
}

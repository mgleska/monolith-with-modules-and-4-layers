<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use App\Order\Entity\Order;
use App\Order\Entity\OrderLine;
use App\Order\Entity\OrderSscc;
use App\Order\Enum\OrderStatusEnum;

use function array_map;

class OrderDto
{
    public int $id;
    public string $number;
    public OrderStatusEnum $status;
    public int $quantityTotal;
    public OrderAddressDto $loadingAddress;
    public OrderAddressDto $deliveryAddress;
    /**
     * @var OrderLineDto[]
     */
    public array $lines;
    /**
     * @var OrderSsccDto[]
     */
    public array $ssccs;

    /**
     * @param OrderLine[] $lines
     * @param OrderSscc[] $ssccs
     */
    public static function fromEntity(Order $entity, array $lines, array $ssccs): self
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
        $dto->lines = array_map(
            function (OrderLine $line) {
                return OrderLineDto::fromEntity($line);
            },
            $lines
        );
        $dto->ssccs = array_map(
            function (OrderSscc $sscc) {
                return OrderSsccDto::fromEntity($sscc);
            },
            $ssccs
        );

        return $dto;
    }
}

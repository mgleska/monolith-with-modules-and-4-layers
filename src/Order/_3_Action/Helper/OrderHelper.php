<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Helper;

use App\Order\_2_Export\Dto\Order\DateVO;
use App\Order\_2_Export\Dto\Order\OrderAddressContactDto;
use App\Order\_2_Export\Dto\Order\OrderAddressDto;
use App\Order\_2_Export\Dto\Order\OrderDto;
use App\Order\_2_Export\Dto\Order\OrderLineDto;
use App\Order\_2_Export\Dto\Order\OrderSsccDto;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Entity\OrderLine;
use App\Order\_3_Action\Entity\OrderSscc;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class OrderHelper
{
    /**
     * @throws Exception
     */
    public static function createOrderDtoFromEntity(Order $entity): OrderDto
    {
        return new OrderDto(
            $entity->getId(),
            $entity->getVersion(),
            $entity->getNumber(),
            $entity->getStatus(),
            $entity->getQuantityTotal(),
            new DateVO($entity->getLoadingDate()),
            $entity->getLoadingFixedAddressExternalId(),
            new OrderAddressDto(
                $entity->getLoadingNameCompanyOrPerson(),
                $entity->getLoadingAddress(),
                $entity->getLoadingCity(),
                $entity->getLoadingZipCode(),
            ),
            new OrderAddressContactDto(
                $entity->getLoadingContactPerson(),
                $entity->getLoadingContactPhone(),
                $entity->getLoadingContactEmail(),
            ),
            new OrderAddressDto(
                $entity->getDeliveryNameCompanyOrPerson(),
                $entity->getDeliveryAddress(),
                $entity->getDeliveryCity(),
                $entity->getDeliveryZipCode(),
            ),
            new OrderAddressContactDto(
                $entity->getDeliveryContactPerson(),
                $entity->getDeliveryContactPhone(),
                $entity->getDeliveryContactEmail(),
            ),
            array_map(
                function (OrderLine $line) {
                    return self::createOrderLineDtoFromEntity($line);
                },
                $entity->getLines()->toArray()
            ),
            array_map(
                function (OrderSscc $sscc) {
                    return self::createOrderSsccDtoFromEntity($sscc);
                },
                $entity->getSsccs()->toArray()
            )
        );
    }

    public static function createOrderLineDtoFromEntity(OrderLine $entity): OrderLineDto
    {
        return new OrderLineDto(
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

    public static function createOrderSsccDtoFromEntity(OrderSscc $entity): OrderSsccDto
    {
        return new OrderSsccDto(
            $entity->getId(),
            $entity->getCode()
        );
    }
}

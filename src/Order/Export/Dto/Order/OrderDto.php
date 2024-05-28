<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use App\Order\Entity\Order;
use App\Order\Entity\OrderLine;
use App\Order\Entity\OrderSscc;
use App\Order\Enum\OrderStatusEnum;
use OpenApi\Attributes as OA;

use function array_map;

class OrderDto
{
    public readonly int $id;

    #[OA\Property(minLength: 1, maxLength: 50, example: 'ul. Garbary 125')]
    public readonly string $number;

    public readonly OrderStatusEnum $status;
    public readonly int $quantityTotal;
    public readonly OrderAddressDto $loadingAddress;
    public readonly OrderAddressDto $deliveryAddress;
    /**
     * @var OrderLineDto[]
     */
    public readonly array $lines;
    /**
     * @var OrderSsccDto[]
     */
    public readonly array $ssccs;

    /**
     * @param OrderLineDto[] $lines
     * @param OrderSsccDto[] $ssccs
     */
    public function __construct(
        int $id,
        string $number,
        OrderStatusEnum $status,
        int $quantityTotal,
        OrderAddressDto $loadingAddress,
        OrderAddressDto $deliveryAddress,
        array $lines,
        array $ssccs
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->status = $status;
        $this->quantityTotal = $quantityTotal;
        $this->loadingAddress = $loadingAddress;
        $this->deliveryAddress = $deliveryAddress;
        $this->lines = $lines;
        $this->ssccs = $ssccs;
    }

    /**
     * @param OrderLine[] $lines
     * @param OrderSscc[] $ssccs
     */
    public static function fromEntity(Order $entity, array $lines, array $ssccs): self
    {
        return new self(
            $entity->getId(),
            $entity->getNumber(),
            $entity->getStatus(),
            $entity->getQuantityTotal(),
            new OrderAddressDto(
                $entity->getLoadingNameCompanyOrPerson(),
                $entity->getLoadingAddress(),
                $entity->getLoadingCity(),
                $entity->getLoadingZipCode(),
                $entity->getLoadingContactPerson(),
                $entity->getLoadingContactPhone(),
                $entity->getLoadingContactEmail(),
            ),
            new OrderAddressDto(
                $entity->getDeliveryNameCompanyOrPerson(),
                $entity->getDeliveryAddress(),
                $entity->getDeliveryCity(),
                $entity->getDeliveryZipCode(),
                $entity->getDeliveryContactPerson(),
                $entity->getDeliveryContactPhone(),
                $entity->getDeliveryContactEmail(),
            ),
            array_map(
                function (OrderLine $line) {
                    return OrderLineDto::fromEntity($line);
                },
                $lines
            ),
            array_map(
                function (OrderSscc $sscc) {
                    return OrderSsccDto::fromEntity($sscc);
                },
                $ssccs
            )
        );
    }
}

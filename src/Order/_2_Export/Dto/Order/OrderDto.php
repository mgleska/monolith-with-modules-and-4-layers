<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\Order;

use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Entity\OrderLine;
use App\Order\_3_Action\Entity\OrderSscc;
use Exception;
use OpenApi\Attributes as OA;

use function array_map;

class OrderDto
{
    public readonly int $id;

    #[OA\Property(minLength: 1, maxLength: 50, example: 'ul. Garbary 125')]
    public readonly string $number;

    public readonly OrderStatusEnum $status;
    public readonly int $quantityTotal;
    public readonly DateVO $loadingDate;

    #[OA\Property(minLength: 1, maxLength:100, example: 'WH1')]
    public readonly ?string $loadingFixedAddressExternalId;

    public readonly OrderAddressDto $loadingAddress;
    public readonly OrderAddressContactDto $loadingContact;
    public readonly OrderAddressDto $deliveryAddress;
    public readonly OrderAddressContactDto $deliveryContact;
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
     * @throws Exception
     */
    public function __construct(
        int $id,
        string $number,
        OrderStatusEnum $status,
        int $quantityTotal,
        DateVO $loadingDate,
        ?string $loadingFixedAddressExternalId,
        OrderAddressDto $loadingAddress,
        OrderAddressContactDto $loadingContact,
        OrderAddressDto $deliveryAddress,
        OrderAddressContactDto $deliveryContact,
        array $lines,
        array $ssccs
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->status = $status;
        $this->quantityTotal = $quantityTotal;
        $this->loadingDate = $loadingDate;
        $this->loadingFixedAddressExternalId = $loadingFixedAddressExternalId;
        $this->loadingAddress = $loadingAddress;
        $this->loadingContact = $loadingContact;
        $this->deliveryAddress = $deliveryAddress;
        $this->deliveryContact = $deliveryContact;
        $this->lines = $lines;
        $this->ssccs = $ssccs;
    }

    /**
     * @throws Exception
     */
    public static function fromEntity(Order $entity): self
    {
        return new self(
            $entity->getId(),
            $entity->getHeader()->getNumber(),
            $entity->getHeader()->getStatus(),
            $entity->getHeader()->getQuantityTotal(),
            new DateVO($entity->getHeader()->getLoadingDate()),
            $entity->getHeader()->getLoadingFixedAddressExternalId(),
            new OrderAddressDto(
                $entity->getHeader()->getLoadingNameCompanyOrPerson(),
                $entity->getHeader()->getLoadingAddress(),
                $entity->getHeader()->getLoadingCity(),
                $entity->getHeader()->getLoadingZipCode(),
            ),
            new OrderAddressContactDto(
                $entity->getHeader()->getLoadingContactPerson(),
                $entity->getHeader()->getLoadingContactPhone(),
                $entity->getHeader()->getLoadingContactEmail(),
            ),
            new OrderAddressDto(
                $entity->getHeader()->getDeliveryNameCompanyOrPerson(),
                $entity->getHeader()->getDeliveryAddress(),
                $entity->getHeader()->getDeliveryCity(),
                $entity->getHeader()->getDeliveryZipCode(),
            ),
            new OrderAddressContactDto(
                $entity->getHeader()->getDeliveryContactPerson(),
                $entity->getHeader()->getDeliveryContactPhone(),
                $entity->getHeader()->getDeliveryContactEmail(),
            ),
            array_map(
                function (OrderLine $line) {
                    return OrderLineDto::fromEntity($line);
                },
                $entity->getLines()
            ),
            array_map(
                function (OrderSscc $sscc) {
                    return OrderSsccDto::fromEntity($sscc);
                },
                $entity->getSsccs()
            )
        );
    }
}

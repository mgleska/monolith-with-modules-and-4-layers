<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\Order;

use App\Order\_2_Export\Enum\OrderStatusEnum;
use Exception;
use OpenApi\Attributes as OA;

use function array_map;

class OrderDto
{
    public readonly int $id;
    public readonly int $version;

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
        int $version,
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
        $this->version = $version;
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
}

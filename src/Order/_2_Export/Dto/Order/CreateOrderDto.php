<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\Order;

use Exception;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderDto
{
    #[Assert\Date]
    public readonly string $loadingDate;

    #[Assert\Length(min: 1, max: 100)]
    #[OA\Property(example: 'WH1')]
    public readonly ?string $loadingFixedAddressExternalId;

    #[Assert\Valid]
    public readonly ?OrderAddressDto $loadingAddress;

    #[Assert\Valid]
    public readonly OrderAddressContactDto $loadingContact;

    #[Assert\Valid]
    public readonly OrderAddressDto $deliveryAddress;

    #[Assert\Valid]
    public readonly OrderAddressContactDto $deliveryContact;

    /**
     * @var OrderLineDto[]
     */
    #[Assert\Valid]
    public readonly array $lines;

    /**
     * @param OrderLineDto[] $lines
     * @throws Exception
     */
    public function __construct(
        string $loadingDate,
        ?string $loadingFixedAddressExternalId,
        ?OrderAddressDto $loadingAddress,
        OrderAddressContactDto $loadingContact,
        OrderAddressDto $deliveryAddress,
        OrderAddressContactDto $deliveryContact,
        array $lines,
    ) {
        $this->loadingDate = $loadingDate;
        $this->loadingFixedAddressExternalId = $loadingFixedAddressExternalId;
        $this->loadingAddress = $loadingAddress;
        $this->loadingContact = $loadingContact;
        $this->deliveryAddress = $deliveryAddress;
        $this->deliveryContact = $deliveryContact;
        $this->lines = $lines;
    }
}

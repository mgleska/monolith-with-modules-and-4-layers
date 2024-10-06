<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\FixedAddress;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateFixedAddressDto
{
    #[Assert\Range(min: 1)]
    #[Assert\NotNull]
    #[OA\Property(example: '2')]
    public readonly int $customerId;

    #[Assert\Length(min:1, max:100)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'WH1')]
    public readonly string $externalId;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'Acme Company Warehouse 1')]
    public readonly string $nameCompanyOrPerson;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'ul. Garbary 125')]
    public readonly string $address;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'Poznań')]
    public readonly string $city;

    #[Assert\Length(min:1, max:50)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: '61-719')]
    public readonly string $zipCode;

    public function __construct(
        int $customerId,
        string $externalId,
        string $nameCompanyOrPerson,
        string $address,
        string $city,
        string $zipCode
    ) {
        $this->customerId = $customerId;
        $this->externalId = $externalId;
        $this->nameCompanyOrPerson = $nameCompanyOrPerson;
        $this->address = $address;
        $this->city = $city;
        $this->zipCode = $zipCode;
    }
}
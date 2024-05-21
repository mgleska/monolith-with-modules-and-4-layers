<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\FixedAddress;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class CreateFixedAddressDto
{
    #[Assert\Range(min: 1)]
    #[Assert\NotNull]
    #[OA\Property(example: '2')]
    public int $customerId;

    #[Assert\Length(min:1, max:100)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'WH1')]
    public string $externalId;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'Acme Company Warehouse 1')]
    public string $nameCompanyOrPerson;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'ul. Garbary 125')]
    public string $address;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: 'Poznań')]
    public string $city;

    #[Assert\Length(min:1, max:50)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    #[Assert\NotNull]
    #[OA\Property(example: '61-719')]
    public string $zipCode;
}

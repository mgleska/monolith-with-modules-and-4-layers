<?php

declare(strict_types=1);

namespace App\Order\Dto;

use App\Order\Entity\FixedAddress;

class FixedAddressDto
{
    public int $id;
    public string $externalId;
    public string $nameCompanyOrPerson;
    public string $address;
    public string $city;
    public string $zipCode;

    public static function fromEntity(FixedAddress $address): self
    {
        $dto = new self();
        $dto->id = $address->getId();
        $dto->externalId = $address->getExternalId();
        $dto->nameCompanyOrPerson = $address->getNameCompanyOrPerson();
        $dto->address = $address->getAddress();
        $dto->city = $address->getCity();
        $dto->zipCode = $address->getZipCode();

        return $dto;
    }
}

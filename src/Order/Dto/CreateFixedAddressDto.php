<?php

declare(strict_types=1);

namespace App\Order\Dto;

use App\Order\Entity\FixedAddress;

class CreateFixedAddressDto
{
    public string $externalId;
    public string $nameCompanyOrPerson;
    public string $address;
    public string $city;
    public string $zipCode;
}

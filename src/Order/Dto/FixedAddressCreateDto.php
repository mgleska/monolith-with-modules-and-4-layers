<?php

declare(strict_types=1);

namespace App\Order\Dto;

class FixedAddressCreateDto
{
    public string $externalId;
    public string $nameCompanyOrPerson;
    public string $address;
    public string $city;
    public string $zipCode;
}

<?php

declare(strict_types=1);

namespace App\Order\Dto\Order;

class OrderAddressDto
{
    public function __construct(
        public string $nameCompanyOrPerson,
        public string $address,
        public string $city,
        public string $zipCode,
        public string $contactPerson,
        public string $contactPhone,
        public ?string $contactEmail,
    )
    {}
}

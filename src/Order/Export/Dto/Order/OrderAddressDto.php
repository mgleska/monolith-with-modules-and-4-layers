<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use OpenApi\Attributes as OA;

class OrderAddressDto
{
    #[OA\Property(minLength: 1, maxLength:250, example: 'Acme Company Warehouse 1')]
    public readonly string $nameCompanyOrPerson;

    #[OA\Property(minLength: 1, maxLength:250, example: 'ul. Garbary 125')]
    public readonly string $address;

    #[OA\Property(minLength: 1, maxLength:250, example: 'Poznań')]
    public readonly string $city;

    #[OA\Property(minLength: 1, maxLength:250, example: '61-719')]
    public readonly string $zipCode;

    #[OA\Property(minLength: 1, maxLength:250, example: 'John Doe')]
    public readonly string $contactPerson;

    #[OA\Property(minLength: 1, maxLength:250, example: '+48-123-456-789')]
    public readonly string $contactPhone;

    #[OA\Property(minLength: 1, maxLength:250, example: 'johh.doe@acme.com')]
    public readonly ?string $contactEmail;

    public function __construct(
        string $nameCompanyOrPerson,
        string $address,
        string $city,
        string $zipCode,
        string $contactPerson,
        string $contactPhone,
        ?string $contactEmail
    ) {
        $this->nameCompanyOrPerson = $nameCompanyOrPerson;
        $this->address = $address;
        $this->city = $city;
        $this->zipCode = $zipCode;
        $this->contactPerson = $contactPerson;
        $this->contactPhone = $contactPhone;
        $this->contactEmail = $contactEmail;
    }
}

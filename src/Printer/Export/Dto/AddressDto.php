<?php

declare(strict_types=1);

namespace App\Printer\Export\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class AddressDto
{
    #[Assert\Length(min:1, max:40)]
    public string $line1;

    #[Assert\Length(min:1, max:40)]
    public string $line2;

    #[Assert\Length(min:1, max:15)]
    public string $zipCode;

    #[Assert\Length(min:1, max:25)]
    public string $city;
}

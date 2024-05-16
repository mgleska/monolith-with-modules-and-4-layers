<?php

declare(strict_types=1);

namespace App\Order\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FixedAddressCreateDto
{
    #[Assert\Length(min:1, max:100)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    public string $externalId;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    public string $nameCompanyOrPerson;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    public string $address;

    #[Assert\Length(min:1, max:250)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    public string $city;

    #[Assert\Length(min:1, max:50)]
    #[Assert\Regex(pattern: '/^\S/', message: 'This value do not match regex pattern {{ pattern }}')]
    public string $zipCode;
}

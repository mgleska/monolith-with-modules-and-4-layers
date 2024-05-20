<?php

declare(strict_types=1);

namespace App\Printer\Export\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PrintLabelDto
{
    #[Assert\Valid]
    public AddressDto $loadingAddress;

    #[Assert\Valid]
    public AddressDto $deliveryAddress;

    #[Assert\Valid]
    /** @var GoodsLineDto[] $lines */
    public array $lines;

    #[Assert\Valid]
    /** @var SsccDto[] $ssccs */
    public array $ssccs;
}

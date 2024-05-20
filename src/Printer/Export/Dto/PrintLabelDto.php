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

    /** @var GoodsLineDto[] $lines */
    #[Assert\Valid]
    public array $lines;

    /** @var SsccDto[] $ssccs */
    #[Assert\Valid]
    public array $ssccs;
}

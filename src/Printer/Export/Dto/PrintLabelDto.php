<?php

declare(strict_types=1);

namespace App\Printer\Export\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PrintLabelDto
{
    #[Assert\Valid]
    #[Assert\NotNull]
    public AddressDto $loadingAddress;

    #[Assert\Valid]
    #[Assert\NotNull]
    public AddressDto $deliveryAddress;

    /** @var GoodsLineDto[] $lines */
    #[Assert\Valid]
    #[Assert\NotNull]
    public array $lines;

    /** @var SsccDto[] $ssccs */
    #[Assert\Valid]
    #[Assert\NotNull]
    public array $ssccs;
}

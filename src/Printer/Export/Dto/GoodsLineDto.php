<?php

declare(strict_types=1);

namespace App\Printer\Export\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GoodsLineDto
{
    #[Assert\Length(min:1, max:25)]
    public string $description;

    #[Assert\Range(min: 1, max: 99)]
    public int $quantity;
}

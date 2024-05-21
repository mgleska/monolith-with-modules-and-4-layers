<?php

declare(strict_types=1);

namespace App\Printer\Export\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class SsccDto
{
    #[Assert\Length(exactly: 18)]
    #[Assert\NotNull]
    public string $code;
}

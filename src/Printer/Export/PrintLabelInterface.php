<?php

declare(strict_types=1);

namespace App\Printer\Export;

use App\Printer\Export\Dto\PrintLabelDto;

interface PrintLabelInterface
{
    public function printLabel(PrintLabelDto $dto): string;
}

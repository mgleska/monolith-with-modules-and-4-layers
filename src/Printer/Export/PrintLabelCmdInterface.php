<?php

declare(strict_types=1);

namespace App\Printer\Export;

use App\Printer\Export\Dto\PrintLabelDto;

interface PrintLabelCmdInterface
{
    public function printLabelCmd(PrintLabelDto $dto): string;
}

<?php

declare(strict_types=1);

namespace App\Printer\Service;

use App\Printer\Export\Dto\PrintLabelDto;
use App\Printer\Export\PrintLabelInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function sprintf;
use function str_repeat;

class PrintCommand implements PrintLabelInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function printLabel(PrintLabelDto $dto, bool $crossModule): string
    {
        if ($crossModule) {
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                throw new ValidationFailedException('aaa', $errors);
            }
        }

        $label = sprintf("%-40s | %-40s\n", 'Loading Address', 'Delivery Address');
        $label .= str_repeat('-', 81) . "\n";
        $label .= sprintf("%-40s | %-40s\n", $dto->loadingAddress->line1, $dto->deliveryAddress->line1);
        $label .= sprintf("%-40s | %-40s\n", $dto->loadingAddress->line2, $dto->deliveryAddress->line2);
        $label .= sprintf(
            "%-15s %-25s | %-15s %-25s\n",
            $dto->loadingAddress->zipCode,
            $dto->loadingAddress->city,
            $dto->deliveryAddress->zipCode,
            $dto->deliveryAddress->city
        );
        $label .= str_repeat('-', 81) . "\n";

        $count = 1;
        foreach ($dto->lines as $line) {
            $label .= sprintf("| %3d | %-25s | %2d\n", $count, $line->description, $line->quantity);
            $count += 1;
        }
        $label .= str_repeat('-', 81) . "\n";

        $column = 1;
        foreach ($dto->ssccs as $sscc) {
            if ($column > 1) {
                $label .= ' | ';
            }
            $label .= sprintf("%18s", $sscc->code);
            $column += 1;
            if ($column > 4) {
                $label .= "\n";
                $column = 1;
            }
        }

        return $label;
    }
}

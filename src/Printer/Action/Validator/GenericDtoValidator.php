<?php

declare(strict_types=1);

namespace App\Printer\Action\Validator;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenericDtoValidator
{
    public function __construct(
        private readonly ValidatorInterface $symfonyValidator,
    ) {
    }

    /**
     * @throws ValidationFailedException
     */
    public function validate(object $dto, string $methodName): void
    {
        $errors = $this->symfonyValidator->validate($dto);
        if ($errors->count() > 0) {
            throw new ValidationFailedException($methodName, $errors);
        }
    }
}

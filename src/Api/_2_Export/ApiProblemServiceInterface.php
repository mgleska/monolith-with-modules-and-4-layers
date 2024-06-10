<?php

declare(strict_types=1);

namespace App\Api\_2_Export;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

interface ApiProblemServiceInterface
{
    public function handleEvent(ExceptionEvent $event): void;
}

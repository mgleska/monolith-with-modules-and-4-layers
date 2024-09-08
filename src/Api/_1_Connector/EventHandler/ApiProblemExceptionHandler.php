<?php

declare(strict_types=1);

namespace App\Api\_1_Connector\EventHandler;

use App\Api\_2_Export\ApiProblemServiceInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiProblemExceptionHandler
{
    public function __construct(
        private readonly ApiProblemServiceInterface $service,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->service->handleEvent($event);
    }
}

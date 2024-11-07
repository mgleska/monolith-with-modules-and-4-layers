<?php

declare(strict_types=1);

namespace App\Admin\_1_Connector\EventHandler;

use App\Admin\_2_Export\SwitchDatabaseInterface;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class OnKernelControllerHandler
{
    public function __construct(
        private readonly SwitchDatabaseInterface $service,
    ) {
    }

    /**
     * @throws DBALException
     * @noinspection PhpUnusedParameterInspection
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $this->service->switchDatabase();
    }
}

<?php

declare(strict_types=1);

namespace App\Customer\_1_Connector\EventListener;

use App\Customer\_2_Export\FillBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly FillBagInterface $service,
    ) {
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $this->service->fillBag();
    }

    public static function getSubscribedEvents(): array
    {
        return [LoginSuccessEvent::class => 'onLoginSuccess'];
    }
}

<?php

declare(strict_types=1);

namespace App\Auth\_1_Connector\EventHandler;

use App\Auth\_2_Export\GetUserBadgeFromInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly GetUserBadgeFromInterface $service,
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        return $this->service->getUserBadgeFrom($accessToken);
    }
}

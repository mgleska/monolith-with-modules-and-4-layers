<?php

declare(strict_types=1);

namespace App\Auth\_2_Export;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

interface GetUserBadgeFromInterface
{
    public function getUserBadgeFrom(string $accessToken): UserBadge;
}

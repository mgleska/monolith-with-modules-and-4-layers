<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Repository\UserRepository;
use App\Auth\UserBag;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserBag $userBag,
    )
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $userEntity = $this->userRepository->findOneBy(['login' => $accessToken]);
        if (null === $userEntity) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $this->userBag->setUserId($userEntity->getId());
        $this->userBag->setCustomerId($userEntity->getCustomerId());

        // and return a UserBadge object containing the user identifier from the found token
        // (this is the same identifier used in Security configuration; it can be an email,
        // a UUUID, a username, a database ID, etc.)
        return new UserBadge($userEntity->getLogin());
    }
}

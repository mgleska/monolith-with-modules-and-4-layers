<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Service;

use App\Auth\_2_Export\UserBagInterface;
use App\Customer\_2_Export\FillBagInterface;
use App\Customer\_4_Infrastructure\Repository\CustomerRepository;

class BagService implements FillBagInterface
{
    public function __construct(
        private readonly CustomerBag $customerBag,
        private readonly CustomerRepository $repository,
        private readonly UserBagInterface $userBag,
    ) {
    }

    public function fillBag(): void
    {
        $customer = $this->repository->find($this->userBag->getCustomerId());

        $this->customerBag
            ->setCustomerId($customer->getId())
            ->setDatabaseSuffix($customer->getDbNameSuffix())
            ->setName($customer->getName());
    }
}

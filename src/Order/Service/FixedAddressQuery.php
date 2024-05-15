<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Auth\Export\UserBag;
use App\Order\Dto\FixedAddressDto;
use App\Order\Repository\FixedAddressRepository;
use App\Order\Validator\FixedAddressValidator;

class FixedAddressQuery
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly UserBag $userBag,
        private readonly FixedAddressValidator $addressValidator,
    )
    { }

    public function getFixedAddress(int $id): FixedAddressDto
    {
        $address = $this->addressRepository->findOneBy(['id' => $id, 'customerId' => $this->userBag->getCustomerId()]);
        $this->addressValidator->validateExists($address);

        return FixedAddressDto::fromEntity($address);
    }

    /**
     * @return FixedAddressDto[]
     */
    public function getAllFixedAddress(): array
    {
        $addresses = $this->addressRepository->findBy(['customerId' => $this->userBag->getCustomerId()]);

        $result = [];
        foreach ($addresses as $address) {
            $result[$address->getId()] = FixedAddressDto::fromEntity($address);
        }

        return $result;
    }
}

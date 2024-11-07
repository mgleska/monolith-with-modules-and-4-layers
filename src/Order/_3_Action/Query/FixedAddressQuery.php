<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Query;

use App\Order\_2_Export\Dto\FixedAddress\FixedAddressDto;
use App\Order\_2_Export\Query\GetAllFixedAddressesInterface;
use App\Order\_2_Export\Query\GetFixedAddressInterface;
use App\Order\_3_Action\Validator\FixedAddressValidator;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;

class FixedAddressQuery implements GetAllFixedAddressesInterface, GetFixedAddressInterface
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly FixedAddressValidator $addressValidator,
    ) {
    }

    public function getFixedAddress(int $id): FixedAddressDto
    {
        $address = $this->addressRepository->find($id);
        $this->addressValidator->validateExists($address);

        return FixedAddressDto::fromEntity($address);
    }

    /**
     * @return FixedAddressDto[]
     */
    public function getAllFixedAddresses(): array
    {
        $addresses = $this->addressRepository->findAll();

        $result = [];
        foreach ($addresses as $address) {
            $result[$address->getId()] = FixedAddressDto::fromEntity($address);
        }

        return $result;
    }
}

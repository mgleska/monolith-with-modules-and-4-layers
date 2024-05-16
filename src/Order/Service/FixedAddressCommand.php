<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Auth\Export\UserBag;
use App\Order\Dto\CreateFixedAddressDto;
use App\Order\Entity\FixedAddress;
use App\Order\Repository\FixedAddressRepository;
use App\Order\Validator\FixedAddressValidator;

class FixedAddressCommand
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly UserBag $userBag,
        private readonly FixedAddressValidator $validator,
    )
    { }

    public function createFixedAddress(CreateFixedAddressDto $dto): int
    {
        $this->validator->validateExternalIdNotUsed($dto->externalId);

        $address = new FixedAddress();
        $address
            ->setCustomerId($this->userBag->getCustomerId())
            ->setExternalId($dto->externalId)
            ->setNameCompanyOrPerson($dto->nameCompanyOrPerson)
            ->setAddress($dto->address)
            ->setCity($dto->city)
            ->setZipCode($dto->zipCode);

        $this->addressRepository->save($address, true);

        return $address->getId();
    }
}

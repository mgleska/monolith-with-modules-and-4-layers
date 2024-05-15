<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Api\Export\CustomApiException;
use App\Auth\Export\UserBag;
use App\Order\Dto\CreateFixedAddressDto;
use App\Order\Entity\FixedAddress;
use App\Order\Repository\FixedAddressRepository;

class FixedAddressCommand
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly UserBag $userBag,
    )
    { }

    public function createFixedAddress(CreateFixedAddressDto $dto): int
    {
        $address = $this->addressRepository->findOneBy(['customerId' => $this->userBag->getCustomerId(), 'externalId' => $dto->externalId]);
        if ($address !== null) {
            throw new CustomApiException('DUPLICATE_ADDRESS');
        }

        $address = new FixedAddress();
        $address->setCustomerId($this->userBag->getCustomerId());
        $address->setExternalId($dto->externalId);
        $address->setNameCompanyOrPerson($dto->nameCompanyOrPerson);
        $address->setAddress($dto->address);
        $address->setCity($dto->city);
        $address->setZipCode($dto->zipCode);

        $this->addressRepository->save($address, true);

        return $address->getId();
    }
}

<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Customer\Export\ValidateIdInterface as CustomerValidator;
use App\Order\Entity\FixedAddress;
use App\Order\Export\Dto\FixedAddress\CreateFixedAddressDto;
use App\Order\Repository\FixedAddressRepository;
use App\Order\Validator\FixedAddressValidator;

class FixedAddressCommand
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly FixedAddressValidator $validator,
        private readonly CustomerValidator $customerValidator,
    )
    { }

    public function createFixedAddress(CreateFixedAddressDto $dto): int
    {
        $this->customerValidator->validateId($dto->customerId);
        $this->validator->validateExternalIdNotUsed($dto->customerId, $dto->externalId);

        $address = new FixedAddress();
        $address
            ->setCustomerId($dto->customerId)
            ->setExternalId($dto->externalId)
            ->setNameCompanyOrPerson($dto->nameCompanyOrPerson)
            ->setAddress($dto->address)
            ->setCity($dto->city)
            ->setZipCode($dto->zipCode);

        $this->addressRepository->save($address, true);

        return $address->getId();
    }
}

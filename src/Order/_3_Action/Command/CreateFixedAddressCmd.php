<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Command;

use App\CommonInfrastructure\GenericDtoValidator;
use App\Order\_2_Export\Command\CreateFixedAddressInterface;
use App\Order\_2_Export\Dto\FixedAddress\CreateFixedAddressDto;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Validator\CustomerValidator;
use App\Order\_3_Action\Validator\FixedAddressValidator;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;

class CreateFixedAddressCmd implements CreateFixedAddressInterface
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly FixedAddressValidator $validator,
        private readonly CustomerValidator $customerValidator,
        private readonly GenericDtoValidator $dtoValidator,
    ) {
    }

    public function createFixedAddress(CreateFixedAddressDto $dto): int
    {
        $this->dtoValidator->validate($dto, __FUNCTION__);

        $this->customerValidator->validateCustomerId($dto->customerId);
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

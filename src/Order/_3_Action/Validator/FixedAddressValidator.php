<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Validator;

use App\Api\_2_Export\ApiProblemException;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Enum\ApiProblemTypeEnum;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use Symfony\Component\HttpFoundation\Response;

class FixedAddressValidator
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
    ) {
    }

    public function validateExists(?FixedAddress $address): void
    {
        if ($address === null) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_FIXEDADDRESS_NOT_FOUND'
            );
        }
    }

    public function validateExternalIdNotUsed(int $customerId, string $externalId): void
    {
        $address = $this->addressRepository->findOneBy(['customerId' => $customerId, 'externalId' => $externalId]);
        if ($address !== null) {
            throw new ApiProblemException(
                Response::HTTP_PRECONDITION_FAILED,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_FIXEDADDRESS_EXTERNAL_ID_ALREADY_EXIST'
            );
        }
    }
}

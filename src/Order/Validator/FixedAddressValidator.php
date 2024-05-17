<?php

declare(strict_types=1);

namespace App\Order\Validator;

use App\Api\Export\ApiProblemException;
use App\Auth\Export\UserBag;
use App\Order\Api\ApiProblemTypeEnum;
use App\Order\Entity\FixedAddress;
use App\Order\Repository\FixedAddressRepository;
use Symfony\Component\HttpFoundation\Response;

class FixedAddressValidator
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly UserBag $userBag,
    )
    { }

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

    public function validateExternalIdNotUsed(string $externalId): void
    {
        $address = $this->addressRepository->findOneBy(['customerId' => $this->userBag->getCustomerId(), 'externalId' => $externalId]);
        if ($address !== null) {
            throw new ApiProblemException(
                Response::HTTP_PRECONDITION_FAILED,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_FIXEDADDRESS_EXTERNAL_ID_ALREADY_EXIST'
            );
        }
    }
}

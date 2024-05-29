<?php

declare(strict_types=1);

namespace App\Order\Validator;

use App\Api\Export\ApiProblemException;
use App\Auth\Export\UserBag;
use App\Order\Entity\FixedAddress;
use App\Order\Entity\Order;
use App\Order\Enum\ApiProblemTypeEnum;
use App\Order\Export\Dto\Order\OrderAddressDto;
use App\Order\Repository\FixedAddressRepository;
use App\Order\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;

class OrderValidator
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly UserBag $userBag,
        private readonly FixedAddressRepository $addressRepository,
    ) {
    }

    /**
     * @throws ApiProblemException
     */
    public function validateExists(?Order $order): void
    {
        if ($order === null) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_ORDER_NOT_FOUND'
            );
        }
    }

    /**
     * @throws ApiProblemException
     */
    public function validateHasAccess(int $orderId): void
    {
        $c = $this->orderRepository->count(['id' => $orderId, 'customerId' => $this->userBag->getCustomerId()]);

        if ($c === 0) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_ORDER_NOT_FOUND'
            );
        }
    }

    public function validateLoadingAddressForCreate(?string $fixedAddressExternalId, ?OrderAddressDto $address): ?FixedAddress
    {
        if ($fixedAddressExternalId === null && $address === null) {
            throw new ApiProblemException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ApiProblemTypeEnum::CREATE->value,
                'ORDER_CREATE_LOADING_ADDRESS_NOT_SPECIFIED'
            );
        }

        if ($fixedAddressExternalId !== null && $address !== null) {
            throw new ApiProblemException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ApiProblemTypeEnum::CREATE->value,
                'ORDER_CREATE_LOADING_ADDRESS_SPECIFIED_BY_EXTERNAL_ID_AND_BY_VALUE'
            );
        }

        if ($fixedAddressExternalId !== null) {
            $address = $this->addressRepository->findOneBy(
                ['customerId' => $this->userBag->getCustomerId(), 'externalId' => $fixedAddressExternalId]
            );
            if ($address !== null) {
                return $address;
            }
            throw new ApiProblemException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ApiProblemTypeEnum::CREATE->value,
                'ORDER_CREATE_LOADING_ADDRESS_NOT_FOUND'
            );
        }

        return null;
    }
}

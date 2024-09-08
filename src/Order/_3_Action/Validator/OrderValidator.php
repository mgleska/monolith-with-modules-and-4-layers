<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Validator;

use App\Api\_2_Export\ApiProblemException;
use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Dto\Order\OrderAddressDto;
use App\Order\_3_Action\Enum\ApiProblemTypeEnum;
use App\Order\_4_Infrastructure\Entity\FixedAddressEntity;
use App\Order\_4_Infrastructure\Entity\OrderEntity;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;

class OrderValidator
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly UserBagInterface $userBag,
        private readonly FixedAddressRepository $addressRepository,
    ) {
    }

    /**
     * @throws ApiProblemException
     */
    public function validateExists(?OrderEntity $order): void
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

    public function validateLoadingAddressForCreate(?string $fixedAddressExternalId, ?OrderAddressDto $address): ?FixedAddressEntity
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

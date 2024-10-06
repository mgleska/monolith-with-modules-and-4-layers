<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Validator;

use App\Api\_2_Export\ApiProblemException;
use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Dto\Order\OrderAddressDto;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Enum\ApiProblemTypeEnum;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use Symfony\Component\HttpFoundation\Response;

class OrderValidator
{
    public function __construct(
        private readonly OrderHeaderRepository $orderHeaderRepository,
        private readonly UserBagInterface $userBag,
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

    public function validateHasAccess(Order $order): void
    {
        if ($order->getHeader()->getCustomerId() !== $this->userBag->getCustomerId()) {
            throw new ApiProblemException(
                Response::HTTP_FORBIDDEN,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_ORDER_NO_ACCESS'
            );
        }
    }

    /**
     * @throws ApiProblemException
     */
    public function validateHasAccessById(int $orderId): void
    {
        $c = $this->orderHeaderRepository->count(['id' => $orderId, 'customerId' => $this->userBag->getCustomerId()]);

        if ($c === 0) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'ORDER_ORDER_NOT_FOUND'
            );
        }
    }

    public function validateLoadingAddressForCreate(?FixedAddress $fixedAddress, ?OrderAddressDto $address): void
    {
        if ($fixedAddress === null && $address === null) {
            throw new ApiProblemException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ApiProblemTypeEnum::CREATE->value,
                'ORDER_CREATE_LOADING_ADDRESS_NOT_SPECIFIED'
            );
        }

        if ($fixedAddress !== null && $address !== null) {
            throw new ApiProblemException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                ApiProblemTypeEnum::CREATE->value,
                'ORDER_CREATE_LOADING_ADDRESS_SPECIFIED_BY_EXTERNAL_ID_AND_BY_VALUE'
            );
        }
    }
}

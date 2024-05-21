<?php

declare(strict_types=1);

namespace App\Order\Validator;

use App\Api\Export\ApiProblemException;
use App\Auth\Export\UserBag;
use App\Order\Entity\Order;
use App\Order\Enum\ApiProblemTypeEnum;
use App\Order\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;

class OrderValidator
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly UserBag $userBag,
    )
    {}

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
}

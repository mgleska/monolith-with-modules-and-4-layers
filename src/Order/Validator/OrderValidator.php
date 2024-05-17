<?php

declare(strict_types=1);

namespace App\Order\Validator;

use App\Api\Export\Exception\ApiProblemException;
use App\Auth\Export\UserBag;
use App\Order\Api\ApiProblemType;
use App\Order\Entity\Order;
use App\Order\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;

class OrderValidator
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly UserBag $userBag,
    )
    {}

    public function validateExists(?Order $order): void
    {
        if ($order === null) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemType::VALIDATOR->value,
                'ORDER_ORDER_NOT_FOUND'
            );
        }
    }

    public function validateHasAccess(int $orderId): void
    {
        $c = $this->orderRepository->count(['id' => $orderId, 'customerId' => $this->userBag->getCustomerId()]);

        if ($c === 0) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemType::VALIDATOR->value,
                'ORDER_ORDER_NOT_FOUND'
            );
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Query;

use App\CommonInfrastructure\Api\ApiProblemException;
use App\Order\_2_Export\Dto\Order\OrderDto;
use App\Order\_2_Export\Query\GetOrderInterface;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use Exception;

class OrderQuery implements GetOrderInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderValidator $orderValidator,
    ) {
    }

    /**
     * @throws ApiProblemException
     * @throws Exception
     */
    public function getOrder(int $id): OrderDto
    {
        $order = $this->orderRepository->find($id);
        $this->orderValidator->validateExists($order);

        return OrderDto::fromEntity($order);
    }
}

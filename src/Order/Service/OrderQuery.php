<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Auth\Export\UserBag;
use App\Order\Dto\OrderDto;
use App\Order\Dto\OrderLineDto;
use App\Order\Repository\OrderLineRepository;
use App\Order\Repository\OrderRepository;
use App\Order\Repository\OrderSsccRepository;
use App\Order\Validator\OrderValidator;

class OrderQuery
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderLineRepository $lineRepository,
        private readonly OrderSsccRepository $ssccRepository,
        private readonly UserBag $userBag,
        private readonly OrderValidator $orderValidator,
    )
    { }

    public function getOrder(int $id): OrderDto
    {
        $order = $this->orderRepository->findOneBy(['id' => $id, 'customerId' => $this->userBag->getCustomerId()]);
        $this->orderValidator->validateExists($order);

        $lines = $this->lineRepository->findBy(['order' => $order, 'customerId' => $this->userBag->getCustomerId()]);
        $linesArray = [];
        foreach ($lines as $line) {
            $linesArray[] = OrderLineDto::fromEntity($line);
        }

        return OrderDto::fromEntity($order, $linesArray);
    }
}

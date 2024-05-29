<?php

declare(strict_types=1);

namespace App\Order\Query;

use App\Api\Export\ApiProblemException;
use App\Auth\Export\UserBag;
use App\Order\Export\Dto\Order\OrderDto;
use App\Order\Repository\OrderLineRepository;
use App\Order\Repository\OrderRepository;
use App\Order\Repository\OrderSsccRepository;
use App\Order\Validator\OrderValidator;
use Exception;

class OrderQuery
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderLineRepository $lineRepository,
        private readonly OrderSsccRepository $ssccRepository,
        private readonly UserBag $userBag,
        private readonly OrderValidator $orderValidator,
    ) {
    }

    /**
     * @throws ApiProblemException
     * @throws Exception
     */
    public function getOrder(int $id): OrderDto
    {
        $order = $this->orderRepository->findOneBy(['id' => $id, 'customerId' => $this->userBag->getCustomerId()]);
        $this->orderValidator->validateExists($order);

        $lines = $this->lineRepository->findBy(['order' => $order, 'customerId' => $this->userBag->getCustomerId()]);
        $ssccs = $this->ssccRepository->findBy(['order' => $order, 'customerId' => $this->userBag->getCustomerId()]);

        return OrderDto::fromEntity($order, $lines, $ssccs);
    }
}

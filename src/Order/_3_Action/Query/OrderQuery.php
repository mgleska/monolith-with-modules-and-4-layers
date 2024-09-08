<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Query;

use App\Api\_2_Export\ApiProblemException;
use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Dto\Order\OrderDto;
use App\Order\_2_Export\Query\GetOrderInterface;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\OrderLineRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use App\Order\_4_Infrastructure\Repository\OrderSsccRepository;
use Exception;

class OrderQuery implements GetOrderInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderLineRepository $lineRepository,
        private readonly OrderSsccRepository $ssccRepository,
        private readonly UserBagInterface $userBag,
        private readonly OrderValidator $orderValidator,
    ) {
    }

    /**
     * @throws ApiProblemException
     * @throws Exception
     */
    public function getOrder(int $id, bool $isValidated = false): OrderDto
    {
        $order = $this->orderRepository->findOneBy(['id' => $id, 'customerId' => $this->userBag->getCustomerId()]);
        $this->orderValidator->validateExists($order);

        $lines = $this->lineRepository->findBy(['order' => $order, 'customerId' => $this->userBag->getCustomerId()]);
        $ssccs = $this->ssccRepository->findBy(['order' => $order, 'customerId' => $this->userBag->getCustomerId()]);

        return OrderDto::fromEntity($order, $lines, $ssccs);
    }
}

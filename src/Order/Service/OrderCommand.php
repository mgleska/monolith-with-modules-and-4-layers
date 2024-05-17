<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Enum\OrderStatusEnum;
use App\Order\Repository\OrderRepository;
use App\Order\Validator\OrderValidator;
use Doctrine\DBAL\Exception as DBALException;

class OrderCommand
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderValidator $validator,
    )
    { }

    /**
     * @return array{bool, string}
     * @throws DBALException
     */
    public function sendOrder(int $orderId): array
    {
        $this->validator->validateHasAccess($orderId);

        $ok = $this->orderRepository->changeStatus($orderId, OrderStatusEnum::NEW, OrderStatusEnum::SENT);
        if ($ok) {
            return [true, ''];
        }
        else {
            return [false, 'ORDER_ORDER_STATUS_NOT_VALID_FOR_SEND'];
        }
    }
}

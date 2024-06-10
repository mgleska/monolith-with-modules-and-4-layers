<?php

declare(strict_types=1);

namespace App\Order\Command;

use App\Order\Enum\OrderStatusEnum;
use App\Order\Repository\OrderRepository;
use App\Order\Validator\OrderValidator;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface;

class SendOrderCmd
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderValidator $validator,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array{bool, string}
     * @throws DBALException
     */
    public function sendOrder(int $orderId): array
    {
        $this->validator->validateHasAccess($orderId);

        $ok = $this->orderRepository->changeStatus($orderId, OrderStatusEnum::NEW, OrderStatusEnum::SENT);
        if ($ok) {
            $this->logger->info('Order with id {id} sent.', ['id' => $orderId]);
            return [true, ''];
        } else {
            return [false, 'ORDER_STATUS_NOT_VALID_FOR_SEND'];
        }
    }
}

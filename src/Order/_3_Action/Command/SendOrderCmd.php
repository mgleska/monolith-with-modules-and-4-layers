<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Command;

use App\Order\_2_Export\Command\SendOrderInterface;
use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface;

class SendOrderCmd implements SendOrderInterface
{
    public function __construct(
        private readonly OrderHeaderRepository $orderHeaderRepository,
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
        $this->validator->validateHasAccessById($orderId);

        $ok = $this->orderHeaderRepository->testAndChangeStatus($orderId, OrderStatusEnum::NEW, OrderStatusEnum::SENT);
        if ($ok) {
            $this->logger->info('Order with id {id} sent.', ['id' => $orderId]);
            return [true, ''];
        } else {
            return [false, 'ORDER_STATUS_NOT_VALID_FOR_SEND'];
        }
    }
}

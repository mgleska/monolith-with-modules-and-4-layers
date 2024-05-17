<?php

declare(strict_types=1);

namespace App\Printer\Service;

use App\Order\Repository\OrderRepository;
use App\Customer\Repository;
use Psr\Log\LoggerInterface;
use App\Order\Validator as OrderValidator;

class BadService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly PrintCommand $command,
        private readonly LoggerInterface $logger,
    )
    {}

    public function crossModuleCall(int $id): void
    {
        $order = $this->orderRepository->find($id);
        $this->command->printLabel($order->getId());
        $this->logger->alert('forbidden!');
    }
}

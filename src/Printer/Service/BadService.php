<?php

declare(strict_types=1);

namespace App\Printer\Service;

use App\Order\Repository\OrderRepository;
use App\Customer\Repository;
use App\Printer\Export\Dto\PrintLabelDto;
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
        $this->command->printLabel(new PrintLabelDto(), false);
        $this->logger->alert('forbidden!');
    }
}

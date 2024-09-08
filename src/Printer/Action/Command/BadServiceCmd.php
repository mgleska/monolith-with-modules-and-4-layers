<?php

declare(strict_types=1);

namespace App\Printer\Action\Command;

/*
 * To see how module boundary checker works, just uncomment all lines marked with // and run "composer run-script check"
 */

// use App\Customer\_4_Infrastructure\Repository;
// use App\Order\_4_Infrastructure\Repository\OrderRepository;
// use App\Order\_3_Action\Validator as OrderValidator;
use Psr\Log\LoggerInterface;

class BadServiceCmd
{
    public function __construct(
        // private readonly OrderRepository $orderRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function crossModuleCall(int $id): void
    {
        // $order = $this->orderRepository->find($id);
        $this->logger->alert('forbidden!');
    }
}

<?php

declare(strict_types=1);

namespace App\Printer\Command;

/*
 * To see how module boundary checker works, just uncomment all lines marked with // and run "composer run-script check"
 */

//use App\Customer\Repository;
//use App\Order\Repository\OrderRepository;
//use App\Order\Validator as OrderValidator;
use Psr\Log\LoggerInterface;

class BadService
{
    public function __construct(
        //private readonly OrderRepository $orderRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function crossModuleCall(int $id): void
    {
        //$order = $this->orderRepository->find($id);
        $this->logger->alert('forbidden!');
    }
}

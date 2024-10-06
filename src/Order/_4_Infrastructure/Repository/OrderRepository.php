<?php

declare(strict_types=1);

namespace App\Order\_4_Infrastructure\Repository;

use App\Order\_3_Action\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class OrderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderHeaderRepository $headerRepository,
        private readonly OrderLineRepository $lineRepository,
        private readonly OrderSsccRepository $ssccRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function storeNew(Order $order): void
    {
        try {
            $this->entityManager->beginTransaction();
            $this->headerRepository->save($order->getHeader(), true);

            foreach ($order->getLines() as $line) {
                $this->lineRepository->save($line);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function get(int $id, bool $withLines = false, bool $withSsccs = false): ?Order
    {
        $header = $this->headerRepository->find($id);
        if ($header === null) {
            return null;
        }

        if ($withLines) {
            $lines = $this->lineRepository->findBy(['orderHeader' => $header]);
        }

        if ($withSsccs) {
            $ssccs = $this->ssccRepository->findBy(['orderHeader' => $header]);
        }

        return new Order($header, $lines ?? [], $ssccs ?? []);
    }
}

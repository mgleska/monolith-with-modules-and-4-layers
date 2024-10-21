<?php

declare(strict_types=1);

namespace App\Order\_4_Infrastructure\Repository;

use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Entity\OrderLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @throws Exception
     */
    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $entity->incrementVersion();
            if ($this->getEntityManager()->getConnection()->isTransactionActive()) {
                try {
                    $this->getEntityManager()->flush();
                    $this->getEntityManager()->commit();
                } catch (Exception $e) {
                    $this->getEntityManager()->rollback();
                    throw $e;
                }
            } else {
                $this->getEntityManager()->flush();
            }
        }
    }

    public function removeLine(OrderLine $line): void
    {
        $this->getEntityManager()->remove($line);
    }

    public function getWithLock(int $id): Order|null
    {
        if (! $this->getEntityManager()->getConnection()->isTransactionActive()) {
            $this->getEntityManager()->beginTransaction();
        }

        return $this->find($id, LockMode::PESSIMISTIC_WRITE);
    }
}

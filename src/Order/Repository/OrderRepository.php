<?php

declare(strict_types=1);

namespace App\Order\Repository;

use App\Order\Entity\Order;
use App\Order\Enum\OrderStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws DBALException
     */
    public function changeStatus(int $orderId, OrderStatusEnum $fromStatus, OrderStatusEnum $toStatus): bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            UPDATE ord_order SET status = :toStatus
            WHERE id = :orderId
            AND status = :fromStatus
        ';
        $countUpdated = $conn->executeStatement($sql, ['orderId' => $orderId, 'fromStatus' => $fromStatus->value, 'toStatus' => $toStatus->value]);

        return $countUpdated > 0;
    }
}

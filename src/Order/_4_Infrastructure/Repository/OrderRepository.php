<?php

declare(strict_types=1);

namespace App\Order\_4_Infrastructure\Repository;

use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_4_Infrastructure\Entity\OrderEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderEntity>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderEntity::class);
    }

    public function save(OrderEntity $entity, bool $flush = false): void
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

    public function getStatus(int $orderId): OrderStatusEnum
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.status')
            ->from(OrderEntity::class, 'o')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId);

        return OrderStatusEnum::from($qb->getQuery()->getSingleScalarResult());
    }
}

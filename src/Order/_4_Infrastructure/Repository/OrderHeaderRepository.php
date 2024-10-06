<?php

declare(strict_types=1);

namespace App\Order\_4_Infrastructure\Repository;

use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Entity\OrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderHeader>
 */
class OrderHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderHeader::class);
    }

    public function save(OrderHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws DBALException
     */
    public function testAndChangeStatus(int $orderId, OrderStatusEnum $fromStatus, OrderStatusEnum $toStatus): bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            UPDATE ord_order_header SET status = :toStatus
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
            ->from(OrderHeader::class, 'o')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId);

        return OrderStatusEnum::from($qb->getQuery()->getSingleScalarResult());
    }
}

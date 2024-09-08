<?php

declare(strict_types=1);

namespace App\Order\_4_Infrastructure\Repository;

use App\Order\_4_Infrastructure\Entity\OrderSsccEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderSsccEntity>
 */
class OrderSsccRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderSsccEntity::class);
    }

    public function save(OrderSsccEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

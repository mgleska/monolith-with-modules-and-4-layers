<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Order\Repository\OrderSsccRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function sprintf;

#[ORM\Entity(repositoryClass: OrderSsccRepository::class)]
#[ORM\Table(name: "ord_order_sscc")]
class OrderSscc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $customerId;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    private Order $order;

    #[ORM\Column(type: Types::BIGINT)]
    private int $code;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): OrderSscc
    {
        $this->order = $order;
        return $this;
    }

    public function getCode(): string
    {
        return sprintf('%018d', $this->code);
    }

    public function setCode(string $code): OrderSscc
    {
        $this->code = (int)$code;
        return $this;
    }
}

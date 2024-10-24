<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Entity;

use App\Order\_4_Infrastructure\Repository\OrderSsccRepository;
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
    private OrderHeader $orderHeader;

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

    public function getOrderHeader(): OrderHeader
    {
        return $this->orderHeader;
    }

    public function setOrderHeader(OrderHeader $orderHeader): static
    {
        $this->orderHeader = $orderHeader;
        return $this;
    }

    public function getCode(): string
    {
        return sprintf('%018d', $this->code);
    }

    public function setCode(string $code): static
    {
        $this->code = (int)$code;
        return $this;
    }
}

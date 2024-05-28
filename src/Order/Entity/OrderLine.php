<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Order\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
#[ORM\Table(name: "ord_order_line")]
class OrderLine
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

    #[ORM\Column]
    private int $quantity;

    // pallet length in [cm]
    #[ORM\Column]
    private int $length;

    // pallet width in [cm]
    #[ORM\Column]
    private int $width;

    // pallet height in [cm]
    #[ORM\Column]
    private int $height;

    // pallet weight in [kg] multiplied by 100
    #[ORM\Column]
    private int $weightOnePallet;

    // total weight of all pallets of order line, in [kg] multiplied by 100
    #[ORM\Column]
    private int $weightTotal;

    #[ORM\Column(length: 250)]
    private string $goodsDescription;

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

    public function setOrder(Order $order): OrderLine
    {
        $this->order = $order;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): OrderLine
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): OrderLine
    {
        $this->length = $length;
        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): OrderLine
    {
        $this->width = $width;
        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): OrderLine
    {
        $this->height = $height;
        return $this;
    }

    public function getWeightOnePallet(): int
    {
        return $this->weightOnePallet;
    }

    public function setWeightOnePallet(int $weightOnePallet): OrderLine
    {
        $this->weightOnePallet = $weightOnePallet;
        return $this;
    }

    public function getWeightTotal(): int
    {
        return $this->weightTotal;
    }

    public function setWeightTotal(int $weightTotal): OrderLine
    {
        $this->weightTotal = $weightTotal;
        return $this;
    }

    public function getGoodsDescription(): string
    {
        return $this->goodsDescription;
    }

    public function setGoodsDescription(string $goodsDescription): OrderLine
    {
        $this->goodsDescription = $goodsDescription;
        return $this;
    }
}

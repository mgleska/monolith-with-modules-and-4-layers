<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Entity;

class Order // Aggregate
{
    // root of the aggregate
    private OrderHeader $header;

    /**
     * @var OrderLine[] $lines
     */
    private array $lines;

    /**
     * @var OrderSscc[]
     */
    private array $ssccs;

    /**
     * @param OrderHeader $header
     * @param OrderLine[] $lines
     * @param OrderSscc[] $ssccs
     */
    public function __construct(OrderHeader $header, array $lines = [], array $ssccs = [])
    {
        $this->header = $header;
        $this->lines = $lines;
        $this->ssccs = $ssccs;
    }

    public function getId(): int
    {
        return $this->header->getId();
    }

    public function getHeader(): OrderHeader
    {
        return $this->header;
    }

    public function setHeader(OrderHeader $header): static
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return OrderLine[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @param OrderLine[] $lines
     */
    public function setLines(array $lines): static
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * @return OrderSscc[]
     */
    public function getSsccs(): array
    {
        return $this->ssccs;
    }

    /**
     * @param OrderSscc[] $ssccs
     */
    public function setSsccs(array $ssccs): static
    {
        $this->ssccs = $ssccs;

        return $this;
    }
}

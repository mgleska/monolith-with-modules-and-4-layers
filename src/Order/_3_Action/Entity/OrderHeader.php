<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Entity;

use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderHeaderRepository::class)]
#[ORM\Table(name: "ord_order_header")]
class OrderHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $customerId;

    #[ORM\Column(length: 50)]
    private string $number;

    #[ORM\Column(length: 20, enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $status;

    #[ORM\Column]
    private int $quantityTotal;

    #[ORM\Column(type: 'date')]
    private DateTime $loadingDate;

    #[ORM\Column(length: 250)]
    private string $loadingNameCompanyOrPerson;

    #[ORM\Column(length: 250)]
    private string $loadingAddress;

    #[ORM\Column(length: 250)]
    private string $loadingCity;

    #[ORM\Column(length: 50)]
    private string $loadingZipCode;

    #[ORM\Column(length: 250)]
    private string $loadingContactPerson;

    #[ORM\Column(length: 250)]
    private string $loadingContactPhone;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $loadingContactEmail = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $loadingFixedAddressExternalId = null;

    #[ORM\Column(length: 250)]
    private string $deliveryNameCompanyOrPerson;

    #[ORM\Column(length: 250)]
    private string $deliveryAddress;

    #[ORM\Column(length: 250)]
    private string $deliveryCity;

    #[ORM\Column(length: 50)]
    private string $deliveryZipCode;

    #[ORM\Column(length: 250)]
    private string $deliveryContactPerson;

    #[ORM\Column(length: 250)]
    private string $deliveryContactPhone;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $deliveryContactEmail = null;

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

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrderStatusEnum $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getQuantityTotal(): int
    {
        return $this->quantityTotal;
    }

    public function setQuantityTotal(int $quantityTotal): static
    {
        $this->quantityTotal = $quantityTotal;
        return $this;
    }

    public function getLoadingNameCompanyOrPerson(): string
    {
        return $this->loadingNameCompanyOrPerson;
    }

    public function setLoadingNameCompanyOrPerson(string $loadingNameCompanyOrPerson): static
    {
        $this->loadingNameCompanyOrPerson = $loadingNameCompanyOrPerson;
        return $this;
    }

    public function getLoadingAddress(): string
    {
        return $this->loadingAddress;
    }

    public function setLoadingAddress(string $loadingAddress): static
    {
        $this->loadingAddress = $loadingAddress;
        return $this;
    }

    public function getLoadingCity(): string
    {
        return $this->loadingCity;
    }

    public function setLoadingCity(string $loadingCity): static
    {
        $this->loadingCity = $loadingCity;
        return $this;
    }

    public function getLoadingZipCode(): string
    {
        return $this->loadingZipCode;
    }

    public function setLoadingZipCode(string $loadingZipCode): static
    {
        $this->loadingZipCode = $loadingZipCode;
        return $this;
    }

    public function getLoadingContactPerson(): string
    {
        return $this->loadingContactPerson;
    }

    public function setLoadingContactPerson(string $loadingContactPerson): static
    {
        $this->loadingContactPerson = $loadingContactPerson;
        return $this;
    }

    public function getLoadingContactPhone(): string
    {
        return $this->loadingContactPhone;
    }

    public function setLoadingContactPhone(string $loadingContactPhone): static
    {
        $this->loadingContactPhone = $loadingContactPhone;
        return $this;
    }

    public function getLoadingContactEmail(): ?string
    {
        return $this->loadingContactEmail;
    }

    public function setLoadingContactEmail(?string $loadingContactEmail): static
    {
        $this->loadingContactEmail = $loadingContactEmail;
        return $this;
    }

    public function getDeliveryNameCompanyOrPerson(): string
    {
        return $this->deliveryNameCompanyOrPerson;
    }

    public function setDeliveryNameCompanyOrPerson(string $deliveryNameCompanyOrPerson): static
    {
        $this->deliveryNameCompanyOrPerson = $deliveryNameCompanyOrPerson;
        return $this;
    }

    public function getDeliveryAddress(): string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    public function getDeliveryCity(): string
    {
        return $this->deliveryCity;
    }

    public function setDeliveryCity(string $deliveryCity): static
    {
        $this->deliveryCity = $deliveryCity;
        return $this;
    }

    public function getDeliveryZipCode(): string
    {
        return $this->deliveryZipCode;
    }

    public function setDeliveryZipCode(string $deliveryZipCode): static
    {
        $this->deliveryZipCode = $deliveryZipCode;
        return $this;
    }

    public function getDeliveryContactPerson(): string
    {
        return $this->deliveryContactPerson;
    }

    public function setDeliveryContactPerson(string $deliveryContactPerson): static
    {
        $this->deliveryContactPerson = $deliveryContactPerson;
        return $this;
    }

    public function getDeliveryContactPhone(): string
    {
        return $this->deliveryContactPhone;
    }

    public function setDeliveryContactPhone(string $deliveryContactPhone): static
    {
        $this->deliveryContactPhone = $deliveryContactPhone;
        return $this;
    }

    public function getDeliveryContactEmail(): ?string
    {
        return $this->deliveryContactEmail;
    }

    public function setDeliveryContactEmail(?string $deliveryContactEmail): static
    {
        $this->deliveryContactEmail = $deliveryContactEmail;
        return $this;
    }

    public function getLoadingFixedAddressExternalId(): ?string
    {
        return $this->loadingFixedAddressExternalId;
    }

    public function setLoadingFixedAddressExternalId(?string $loadingFixedAddressExternalId): static
    {
        $this->loadingFixedAddressExternalId = $loadingFixedAddressExternalId;
        return $this;
    }

    public function getLoadingDate(): DateTime
    {
        return $this->loadingDate;
    }

    public function setLoadingDate(DateTime $loadingDate): static
    {
        $this->loadingDate = $loadingDate;
        return $this;
    }
}

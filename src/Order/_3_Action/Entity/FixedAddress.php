<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Entity;

use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FixedAddressRepository::class)]
#[ORM\Table(name: "ord_fixed_address")]
class FixedAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $customerId;

    #[ORM\Column(length: 100)]
    private string $externalId;

    #[ORM\Column(length: 250)]
    private string $nameCompanyOrPerson;

    #[ORM\Column(length: 250)]
    private string $address;

    #[ORM\Column(length: 250)]
    private string $city;

    #[ORM\Column(length: 50)]
    private string $zipCode;

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

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getNameCompanyOrPerson(): string
    {
        return $this->nameCompanyOrPerson;
    }

    public function setNameCompanyOrPerson(string $nameCompanyOrPerson): static
    {
        $this->nameCompanyOrPerson = $nameCompanyOrPerson;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }
}

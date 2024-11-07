<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Entity;

use App\Customer\_4_Infrastructure\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: "cst_customer")]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $version;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 50)]
    private string $dbNameSuffix;

    public function __construct()
    {
        $this->version = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDbNameSuffix(): string
    {
        return $this->dbNameSuffix;
    }

    public function setDbNameSuffix(string $dbNameSuffix): void
    {
        $this->dbNameSuffix = $dbNameSuffix;
    }

    public function incrementVersion(): void
    {
        $this->version += 1;
    }
}

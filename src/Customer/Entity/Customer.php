<?php

namespace App\Customer\Entity;

use App\Customer\Repository\BbbRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BbbRepository::class)]
#[ORM\Table(name: "cst_customer")]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name = '';

    public function getId(): int
    {
        return $this->id;
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
}

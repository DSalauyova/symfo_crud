<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
//validateurs 
//Les assert permettent de spécifier, directement au niveau des entités, quel est le format attendu pour chaque donnée grâce aux annotations. sinon erreur
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[UniqueEntity('name')]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    //données non vides
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $name = null;

    #[ORM\Column(type: 'float')]
    //valeur n'est pas absente
    #[Assert\NotNull()]
    #[Assert\Positive()]
    private ?float $quantity = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    #[Assert\Positive()]
    #[Assert\LessThan(200)]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'Ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * [__construct description]
     *
     * @return  [type]  [return description]
     */
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}

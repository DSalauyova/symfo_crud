<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('username')]

#[ORM\Entity(repositoryClass: UserRepository::class)]
// #[ORM\EntityListeners(['App\EntityListener\UserListener'])]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 80)]
    private ?string $username = null;

    #[ORM\Column]
    #[Assert\Positive()]
    #[Assert\Length(min: 4, max: 4)]
    private ?int $agent_number = null;

    #[ORM\Column(length: 180)]
    #[Assert\Length(min: 5, max: 180)]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private array $roles = [];

    //ajouté, pas mettre le decore de ORM pour pas que le pass non encodé se trouve dansla bd
    private ?string $plainPassword = null;
    private ?string $newPassword = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank()]
    private ?string $password = "null";

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ingredient::class, orphanRemoval: true)]
    private Collection $Ingredients;

    //methode magique
    public function __construct()
    {
        //quand l'objet est instancié, son createdAt est nouvelle DateTimeImm
        $this->createdAt = new \DateTimeImmutable();
        $this->Ingredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
    public function getAgentNumber(): ?int
    {
        return $this->agent_number;
    }

    public function setAgentNumber(int $agent_number): static
    {
        $this->agent_number = $agent_number;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the value of newPassword
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * Set the value of newPassword
     *
     * @return  self
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->Ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->Ingredients->contains($ingredient)) {
            $this->Ingredients->add($ingredient);
            $ingredient->setUser($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        if ($this->Ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getUser() === $this) {
                $ingredient->setUser(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Food::class, mappedBy: 'user')]
    private Collection $cart;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FoodComment::class)]
    private Collection $foodComments;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    public function __toString(): string
    {
        return (string) $this->getEmail();
    }

    public function __construct()
    {
        $this->cart = new ArrayCollection();
        $this->foodComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getCart(): Collection
    {
        return $this->cart;
    }

    public function addCart(Food $cart): self
    {
            $this->cart->add($cart);
            $cart->addUser($this);

        return $this;
    }

    public function removeCart(Food $cart): self
    {
        if ($this->cart->removeElement($cart)) {
            $cart->removeUser($this);
        }

        return $this;
    }

    public function emptyCart(): self
    {
        foreach ($this->cart as $key => $value) {
            if ($this->cart->removeElement($value)) {
                $value->removeUser($this);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FoodComment>
     */
    public function getFoodComments(): Collection
    {
        return $this->foodComments;
    }

    public function addFoodComment(FoodComment $foodComment): self
    {
        if (!$this->foodComments->contains($foodComment)) {
            $this->foodComments->add($foodComment);
            $foodComment->setUser($this);
        }

        return $this;
    }

    public function removeFoodComment(FoodComment $foodComment): self
    {
        if ($this->foodComments->removeElement($foodComment)) {
            // set the owning side to null (unless already changed)
            if ($foodComment->getUser() === $this) {
                $foodComment->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}

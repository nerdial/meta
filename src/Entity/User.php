<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(

    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]

)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[
        Groups(['read'])
    ]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Item::class, orphanRemoval: true)]
    private Collection $items;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Auction::class)]
    private Collection $auctions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\Column(length: 255)]
    #[
        Groups(['read'])
    ]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[
        Groups(['read'])
    ]
    private ?string $wallet_id = null;

    #[ORM\Column(nullable: true)]
    #[
        Groups(['read'])
    ]
    private ?float $wallet_amount = null;


    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->auctions = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setUser($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getUser() === $this) {
                $item->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Auction>
     */
    public function getAuctions(): Collection
    {
        return $this->auctions;
    }

    public function addAuction(Auction $auction): self
    {
        if (!$this->auctions->contains($auction)) {
            $this->auctions->add($auction);
            $auction->setBuyer($this);
        }

        return $this;
    }

    public function removeAuction(Auction $auction): self
    {
        if ($this->auctions->removeElement($auction)) {
            // set the owning side to null (unless already changed)
            if ($auction->getBuyer() === $this) {
                $auction->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getWalletId(): ?string
    {
        return $this->wallet_id;
    }

    public function setWalletId(?string $wallet_id): self
    {
        $this->wallet_id = $wallet_id;

        return $this;
    }

    public function getWalletAmount(): ?float
    {
        return $this->wallet_amount;
    }

    public function setWalletAmount(?float $wallet_amount): self
    {
        $this->wallet_amount = $wallet_amount;

        return $this;
    }


}

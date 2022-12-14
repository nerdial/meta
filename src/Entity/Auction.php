<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AuctionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AuctionRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            openapiContext: [
                'summary' => 'Seller can create a new auction for others to buy the item',
                'description' => 'Create a new auction by seller'
            ]
        ),
        new GetCollection(),
        new Patch(
            uriTemplate: '/auctions/{id}/buy',
            requirements: ['id' => '\d+'],
            openapiContext: [
                'summary' => 'Buyer purchases an item, Moves the minted item to its wallet and creates transaction',
                'description' => 'The auction is already created by a seller, then a buyer comes and buys the action'
            ]
        )
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']])]
class Auction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[
        Groups(['read', 'write'])
    ]
    private ?float $price = null;

    #[ORM\OneToOne(inversedBy: 'auction', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[
        Groups(['write'])
    ]
    private ?Item $item = null;

    #[ORM\ManyToOne(inversedBy: 'auctions')]
    #[
        Groups(['write'])
    ]
    private ?User $buyer = null;

    #[ORM\OneToOne(mappedBy: 'auction', cascade: ['persist', 'remove'])]
    #[
        Groups(['read'])
    ]
    private ?Transaction $transaction = null;

    #[ORM\Column(nullable: true)]
    #[
        Groups(['read'])
    ]
    private array $metadata = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): self
    {
        // set the owning side of the relation if necessary
        if ($transaction->getAuction() !== $this) {
            $transaction->setAuction($this);
        }

        $this->transaction = $transaction;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }
}

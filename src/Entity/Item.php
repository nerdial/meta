<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
    new Post(),
    new GetCollection(),
],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity(repositoryClass: ItemRepository::class)]

class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[
        Groups(['read'])
    ]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Assert\NotBlank,
        Assert\Length(min: 3),
        Groups(['write', 'read'])
    ]
    private ?string $title = null;


    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[
        Assert\NotBlank,
        Groups(['write', 'read'])
    ]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'item', cascade: ['persist', 'remove'])]
    private ?Auction $auction = null;

    #[ORM\Column(type: Types::TEXT)]
    #[
        Assert\NotBlank,
        Groups(['write', 'read'])
    ]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[
        Groups(['read'])
    ]
    private  array $metadata = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAuction(): ?Auction
    {
        return $this->auction;
    }

    public function setAuction(Auction $auction): self
    {
        // set the owning side of the relation if necessary
        if ($auction->getItem() !== $this) {
            $auction->setItem($this);
        }

        $this->auction = $auction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata=[]): self
    {
        $this->metadata = $metadata;

        return $this;
    }
}

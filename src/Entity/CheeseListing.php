<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: CheeseListingRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_USER')"
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['cheese:read', 'cheese:item:get'],
            ],
        ],
        'put' => [
            'security' => "is_granted('EDIT', object)",
            'security_message' => 'Only owner can edit a cheese listing.'
        ],
        'delete' => ['security' => "is_granted('ROLE_ADMIN')"],
        'patch',
    ],
    shortName: 'cheese',
    attributes: [
        'pagination_items_per_page' => 10,
        'formats'                   => ['jsonld', 'json', 'html', 'jsonhal', 'csv' => 'text/csv'],
    ]
)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(SearchFilter::class, properties: [
    'title'          => SearchFilterInterface::STRATEGY_PARTIAL,
    'description'    => SearchFilterInterface::STRATEGY_PARTIAL,
    'owner'          => SearchFilterInterface::STRATEGY_EXACT,
    'owner.username' => SearchFilterInterface::STRATEGY_PARTIAL,
])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(PropertyFilter::class)]
class CheeseListing
{
    private const DESCRIPTION_TEXT_LIMIT = 40;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['cheese:read', 'cheese:write', 'user:read', 'user:write'])]
    #[NotBlank]
    #[Length(min: 2, max: 50, maxMessage: 'Describe your cheese in 50 chars or less')]
    private ?string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['cheese:read'])]
    #[NotBlank]
    private ?string $description = null;

    /**
     * The price of this delicious cheese, in cents.
     */
    #[ORM\Column]
    #[Groups(['cheese:read', 'cheese:write', 'user:read', 'user:write'])]
    #[NotBlank]
    #[GreaterThan(0)]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt;

    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'cheeseListings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cheese:read', 'cheese:write'])]
    #[Valid]
    private ?User $owner = null;

    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->title     = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups(['cheese:read'])]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < self::DESCRIPTION_TEXT_LIMIT) {
            return $this->description;
        }

        return substr(strip_tags($this->description), 0, self::DESCRIPTION_TEXT_LIMIT) . '...';
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * The description of the cheese as raw text.
     */
    #[Groups(['cheese:write', 'user:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long ago in text that this cheese listing was added.
     */
    #[Groups(['cheese:read'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }

    public function isIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}

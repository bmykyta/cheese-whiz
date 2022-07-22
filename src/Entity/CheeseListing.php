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

#[ORM\Entity(repositoryClass: CheeseListingRepository::class)]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get' => [], 'put', 'patch'],
    shortName: 'cheeses',
    attributes: [
        'pagination_items_per_page' => 10,
        'formats'                   => ['jsonld', 'json', 'html', 'jsonhal', 'csv' => 'text/csv'],
    ],
    denormalizationContext: [
        'groups' => [
            'cheese_listing:write',
        ],
    ],
    normalizationContext: [
        'groups' => [
            'cheese_listing:read',
        ],
    ]
)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(SearchFilter::class, properties: [
    'title'       => SearchFilterInterface::STRATEGY_PARTIAL,
    'description' => SearchFilterInterface::STRATEGY_PARTIAL,
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
    #[Groups(['cheese_listing:read', 'cheese_listing:write'])]
    #[NotBlank]
    #[Length(min: 2, max: 50, maxMessage: 'Describe your cheese in 50 chars or less')]
    private ?string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['cheese_listing:read'])]
    #[NotBlank]
    private ?string $description = null;

    /**
     * The price of this delicious cheese, in cents.
     */
    #[ORM\Column]
    #[Groups(['cheese_listing:read', 'cheese_listing:write'])]
    #[NotBlank]
    #[GreaterThan(0)]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt;

    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'cheeseListings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cheese_listing:read', 'cheese_listing:write'])]
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

    #[Groups(['cheese_listing:read'])]
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
     * The description of the cheese ar raw text.
     */
    #[Groups(['cheese_listing:write'])]
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
    #[Groups(['cheese_listing:read'])]
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

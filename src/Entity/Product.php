<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $shortDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(length: 100)]
    private ?string $code = null;

    /**
     * @var Collection<int, EcoLabel>
     */
    #[ORM\ManyToMany(targetEntity: EcoLabel::class, inversedBy: 'products')]
    private Collection $ecoLabels;

    /**
     * @var Collection<int, Promotion>
     */
    #[ORM\ManyToMany(targetEntity: Promotion::class, inversedBy: 'products')]
    private Collection $promotions;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'products')]
    private Collection $tags;

    /**
     * @var Collection<int, ProductVariant>
     */
    #[ORM\OneToMany(targetEntity: ProductVariant::class, mappedBy: 'product', cascade: ['persist', 'remove'],
    orphanRemoval: true)]
    private Collection $productVariants;

    /**
     * @var Collection<int, PetType>
     */
    #[ORM\ManyToMany(targetEntity: PetType::class, inversedBy: 'products')]
    private Collection $petTypes;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function __construct()
    {
        $this->ecoLabels = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->productVariants = new ArrayCollection();
        $this->petTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        if ($this->name === '') {
            throw new \LogicException('Le produit doit avoir un nom');
        }
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, EcoLabel>
     */
    public function getEcoLabels(): Collection
    {
        return $this->ecoLabels;
    }

    public function addEcoLabel(EcoLabel $ecoLabel): static
    {
        if (!$this->ecoLabels->contains($ecoLabel)) {
            $this->ecoLabels->add($ecoLabel);
        }

        return $this;
    }

    public function removeEcoLabel(EcoLabel $ecoLabel): static
    {
        $this->ecoLabels->removeElement($ecoLabel);

        return $this;
    }

    /**
     * @return Collection<int, PetType>
     */
    public function getPetTypes(): Collection
    {
        return $this->petTypes;
    }

    public function addPetType(PetType $petType): static
    {
        if (!$this->petTypes->contains($petType)) {
            $this->petTypes->add($petType);
        }

        return $this;
    }

    public function removePetType(PetType $petType): static
    {
        $this->petTypes->removeElement($petType);

        return $this;
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): static
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions->add($promotion);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): static
    {
        $this->promotions->removeElement($promotion);

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, ProductVariant>
     */
    public function getProductVariants(): Collection
    {
        return $this->productVariants;
    }

    public function addProductVariant(ProductVariant $productVariant): static
    {
        if (!$this->productVariants->contains($productVariant)) {
            $this->productVariants->add($productVariant);
            $productVariant->setProduct($this);
        }

        return $this;
    }

    public function removeProductVariant(ProductVariant $productVariant): static
    {
        $this->productVariants->removeElement($productVariant);

        return $this;
    }

    /**
     * Récupère le variant par défaut (le premier actif)
     */
    public function getDefaultVariant(): ?ProductVariant
    {
        foreach ($this->productVariants as $variant) {
            if ($variant->isActive()) {
                return $variant;
            }
        }
        
        return $this->productVariants->first() ?: null;
    }

    /**
     * Récupère le prix du variant par défaut
     */
    public function getPrice(): ?string
    {
        return $this->getDefaultVariant()?->getPrice();
    }

    /**
     * Récupère le stock total
     */
    public function getTotalStock(): int
    {
        $total = 0;
        foreach ($this->productVariants as $variant) {
            $total += $variant->getStock();
        }
        return $total;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}

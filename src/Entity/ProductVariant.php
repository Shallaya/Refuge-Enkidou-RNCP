<?php

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ProductVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $variantName;

    #[ORM\Column(length: 150, unique: true)]
    private ?string $sku = null;

    #[ORM\Column]
    private int $stock = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2, nullable: false)]
    private string $price;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $material = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $weightValue = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $weightUnit = null; // g, kg

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $volumeValue = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $volumeUnit = null; // ml, L

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $size = null; // S, M, L

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'productVariants')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    // ======================
    // LIFECYCLE
    // ======================

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSku(): void
    {
        $this->generateSku();
    }

    // ======================
    // MÉTHODES MÉTIER
    // ======================

    private function generateSku(): void
    {
        $product = $this->getProduct();
        
        $parts = [];

        // On ajoute toujours le préfixe + ID
        $parts[] = 'PRD-' . $product->getId();

        // Liste des champs à inclure dans le SKU
        $fields = [
            $product->getCode(),
            $this->material,
            $this->size,
            $this->weightValue,
            $this->weightUnit,
            $this->volumeValue,
            $this->volumeUnit,
            $this->color
        ];

        foreach ($fields as $field) {
            if ($field !== null && $field !== '') {
                $parts[] = $this->sanitize((string) $field);
            }
        }

        $this->sku = implode('-', $parts);
    }

    private function sanitize(string $value): string
    {
        $value = (string) iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = (string) preg_replace('/[^A-Za-z0-9]/', '', $value);

        return strtoupper($value);
    }

    // ======================
    // GETTERS & SETTERS
    // ======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariantName(): string
    {
        return $this->variantName;
    }

    public function setVariantName(string $variantName): static
    {
        $this->variantName = $variantName;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = max(0, $stock);

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getWeightValue(): ?string
    {
        return $this->weightValue;
    }

    public function setWeightValue(?string $weightValue): static
    {
        $this->weightValue = $weightValue;

        return $this;
    }

    public function getWeightUnit(): ?string
    {
        return $this->weightUnit;
    }

    public function setWeightUnit(?string $weightUnit): static
    {
        $this->weightUnit = $weightUnit;

        return $this;
    }

    public function getVolumeValue(): ?string
    {
        return $this->volumeValue;
    }

    public function setVolumeValue(?string $volumeValue): static
    {
        $this->volumeValue = $volumeValue;

        return $this;
    }

    public function getVolumeUnit(): ?string
    {
        return $this->volumeUnit;
    }

    public function setVolumeUnit(?string $volumeUnit): static
    {
        $this->volumeUnit = $volumeUnit;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

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

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\PetTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PetTypeRepository::class)]
class PetType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'petType', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'petType')]
    private Collection $categories;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
{
    if (!$this->products->contains($product)) {
        $this->products->add($product);
        $product->setPetType($this);
    }

    return $this;
}

public function removeProduct(Product $product): static
{
    if ($this->products->removeElement($product)) {
        if ($product->getPetType() === $this) {
            $product->setPetType(null);
        }
    }

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

/**
 * @return Collection<int, Category>
 */
public function getCategories(): Collection
{
    return $this->categories;
}

public function addCategory(Category $category): static
{
    if (!$this->categories->contains($category)) {
        $this->categories->add($category);
        $category->setPetType($this);
    }

    return $this;
}

public function removeCategory(Category $category): static
{
    if ($this->categories->removeElement($category)) {
        // set the owning side to null (unless already changed)
        if ($category->getPetType() === $this) {
            $category->setPetType(null);
        }
    }

    return $this;
}
}

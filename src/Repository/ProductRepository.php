<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\PetType;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Récupère les variantes actives filtrées par type d'animal et optionnellement par catégorie
     * @return Product[]
     */
    public function findByPetTypeAndCategory(
        PetType $petType,
        ?Category $category = null
    ): array {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.productVariants', 'v')
            ->addSelect('p') // Évite les requêtes N+1
            ->where('p.petType = :petType')
            ->andWhere('p.isActive = true')
            ->andWhere('v.isActive = true')
            ->setParameter('petType', $petType)
            ->orderBy('p.name', 'ASC');
        
        // Si une catégorie est spécifiée
        if ($category !== null) {
            // Récupérer la catégorie ET ses enfants
            $categoryIds = [$category->getId()];
            
            foreach ($category->getChildren() as $child) {
                $categoryIds[] = $child->getId();
            }
            
            $queryBuilder->andWhere('p.category IN (:categories)')
                ->setParameter('categories', $categoryIds);
        }
        
        return $queryBuilder->getQuery()->getResult();
    }

}

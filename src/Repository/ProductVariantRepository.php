<?php

namespace App\Repository;

use App\Entity\ProductVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductVariant>
 */
class ProductVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }

    /**
     * Trouve les variantes actives pour l'affichage en page d'accueil
     * Avec jointure pour éviter les requêtes N+1
     * @return ProductVariant[] 
     */
    public function findFeaturedVariants(int $limit = 8): array
    {
        return $this->createQueryBuilder('v')
            ->innerJoin('v.product', 'p')
            ->addSelect('p')
            ->where('v.isActive = true')
            ->andWhere('p.isActive = true')
            ->orderBy('v.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    
}

<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductVariantType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            
            TextField::new('name', 'Nom du produit')->setRequired(true),
            TextField::new('code', 'Code produit')->setRequired(true),
            
            TextField::new('slug', 'Slug')
                ->onlyOnDetail()
                ->setHelp('Généré automatiquement depuis le nom'),
            
            TextField::new('shortDescription', 'Description courte')->setMaxLength(255),
            TextareaField::new('description', 'Description complète')->hideOnIndex(),
            
            ImageField::new('image', 'Image')
                ->setBasePath('uploads/products')
                ->setUploadDir('public/uploads/products')
                ->setUploadedFileNamePattern('[name].[extension]'),
            
            AssociationField::new('category', 'Catégorie')->setRequired(true),
            AssociationField::new('petTypes', 'Types d\'animal'),
            AssociationField::new('ecoLabels', 'Labels écologiques')->hideOnIndex(),
            AssociationField::new('tags', 'Tags')->hideOnIndex(),
            AssociationField::new('promotions', 'Promotions')->hideOnIndex(),

            CollectionField::new('productVariants', 'Variantes')
                ->onlyOnForms()
                ->setEntryType(ProductVariantType::class)
                ->allowAdd()
                ->allowDelete()
                ->setFormTypeOption('by_reference', false),
            
            TextField::new('variantsInfo', 'Variantes')
                ->onlyOnIndex()
                ->formatValue(function ($value, Product $entity) {
                    $count = $entity->getProductVariants()->count();
                    if ($count === 0) {
                        return '<span class="badge badge-danger">0 variante</span>';
                    }
                    return sprintf(
                        '<span class="badge badge-success">%d variante%s</span>',
                        $count,
                        $count > 1 ? 's' : ''
                    );
                }),
            
            BooleanField::new('isActive', 'Actif'),
            DateTimeField::new('createdAt', 'Date de création')->onlyOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function createEntity(string $entityFqcn): Product
    {
        $product = new Product();
        $product->setCreatedAt(new \DateTimeImmutable());
        $product->setIsActive(true);

        $variant = new \App\Entity\ProductVariant();
        $variant->setVariantName('Variante par défaut');
        $variant->setPrice('0.00');
        $variant->setStock(0);
        $variant->setIsActive(true);

        $product->addProductVariant($variant);

        return $product;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('category')
            ->add('petTypes')
            ->add('isActive');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        // Si le produit a déjà un ID, ce n'est pas une vraie création classique
        // on laisse EasyAdmin gérer normalement
        if ($entityInstance->getId() !== null) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        // On garde les variantes temporairement de côté
        $variants = [];
        foreach ($entityInstance->getProductVariants() as $variant) {
            $variants[] = $variant;
        }

        // On retire temporairement les variantes de la collection
        // pour persister d'abord le produit seul
        foreach ($variants as $variant) {
            $entityInstance->removeProductVariant($variant);
        }

        // 1) Persister le produit seul pour obtenir son ID réel
        $entityManager->persist($entityInstance);
        $entityManager->flush();

        // 2) Rattacher les variantes au produit maintenant que l'ID existe
        foreach ($variants as $variant) {
            $entityInstance->addProductVariant($variant);
            $entityManager->persist($variant);
        }

        // 3) Persister les variantes
        $entityManager->flush();
    }
}
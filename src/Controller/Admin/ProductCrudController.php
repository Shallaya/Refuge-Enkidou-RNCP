<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductVariantType;
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
}
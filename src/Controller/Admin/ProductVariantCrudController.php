<?php

namespace App\Controller\Admin;

use App\Entity\ProductVariant;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductVariantCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack
    ) {}

    public static function getEntityFqcn(): string
    {
        return ProductVariant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Variante de produit')
            ->setEntityLabelInPlural('Variantes de produits')
            ->setDefaultSort(['product' => 'ASC', 'variantName' => 'ASC'])
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une nouvelle variante')
            ->setPageTitle(Crud::PAGE_EDIT, fn (ProductVariant $variant) => 
                sprintf('Modifier <b>%s</b> - %s', 
                    $variant->getProduct()->getName(), 
                    $variant->getVariantName()
                )
            );
    }

    public function configureFields(string $pageName): iterable
    {
        $productField = AssociationField::new('product', 'Produit')
            ->setRequired(true);

        // ⭐ Si on vient depuis un produit, pré-sélectionner et masquer le champ
        $request = $this->requestStack->getCurrentRequest();
        $productId = $request->query->get('productId');
        
        if ($productId && $pageName === Crud::PAGE_NEW) {
            $productField->setFormTypeOption('data', 
                $this->em->getRepository(Product::class)->find($productId)
            )
            ->setFormTypeOption('disabled', true)
            ->setHelp('Produit pré-sélectionné');
        }

        return [
            IdField::new('id')->onlyOnIndex(),
            
            $productField,
            
            TextField::new('variantName', 'Nom de la variante')
                ->setRequired(true)
                ->setHelp('Ex: Standard, Rouge 500g, Bleu Taille M...'),
            
            TextField::new('sku', 'SKU')
                ->onlyOnIndex()
                ->setHelp('Généré automatiquement'),
            
            MoneyField::new('price', 'Prix')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setNumDecimals(2)
                ->setRequired(true),
            
            NumberField::new('stock', 'Stock')
                ->setRequired(true),
            
            TextField::new('material', 'Matériau'),
            
            TextField::new('color', 'Couleur'),
            
            TextField::new('size', 'Taille')
                ->setHelp('S, M, L, XL...'),
            
            NumberField::new('weightValue', 'Poids (valeur)'),
            
            TextField::new('weightUnit', 'Unité poids')
                ->setHelp('g, kg'),
            
            NumberField::new('volumeValue', 'Volume (valeur)'),
            
            TextField::new('volumeUnit', 'Unité volume')
                ->setHelp('ml, L'),
            
            BooleanField::new('isActive', 'Actif'),
        ];
    }

    /**
     * ⭐ À la création : pré-remplir le produit si passé en paramètre
     */
    public function createEntity(string $entityFqcn)
    {
        $variant = new ProductVariant();
        $variant->setIsActive(true);
        $variant->setStock(0);
        
        // ⭐ Récupérer le productId depuis l'URL
        $request = $this->requestStack->getCurrentRequest();
        $productId = $request->query->get('productId');
        
        if ($productId) {
            $product = $this->em->getRepository(Product::class)->find($productId);
            if ($product) {
                $variant->setProduct($product);
            }
        }
        
        return $variant;
    }

    /**
     * ⭐ ACTIONS : Retour vers le produit parent
     */
    public function configureActions(Actions $actions): Actions
    {
        $backToProduct = Action::new('backToProduct', 'Retour au produit', 'fa fa-arrow-left')
            ->linkToCrudAction('backToProduct')
            ->displayAsLink()
            ->setCssClass('btn btn-secondary');

        return $actions
            ->add(Crud::PAGE_EDIT, $backToProduct)
            ->add(Crud::PAGE_DETAIL, $backToProduct)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function (ProductVariant $variant) {
                    // Empêcher la suppression s'il ne reste qu'une variante
                    return $variant->getProduct()->getProductVariants()->count() > 1;
                });
            });
    }

    /**
     * ⭐ ACTION : Retourner vers le produit parent
     */
    public function backToProduct()
    {
        $variant = $this->getContext()->getEntity()->getInstance();
        $product = $variant->getProduct();
        
        return $this->redirectToRoute('admin', [
            'crudAction' => 'edit',
            'crudControllerFqcn' => ProductCrudController::class,
            'entityId' => $product->getId()
        ]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('product')
            ->add('isActive')
            ->add('material')
            ->add('color')
            ->add('size');
    }

    /**
     * ⭐ Empêcher la suppression de la dernière variante
     */
    public function deleteEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof ProductVariant) {
            parent::deleteEntity($em, $entityInstance);
            return;
        }

        $product = $entityInstance->getProduct();
        
        if ($product->getProductVariants()->count() <= 1) {
            $this->addFlash('danger', 'Impossible de supprimer la dernière variante d\'un produit !');
            return;
        }

        parent::deleteEntity($em, $entityInstance);
        $this->addFlash('success', 'Variante supprimée avec succès.');
    }
}
<?php

namespace App\Form;

use App\Entity\ProductVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variantName', TextType::class, [
                'label' => 'Nom de la variante',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'divisor' => 1,
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
            ])
            ->add('material', TextType::class, [
                'label' => 'Matériau',
                'required' => false,
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
                'required' => false,
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
                'required' => false,
            ])
            ->add('weightValue', NumberType::class, [
                'label' => 'Poids',
                'required' => false,
                'scale' => 2,
            ])
            ->add('weightUnit', TextType::class, [
                'label' => 'Unité poids',
                'required' => false,
            ])
            ->add('volumeValue', NumberType::class, [
                'label' => 'Volume',
                'required' => false,
                'scale' => 2,
            ])
            ->add('volumeUnit', TextType::class, [
                'label' => 'Unité volume',
                'required' => false,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Active',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductVariant::class,
        ]);
    }
}
<?php

// src/Form/ProductOrderableType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOrderableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sku', TextType::class, [
                'required' => true,
            ])
            ->add('productName', TextType::class, [
                'required' => true,
            ])
            ->add('productIdentifiers', TextType::class, [
                'required' => false,
            ])
            ->add('brand', TextType::class, [
                'required' => true,
            ])
            ->add('price', NumberType::class, [
                'required' => true,
            ])
            ->add('mustShipAlone', CheckboxType::class, [
                'required' => false,
            ])
            ->add('skuUpdate', TextType::class, [
                'required' => false,
            ])
            ->add('shippingWeight', NumberType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOrderableTypeData::class,
        ]);
    }
}

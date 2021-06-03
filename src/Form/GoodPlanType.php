<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\GoodPlan;
use App\Entity\Partner;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoodPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'v-model' => 'title'
                ],
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'attr' => [
                    'v-model' => 'description'
                ],
            ])
            ->add('link', UrlType::class, [
                'label' => 'Link',
                'required' => false,
                'attr' => [
                    'v-model' => 'link'
                ],
            ])
            ->add('promoCode', TextType::class, [
                'label' => 'Promo Code',
                'required' => false,
                'attr' => [
                    'v-model' => 'promoCode'
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'required' => false,
                'attr' => [
                    'v-model' => 'price'
                ],
            ])
            ->add('initialPrice', MoneyType::class, [
                'label' => 'Initial Price',
                'required' => false,
                'attr' => [
                    'v-model' => 'initialPrice'
                ],
            ])
            ->add('shippingCost', MoneyType::class, [
                'label' => 'ShippingCost',
                'required' => false,
                'attr' => [
                    'v-model' => 'shippingCost'
                ],
            ])
            ->add('freeShipping', CheckboxType::class, [
                'label' => 'Free Shipping',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'v-model' => 'freeShipping'
                ],
            ])
            ->add('partner', EntityType::class, [
                'label' => 'Partner',
                'class' => Partner::class,
                'required' => false,
                'choice_label' => 'name',
                'attr' => [
                    'v-model' => 'partner'
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'v-model' => 'categories'
                ],
            ])
            ->add('picture', FileType::class, [
                'label' => 'Picture',
                'required' => false,
                'attr' => [
                    'v-model' => 'picture'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GoodPlan::class,
        ]);
    }
}

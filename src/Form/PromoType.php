<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Partner;
use App\Entity\Promo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoType extends AbstractType
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
            ->add('description', TextareaType::class, [
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
            ])
            ->add('promoType', EntityType::class, [
                'label' => 'Promo Type',
                'class' => \App\Entity\PromoType::class,
                'choice_label' => 'type',
                'attr' => [
                    'v-model' => 'promoType'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promo::class,
        ]);
    }
}

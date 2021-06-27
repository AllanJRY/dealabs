<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Partner;
use App\Entity\Promo;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'label' => false,
                'attr' => [
                    'v-model' => 'title',
                    'placeholder' => 'Donnez un titre court et descriptif à votre code promo'

                ],
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'attr' => [
                    'v-model' => 'description',
                    'placeholder' => 'En manque d\'idées ? Présentez le produit ou l\'offre avec vos propres mots, expliquez en quoi l\'offre est intéressante selon vous, décrivez la façon d\'obtenir le prix s\'il y a une astuce… Ne faites pas de copier-coller de contenus d\'autres sites ! Votre deal est en magasin ? N’oubliez pas de préciser la ville où il se trouve, et le stock disponible !'
                ],
            ])
            ->add('link', UrlType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'v-model' => 'link',
                    'placeholder' => 'https://www.example.com/superdeal'
                ],
            ])
            ->add('promoCode', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'v-model' => 'promoCode',
                    'placeholder' => 'Inscrivez le code promo'
                ],
            ])
            ->add('partner', EntityType::class, [
                'label' => false,
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
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'v-model' => 'picture'
                ],
            ])
            ->add('promoType', EntityType::class, [
                'label' => false,
                'class' => \App\Entity\PromoType::class,
                'choice_label' => 'type',
                'attr' => [
                    'v-model' => 'promoType'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promo::class,
        ]);
    }
}

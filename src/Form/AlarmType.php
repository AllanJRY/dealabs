<?php

namespace App\Form;

use App\Entity\Alarm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Donnez un recherche court et descriptif'
                ],
            ])
            ->add('rating', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Température minimum Nouveau (0°)' => 0,
                    'Température minimum Tiède (20°)' => 20,
                    'Température minimum Hot (100°)' => 100,
                    'Température minimum Super hot (500°)' => 5
                ],
                'attr' => [
                    'class' => 'm-3',
                ],
                'multiple' => false,
                'expanded' => true
            ])
            ->add('mail_notif', CheckboxType::class, [
                'label' => ' Recevoir une notification par e-mail chaque jour pour chaque deal correspondant à cette alerte',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alarm::class,
        ]);
    }
}

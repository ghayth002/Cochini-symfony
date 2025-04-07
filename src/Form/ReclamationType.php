<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description de la réclamation',
                'attr' => [
                    'placeholder' => 'Veuillez décrire votre problème en détail...',
                    'rows' => 5,
                    'class' => 'form-control'
                ]
            ])
            ->add('typeR', ChoiceType::class, [
                'label' => 'Type de réclamation',
                'choices' => [
                    'Problème technique' => 'Problème technique',
                    'Problème avec un coach' => 'Problème avec un coach',
                    'Problème avec un produit' => 'Problème avec un produit',
                    'Problème avec un événement' => 'Problème avec un événement',
                    'Autre' => 'Autre'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('coach', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'label' => 'Coach concerné (optionnel)',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
} 
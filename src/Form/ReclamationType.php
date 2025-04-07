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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description de la réclamation *',
                'attr' => [
                    'placeholder' => 'Veuillez décrire votre problème en détail...',
                    'rows' => 5,
                    'class' => 'form-control',
                    'maxlength' => 2000,
                    'data-validation-message' => 'La description est requise et doit comporter entre 10 et 2000 caractères.'
                ],
                'help' => 'Minimum 10 caractères, maximum 2000 caractères.',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La description ne peut pas être vide.']),
                    new Length([
                        'min' => 10,
                        'max' => 2000,
                        'minMessage' => 'La description doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('typeR', ChoiceType::class, [
                'label' => 'Type de réclamation *',
                'choices' => [
                    'Problème avec un adhérant' => 'Problème avec un adhérant',
                    'Problème avec un coach' => 'Problème avec un coach',
                    'Problème avec un produit' => 'Problème avec un produit',
                    'Problème avec un événement' => 'Problème avec un événement',
                    'Autre' => 'Autre'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'data-validation-message' => 'Veuillez sélectionner un type de réclamation.'
                ],
                'help' => 'Choisissez le type qui correspond le mieux à votre réclamation.',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le type de réclamation doit être spécifié.'])
                ]
            ])
            ->add('coach', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'label' => 'Coach concerné (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'help' => 'Sélectionnez un coach si votre réclamation concerne une personne spécifique.',
                'placeholder' => 'Sélectionner un coach (optionnel)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'attr' => [
                'novalidate' => 'novalidate', // Désactive la validation HTML5 pour utiliser Symfony validation
            ],
        ]);
    }
} 
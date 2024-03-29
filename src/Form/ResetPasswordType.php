<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('new_password', RepeatedType::class, [
            'type'=> PasswordType::class,
            'invalid_message'=>'Le Mot de passe et la confirmation doivent être identique!',
            'label' => 'Nouveau Mot de Passe',
            'required'=>true,
            'first_options'=>['label'=>'Nouveau Mot de Passe',
            'attr' => [
                'placeholder' => 'Merci de saisir votre nouveau Mot de passe'
            ]],
            'second_options'=>['label'=>'Confirmez votre nouveau Mot de Passe',
            'attr' => [
                'placeholder' => 'Merci de confirmer votre Mot de Passe'
            ]],
            
        ])

        ->add('Submit', SubmitType::class, [
            'label' => 'Mettre à jour mon mot de passe',
            'attr' => [
                'class' => 'btn btn-info w-100'
            ]
        ])
     
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => 'Prénom'
            ])

            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => 'Nom'
                ])

            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => 'E-mail'
                ])
            ->add('old_password', PasswordType::class,[
                'mapped' => false,
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                ]
            ])
            
            ->add('new_password', RepeatedType::class, [
                'type'=> PasswordType::class,
                'mapped' => false,
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
                'label' => 'Mettre à jour'
            ])
         
           
           ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

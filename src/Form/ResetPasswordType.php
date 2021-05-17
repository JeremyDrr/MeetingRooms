<?php

namespace App\Form;

use App\Entity\ResetPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, $this->getConfiguration("Mot de passe", "Entrez votre nouveau mot de passe", [
                'attr' => [
                    'class' => 'form-control',
                ]
            ]))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Confirmer le mot de passe", "Confirmez le mot de passe", [
                'attr' => [
                    'class' => 'form-control',
                ]
            ]))
            ->add('token', HiddenType::class, $this->getConfiguration("Token", "Token", [
                'attr' => [
                    'class' => 'form-control',
                ],
                'data' => $options['tokenValue'],
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResetPassword::class,
            'tokenValue' => 0000,
        ]);
    }
}

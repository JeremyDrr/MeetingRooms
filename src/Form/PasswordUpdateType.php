<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Mot de passe actuel", "Entrez votre mot de passe actuel", [
                'attr' => [
                    'class' => "form-control",
                ]
            ]))
            ->add("newPassword", PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Entrez votre nouveau mot de passe", [
                'attr' => [
                    'class' => "form-control",
                ]
            ]))
            ->add("confirmPassword", PasswordType::class, $this->getConfiguration("Confirmer le mot de passe", "Confirmez le mot de passe", [
                'attr' => [
                    'class' => "form-control",
                ]
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

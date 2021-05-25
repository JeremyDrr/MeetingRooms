<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Entrez le prénom", [
                'attr' => [
                    'class' => "form-control"
                ]
            ]))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom de famille", "Entrez le nom de famille", [
                'attr' => [
                    'class' => "form-control"
                ]
            ]))
            ->add('email', EmailType::class, $this->getConfiguration("Adresse email", "Entrez l'adresse email", [
                'attr' => [
                    'class' => "form-control"
                ]
            ]))
            ->add('description', TextType::class, $this->getConfiguration("Description", "Entrez la description de votre rôle au sein d'UT1", [
                'attr' => [
                    'class' => "form-control"
                ],
                'required'   => false,
            ]))
            ->add('picture', TextType::class, $this->getConfiguration("Photo de profil", "Entrez l'adresse de la photo de profil", [
                'attr' => [
                    'class' => "form-control"
                ],
                'required'   => false,
                'empty_data' => '/assets/img/user.svg',
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

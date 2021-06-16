<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Nom de la salle", "Entrez le nom de la salle", [
                'attr' => [
                    'class' => "form-control"
                ]
            ]))
            ->add('seats', IntegerType::class, $this->getConfiguration("Nombre de sièges", "Entrez le nombre de sièges", [
                'attr' => [
                    'class' => "form-control",
                    'min' => 1,
                    'max' => 100
                ]
            ]))
            ->add('hasProjector', CheckboxType::class, $this->getConfiguration("Vidéoprojecteur disponible", "", [
                'attr' => [
                    'class' => 'form-check-input',


                ],
                'required'   => false,
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

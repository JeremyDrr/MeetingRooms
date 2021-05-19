<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('room', EntityType::class, $this->getConfiguration("Salle de réunion", "Sélectionnez la salle de réunion", [
                'class' => Room::class,
                'choice_label' => 'name',
            ]))
            ->add('startDate', DateTimeType::class, $this->getConfiguration("Date de début", "Sélectionnez la date de début", [
                'attr' => [
                    'class' => "form-control",
                ]
            ]))
            ->add('endDate', DateTimeType::class, $this->getConfiguration("Date de fin", "Sélectionnez la date de fin", [
                'attr' => [
                    'class' => "form-control",
                ]
            ]))
            ->add('recurrent', CheckboxType::class, $this->getConfiguration("Il s'agit d'une réunion récurrente", "", [
                'attr' => [
                    'class' => 'form-check-input'

                ],
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

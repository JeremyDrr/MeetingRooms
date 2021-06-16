<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre de la réunion", "Saisissez le titre de la réunion", [
                'attr' => [
                    'class' => "form-control"
                ]
            ]))
            ->add('room', EntityType::class, $this->getConfiguration("Salle de réunion", "Sélectionnez la salle de réunion", [
                'class' => Room::class,
                'choice_label' => 'name',
            ]))
            ->add('startDate', DateTimeType::class, $this->getConfiguration("Date de début", "Sélectionnez la date de début", [
                'attr' => [
                    'class' => "form-control",
                ],
                'date_widget' => 'single_text',
                'years' => range(date('Y'), date('Y')),
                'months' => range(date('m'), 12),
                'days' => range(date('d'), 31),
            ]))
            ->add('endDate', DateTimeType::class, $this->getConfiguration("Date de fin", "Sélectionnez la date de fin", [
                'attr' => [
                    'class' => "form-control",
                ],
                'date_widget' => 'single_text',
                'years' => range(date('Y'), date('Y')),
                'months' => range(date('m'), 12),
                'days' => range(date('d'), 31),
            ]))
            ->add('recurrent', CheckboxType::class, $this->getConfiguration("Réserver pour la semaine suivante", "", [
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

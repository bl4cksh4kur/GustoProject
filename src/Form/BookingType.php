<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'Début de la réservation',
                'required' => true,
                'date_widget'=>'single_text',
                'time_widget'=>'single_text',
                'with_seconds' => false,
                'html5' => false,
                'date_format' => 'dd-MM-yy',
                'attr' => [
                    'class' => 'shadow-none'
                ],

            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Fin de la réservation',
                'required' => true,
                'date_widget'=>'single_text',
                'time_widget'=>'single_text',
                'date_format' => 'dd-MM-yy',
                'with_seconds' => false,
                'html5' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}

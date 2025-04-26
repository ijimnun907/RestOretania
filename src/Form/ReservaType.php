<?php

namespace App\Form;

use App\Entity\Mesa;
use App\Entity\Reserva;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mesa', EntityType::class, [
                'class' => Mesa::class,
                'multiple' => false,
                'required' => true,
                'choice_label' => function ($mesa) {
                    return 'Mesa ' . $mesa->getNumero() . 'capacidad: ' . $mesa->getCapacidad() . ')';
                },
                'placeholder' => 'Selecciona una mesa'
            ])
            ->add('fecha', DateType::class, [
                'widget' => 'single_text',
                'mapped' => false,
                'attr' => ['min' => (new \DateTime())->format('Y-m-d')],
                'label' => 'Fecha'
            ])
            ->add('hora', ChoiceType::class, [
                'choices' => [],
                'mapped' => false,
                'placeholder' => 'Selecciona una hora',
                'label' => 'Hora',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reserva::class,
        ]);
    }
}

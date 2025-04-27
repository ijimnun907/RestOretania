<?php

namespace App\Form;

use App\Entity\Mesa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', IntegerType::class, [
                'label' => 'Número de mesa'
            ])
            ->add('capacidad', IntegerType::class, [
                'label' => 'Capacidad de la mesa'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mesa::class,
        ]);
    }
}

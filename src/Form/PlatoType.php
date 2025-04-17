<?php

namespace App\Form;

use App\Entity\Plato;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints as Assert;

class PlatoType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('precio', MoneyType::class, [
                'divisor' => 100
            ])
            ->add('foto', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Foto del plato',
                'attr' => ['accept' => 'image/*'],
                'constraints' => [
                    new Assert\File(
                        mimeTypes: ['image/jpeg', 'image/png'],
                        mimeTypesMessage: 'Por favor sube una imagen JPG o PNG'
                    )
                ]
            ])
            ->add('contieneGluten', CheckboxType::class, [
                'label' => 'Contiene gluten',
                'required' => false
            ])
            ->add('contieneLactosa', CheckboxType::class, [
                'label' => 'Contiene lactosa',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plato::class,
        ]);
    }
}

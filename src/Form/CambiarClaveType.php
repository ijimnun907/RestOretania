<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CambiarClaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['admin']){
            $builder->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Contraseña actual',
                'constraints' => [
                    new NotBlank(),
                    new UserPassword([
                            'message' => 'Contraseña actual incorrecta']
                    ),
                ],
            ]);
        }
        $builder
            ->add('newPassword', RepeatedType::class, [
                'label' => 'Nueva contraseña',
                'required' => true,
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'No coinciden las contraseñas',
                'first_options' => [
                    'label' => 'Nueva contraseña',
                    'constraints' => [
                        new NotBlank(),
                        new Length(min: 7, max: 255, minMessage: 'La contraseña tiene que tener minimo 7 caracteres', maxMessage: 'La contraseña no puede tener más de 255 caracteres')
                    ]
                ],
                'second_options' => [
                    'label' => 'Repite la nueva contraseña',
                    'required' => true
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'admin' => false
        ]);
    }
}
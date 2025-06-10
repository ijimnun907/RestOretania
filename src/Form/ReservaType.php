<?php

namespace App\Form;

use App\Entity\Mesa;
use App\Entity\Reserva;
use App\Entity\Usuario;
use App\Repository\ReservaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReservaType extends AbstractType
{
    private Security $security;
    private ReservaRepository $reservaRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, ReservaRepository $reservaRepository, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->reservaRepository = $reservaRepository;
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('usuario', EntityType::class, [
                'class' => Usuario::class,
                'choice_label' => 'email',
                'label' => 'Usuario',
                'multiple' => false,
                'required' => false,
                'disabled' => !$this->security->isGranted('ROLE_CAMARERO')
            ])
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
                'label' => 'Fecha',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Debes seleccionar una fecha.']),
                ],
            ])
            ->add('hora', ChoiceType::class, [
                'choices' => [],
                'mapped' => false,
                'placeholder' => 'Selecciona una hora',
                'label' => 'Hora',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Debes seleccionar una hora.']),
                ],
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $fechaStr = $data['fecha'] ?? null;
            $mesaId = $data['mesa'] ?? null;

            // Solo si tenemos fecha y mesa, calculamos las horas disponibles
            if ($fechaStr && $mesaId) {
                $fecha = \DateTime::createFromFormat('Y-m-d', $fechaStr);
                $mesa = $this->entityManager->getRepository(Mesa::class)->find($mesaId);

                if ($fecha && $mesa) {
                    // Replicamos la misma lógica que usas en el controlador para obtener horas
                    $todasLasHoras = ['13:00', '13:15', '13:30', '13:45', '14:00', '14:15', '14:30', '14:45', '15:00', '15:15',
                        '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00', '19:15', '19:30', '19:45',
                        '20:00', '20:15', '20:30', '20:45', '21:00', '21:15', '21:30', '21:45', '22:00'];

                    $reservasEnFecha = $this->reservaRepository->findReservasPorFechaYMesa($fecha, $mesa);
                    $horasOcupadas = array_map(fn($reserva) => $reserva->getFechaHora()->format('H:i'), $reservasEnFecha);

                    $horasLibres = array_diff($todasLasHoras, $horasOcupadas);

                    // Re-añadimos el campo 'hora' con las opciones correctas
                    $form->add('hora', ChoiceType::class, [
                        'choices' => array_combine($horasLibres, $horasLibres),
                        'mapped' => false,
                        'placeholder' => 'Selecciona una hora',
                        'label' => 'Hora',
                        'required' => true,
                        'constraints' => [
                            new NotBlank(['message' => 'Debes seleccionar una hora.']),
                        ],
                    ]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reserva::class,
        ]);
    }
}

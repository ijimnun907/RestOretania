<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use App\Factory\MesaFactory;
use App\Factory\PlatoFactory;
use App\Factory\ReservaFactory;
use App\Factory\UsuarioFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- Usuarios ---
        $admin = new Usuario();
        $admin->setUsername('admin');
        $admin->setEmail('admin@restaurante.com');
        $admin->setTelefono('123456789');
        $admin->setEsAdministrador(true);
        $admin->setEsCamarero(false);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin123'
        );
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);

        $camarero = new Usuario();
        $camarero->setUsername('camarero');
        $camarero->setEmail('camarero@restaurante.com');
        $camarero->setTelefono('123456788');
        $camarero->setEsCamarero(true);
        $camarero->setEsAdministrador(false);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $camarero,
            'admin123'
        );
        $camarero->setPassword($hashedPassword);
        $manager->persist($camarero);
        UsuarioFactory::createMany(20);

        PlatoFactory::createOne([
            'nombre' => 'Macarrones',
            'descripcion' => 'Macarrones con tomate',
            'precio' => 1500,
            'contieneGluten' => true,
            'contieneLactosa' => false,
            'foto' => "Macarrones.jpeg"
        ]);
        PlatoFactory::createOne([
            'nombre' => 'Paella',
            'descripcion' => 'Paella con marisco',
            'precio' => 2500,
            'contieneGluten' => true,
            'contieneLactosa' => false,
            'foto' => "paella.jpeg"
        ]);
        PlatoFactory::createOne([
            'nombre' => 'Hamburguesa',
            'descripcion' => 'Hamburguesa clasica',
            'precio' => 700,
            'contieneGluten' => true,
            'contieneLactosa' => false,
            'foto' => "hamburguesa.jpg"
        ]);
        PlatoFactory::createOne([
            'nombre' => 'Jamon',
            'descripcion' => 'Jamon iberico',
            'precio' => 2000,
            'contieneGluten' => true,
            'contieneLactosa' => true,
            'foto' => "jamon.jpeg"
        ]);
        MesaFactory::createMany(12); // Vamos a crear 12 mesas

        // Es importante hacer un flush aquí para que las mesas y usuarios existan en la BD
        // antes de crear las reservas que dependen de ellos.
        $manager->flush();


        // --- 2. Lógica para crear RESERVAS ÚNICAS ---

        // Obtenemos todas las mesas que acabamos de crear.
        $mesas = MesaFactory::all();

        // Usamos la lista de horas válidas que nos proporcionaste.
        $horasValidas = [
            '13:00', '13:15', '13:30', '13:45', '14:00', '14:15', '14:30', '14:45', '15:00', '15:15',
            '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00', '19:15', '19:30', '19:45',
            '20:00', '20:15', '20:30', '20:45', '21:00', '21:15', '21:30', '21:45', '22:00'
        ];

        // Creamos un "pool" de todos los huecos disponibles para los próximos 7 días.
        $slotsDisponibles = [];
        $fechaInicio = new \DateTime('today');

        for ($d = 0; $d < 7; $d++) {
            $fechaActual = (clone $fechaInicio)->modify("+$d days");
            foreach ($mesas as $mesa) {
                foreach ($horasValidas as $horaStr) {
                    list($h, $m) = explode(':', $horaStr);
                    $fechaHora = \DateTimeImmutable::createFromMutable($fechaActual)->setTime((int)$h, (int)$m);

                    // Añadimos el hueco (mesa + fecha y hora) al pool.
                    $slotsDisponibles[] = ['mesa' => $mesa, 'fechaHora' => $fechaHora];
                }
            }
        }

        // Barajamos el pool para que las reservas sean aleatorias.
        shuffle($slotsDisponibles);

        // Decidimos cuántas reservas crear (por ejemplo, 150, o menos si no hay tantos huecos).
        $numeroDeReservasACrear = min(150, count($slotsDisponibles));
        $slotsParaReservar = array_slice($slotsDisponibles, 0, $numeroDeReservasACrear);

        // Creamos una reserva para cada hueco único seleccionado.
        foreach ($slotsParaReservar as $slot) {
            ReservaFactory::createOne([
                'mesa' => $slot['mesa'],
                'fechaHora' => $slot['fechaHora'],
                'usuario' => UsuarioFactory::random() // Asignamos un usuario aleatorio
            ]);
        }

        $manager->flush();
    }
}

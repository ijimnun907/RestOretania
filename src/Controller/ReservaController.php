<?php

namespace App\Controller;

use App\Entity\Reserva;
use App\Form\ReservaType;
use App\Repository\MesaRepository;
use App\Repository\ReservaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservaController extends AbstractController
{
    #[Route('/reserva/nueva', name: 'reserva_nueva')]
    public function nueva(Request $request, ReservaRepository $reservaRepository): Response
    {
        $reserva = new Reserva();
        $form = $this->createForm(ReservaType::class, $reserva);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->procesarFormularioReserva($form, $reserva, $reservaRepository);

            return $this->redirectToRoute('reserva_nueva'); // Puedes cambiar la ruta donde redirigir después
        }

        return $this->render('reserva/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reserva/editar/{id}', name: 'reserva_editar')]
    public function editar(Reserva $reserva, Request $request, ReservaRepository $reservaRepository): Response
    {
        $form = $this->createForm(ReservaType::class, $reserva);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->procesarFormularioReserva($form, $reserva, $reservaRepository);

            return $this->redirectToRoute('reserva_nueva'); // Igual, puedes redirigir donde quieras
        }

        return $this->render('reserva/form.html.twig', [
            'form' => $form->createView(),
            'editando' => true, // Puedes usar esta variable en Twig si quieres mostrar mensajes tipo "Editando reserva"
        ]);
    }

    /**
     * Función privada para procesar y guardar una reserva.
     */
    private function procesarFormularioReserva(FormInterface $form, Reserva $reserva, ReservaRepository $reservaRepository): void
    {
        $mesa = $form->get('mesa')->getData();
        $fecha = $form->get('fecha')->getData();
        $hora = $form->get('hora')->getData();

        $fechaHora = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $fecha->format('Y-m-d') . ' ' . $hora);

        $reserva->setMesa($mesa);
        $reserva->setUsuario($this->getUser()); // Establece automáticamente el usuario logueado
        $reserva->setFechaHora($fechaHora);

        $reservaRepository->add($reserva);
        $reservaRepository->save();
    }

    #[Route('/horas-disponibles/{mesaId}/{fecha}')]
    public function horasDisponibles(int $mesaId, string $fecha, ReservaRepository $reservaRepository, MesaRepository $mesaRepository) : JsonResponse
    {
        $mesa = $mesaRepository->find($mesaId);
        if (!$mesa) {
            return $this->json([]);
        }

        $fechaBase = \DateTimeImmutable::createFromFormat('Y-m-d', $fecha);
        $inicio = $fechaBase->setTime(13, 0);
        $fin = $fechaBase->setTime(22, 0);

        $horas = [];

        while ($inicio < $fin) {
            $yaReservada = $reservaRepository->findOneBy([
                'mesa' => $mesa,
                'fechaHora' => $inicio,
            ]);

            if (!$yaReservada) {
                $horas[] = $inicio->format('H:i');
            }

            $inicio = $inicio->add(new \DateInterval('PT45M'));
        }

        return $this->json($horas);
    }

    #[Route('/reserva/eliminar/{id}', name: 'reserva_eliminar')]
    public function eliminar(Request $request, ReservaRepository $reservaRepository, Reserva $reserva) : Response
    {
        if ($request->request->has('confirmar')){
            try {
                $reservaRepository->remove($reserva);
                $reservaRepository->save();
                $this->addFlash('success', 'Reserva eliminada con exito');
                return $this->redirectToRoute('reservas_listar');
            }
            catch (\Exception $exception){
                $this->addFlash('error', 'No se ha podido eliminar la reserva');
            }
        }

        return $this->render('reserva/eliminar.html.twig', [
            'reserva' => $reserva,
        ]);
    }
}
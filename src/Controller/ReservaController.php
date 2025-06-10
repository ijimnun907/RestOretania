<?php

namespace App\Controller;

use App\Entity\Reserva;
use App\Entity\Usuario;
use App\Form\ReservaType;
use App\Repository\MesaRepository;
use App\Repository\ReservaRepository;
use App\Security\Voter\ReservaVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class ReservaController extends AbstractController
{
    #[Route('/reserva/nueva', name: 'reserva_nueva')]
    public function nueva(Request $request, ReservaRepository $reservaRepository): Response
    {
        $reserva = new Reserva();
        $form = $this->createForm(ReservaType::class, $reserva);

        $form->handleRequest($request);
        $this->procesarFormularioReserva($form, $reserva, $reservaRepository);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('reserva_listar'); // Puedes cambiar la ruta donde redirigir después
        }

        return $this->render('reserva/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reserva/editar/{id}', name: 'reserva_editar')]
    public function editar(Reserva $reserva, Request $request, ReservaRepository $reservaRepository): Response
    {
        $this->denyAccessUnlessGranted(ReservaVoter::EDIT, $reserva);

        $form = $this->createForm(ReservaType::class, $reserva);

        $form->handleRequest($request);
        $this->procesarFormularioReserva($form, $reserva, $reservaRepository);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('reserva_listar'); // Igual, puedes redirigir donde quieras
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
        if (!$form->isSubmitted()) {
            return;
        }

        $mesa = $form->get('mesa')->getData();
        $fecha = $form->get('fecha')->getData();
        $hora = $form->get('hora')->getData();

        $fechaHora = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $fecha->format('Y-m-d') . ' ' . $hora);

        $reserva->setMesa($mesa);
        if ($this->isGranted('ROLE_CAMARERO')){
            $reserva->setUsuario($form->get('usuario')->getData());
        }
        else {
        $reserva->setUsuario($this->getUser()); // Establece automáticamente el usuario logueado
        }
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

    #[IsGranted('ROLE_CAMARERO')]
    #[Route('/reserva/eliminar/{id}', name: 'reserva_eliminar')]
    public function eliminar(Request $request, ReservaRepository $reservaRepository, Reserva $reserva) : Response
    {
        if ($request->request->has('confirmar')){
            try {
                $reservaRepository->remove($reserva);
                $reservaRepository->save();
                $this->addFlash('success', 'Reserva eliminada con exito');
                return $this->redirectToRoute('reserva_listar');
            }
            catch (\Exception $exception){
                $this->addFlash('error', 'No se ha podido eliminar la reserva');
            }
        }

        return $this->render('reserva/eliminar.html.twig', [
            'reserva' => $reserva,
        ]);
    }

    #[Route('/reserva/listar', name: 'reserva_listar')]
    public function listar(ReservaRepository $reservaRepository) : Response
    {
        if ($this->isGranted('ROLE_CAMARERO')){
            $reservas = $reservaRepository->findReservaOrdenadaPorFecha();
        }
        else {
            $usuario = $this->getUser();

            if (!$usuario instanceof Usuario) {
                throw $this->createAccessDeniedException('Debes estar autenticado para ver tus reservas.');
            }

            $reservas = $reservaRepository->findReservasDeUsuarioOrdenadas($usuario);
        }

        return $this->render('reserva/listar.html.twig', [
            'reservas' => $reservas,
        ]);
    }

    #[IsGranted("ROLE_CAMARERO")]
    #[Route('/reserva/hoy', name: 'reserva_hoy')]
    public function listarReservasDeHoy(ReservaRepository $reservaRepository) : Response
    {
        $reservas = $reservaRepository->findReservasDeHoy();

        return $this->render('reserva/reservas_hoy.html.twig', [
            'reservas' => $reservas,
        ]);
    }

    #[Route('/reserva/detalle/{id}' , name: 'reserva_detalle')]
    public function detalleReserva(Reserva $reserva) : Response
    {
        $this->denyAccessUnlessGranted(ReservaVoter::EDIT, $reserva);

        return $this->render('reserva/detalle.html.twig', [
            'reserva' => $reserva,
        ]);
    }
}
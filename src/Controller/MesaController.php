<?php

namespace App\Controller;

use App\Entity\Mesa;
use App\Form\MesaType;
use App\Repository\MesaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesaController extends AbstractController
{
    #[Route('/mesa/modificar/{id}', 'mesa_modificar')]
    public function modificar(Request $request, MesaRepository $mesaRepository, Mesa $mesa) : Response
    {
        $form = $this->createForm(MesaType::class, $mesa);

        $form->handleRequest($request);

        $nuevo = $mesa->getId() === null;

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $mesaRepository->save();
                if ($nuevo){
                    $this->addFlash('success', 'Mesa creada con exito');
                }
                else {
                    $this->addFlash('success', 'Cambios guardados con exito');
                }
                return $this->redirectToRoute('modificar_ruta');
            }
            catch (\Exception $e){
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }

        return $this->render('mesa/modificar.html.twig', [
            'form' => $form->createView(),
            'mesa' => $mesa
        ]);
    }

    #[Route('/mesa/nueva', name: 'mesa_nueva')]
    public function nueva(Request $request, MesaRepository $mesaRepository) : Response
    {
        $mesa = new Mesa();
        $mesaRepository->add($mesa);

        return $this->modificar($request, $mesaRepository, $mesa);
    }

    #[Route('/mesa/eliminar/{id}', name: 'mesa_eliminar')]
    public function eliminar(Request $request, MesaRepository $mesaRepository, Mesa $mesa) : Response
    {
        if ($request->request->has('confirmar')){
            try {
                $mesaRepository->remove($mesa);
                $mesaRepository->save();
                $this->addFlash('success', 'Mesa eliminada con exito');
                return $this->redirectToRoute('mesa_listar');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar la mesa');
            }
        }

        return $this->render('mesa/eliminar.html.twig', [
            'mesa' => $mesa
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Plato;
use App\Form\PlatoType;
use App\Repository\PlatoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatoController extends AbstractController
{
    #[Route('/plato/modificar/{id}', name: 'plato_modificar')]
    public function modificar(Request $request, PlatoRepository $platoRepository, Plato $plato) : Response
    {
        $form = $this->createForm(PlatoType::class, $plato);

        $form->handleRequest($request);

        $nuevo = $plato->getId() === null;

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $platoRepository->save();
                if ($nuevo){
                    $this->addFlash('success', 'Plato creado con éxito');
                }
                else {
                    $this->addFlash('success', 'Plato modificado con éxito');
                }
                return $this->redirectToRoute('plato_listar');
            }
            catch (\Exception $e){
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }

        return $this->render('plato/modificar.html.twig', [
            'form' => $form->createView(),
            'plato' => $plato
        ]);
    }

    #[Route('/plato/nuevo', name: 'plato_nuevo')]
    public function nuevo(Request $request, PlatoRepository $platoRepository) : Response
    {
        $plato = new Plato();
        $platoRepository->add($plato);

        return $this->modificar($request, $platoRepository, $plato);
    }

    #[Route('/plato/eliminar/{id}', name: 'plato_eliminar')]
    public function eliminar(Request $request, PlatoRepository $platoRepository, Plato $plato) : Response
    {
        if ($request->request->has('confirmar')){
            try {
                $platoRepository->remove($plato);
                $platoRepository->save();
                $this->addFlash('success', 'Plato eliminado con exito');
                return $this->redirectToRoute('plato_listar');
            }
            catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar el plato');
            }
        }

        return $this->render('plato/eliminar.html.twig', [
            'plato' => $plato
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Plato;
use App\Form\PlatoType;
use App\Repository\PlatoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlatoController extends AbstractController
{
    #[Route('/plato/modificar/{id}', name: 'plato_modificar')]
    public function modificar(Request $request, PlatoRepository $platoRepository, Plato $plato, SluggerInterface $slugger) : Response
    {
        $form = $this->createForm(PlatoType::class, $plato);

        $form->handleRequest($request);

        $nuevo = $plato->getId() === null;

        if ($form->isSubmitted() && $form->isValid()){
            $fotoFile = $form->get('fotoFile')->getData();

            if ($fotoFile){
                $originalFilename = pathinfo($fotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename. '-' . uniqid() . '.' . $fotoFile->guessExtension();

                try {
                    $fotoFile->move(
                        $this->getParameter('platos_directory'),
                        $newFilename
                    );
                    $plato->setFoto($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error subiendo la imagen.');
                }
            }
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
    public function nuevo(Request $request, PlatoRepository $platoRepository, SluggerInterface $slugger) : Response
    {
        $plato = new Plato();
        $platoRepository->add($plato);

        return $this->modificar($request, $platoRepository, $plato, $slugger);
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
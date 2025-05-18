<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/', name: 'app_portada')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/entrar', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $ultUsuario = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'ultimo_usuario' => $ultUsuario
        ]);
    }

    #[Route('/salir', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Esto no se ejecuta');
    }
}
<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\CambiarClaveType;
use App\Form\RegistrationType;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use App\Security\Voter\UsuarioVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsuarioController extends AbstractController
{
    // Este es para personas nuevas
    #[Route('/registro', name: 'app_registro')]
    public function registro(
        Request $request,
        UsuarioRepository $usuarioRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(RegistrationType::class, $usuario);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashear la contraseña
            $hashedPassword = $passwordHasher->hashPassword(
                $usuario,
                $usuario->getPassword()
            );
            $usuario->setPassword($hashedPassword);

            // Guardar el usuario
            $usuarioRepository->add($usuario);
            $usuarioRepository->save();

            // Puedes redirigir, por ejemplo, al login
            return $this->redirectToRoute('app_login');
        }

        return $this->render('usuario/registro.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Este es solo para admins, igual que crear
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/usuario/modificar/{id}', name: 'usuario_modificar')]
    public function modificar(Request $request, UsuarioRepository $usuarioRepository, Usuario $usuario) : Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario);

        $form->handleRequest($request);

        $nuevo = $usuario->getId() === null;

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $usuarioRepository->save();
                if ($nuevo){
                    $this->addFlash('success', 'Usuario creado con éxito');
                }
                else {
                    $this->addFlash('success', 'Usuario modificado con éxito');
                }
                return $this->redirectToRoute('usuario_listar');
            }
            catch (\Exception $e){
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('usuario/modificar.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/usuario/nuevo', name: 'usuario_nuevo')]
    public function nuevo(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher) : Response
    {
        $usuario = new Usuario();
        $hashedPassword = $passwordHasher->hashPassword(
            $usuario,
            'cambiarClave'
        );
        $usuario->setPassword($hashedPassword);
        $usuarioRepository->add($usuario);

        return $this->modificar($request, $usuarioRepository, $usuario);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/usuario/eliminar/{id}', name: 'usuario_eliminar')]
    public function eliminar(Request $request, UsuarioRepository $usuarioRepository, Usuario $usuario) : Response
    {
        if ($request->request->has('confirmar')){
            try {
                $usuarioRepository->remove($usuario);
                $usuarioRepository->save();
                $this->addFlash('success', 'Usuario eliminado con exito');
                return $this->redirectToRoute('usuario_listar');
            }
            catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar el usuario');
            }
        }

        return $this->render('usuario/eliminar.html.twig', [
            'usuario' => $usuario
        ]);
    }

    #[IsGranted("ROLE_CAMARERO")]
    #[Route('/usuario/listar', name: 'usuario_listar')]
    public function listar(UsuarioRepository $usuarioRepository): Response
    {
        $usuarios = $usuarioRepository->findUsuarioOrdenadoPorNombre();

        return $this->render('usuario/listar.html.twig', [
            'usuarios' => $usuarios
        ]);
    }

    #[Route('/usuario/detalle/{id}', name: 'usuario_detalle', requirements: ['id'=>'\d+'])]
    public function usuarioDetalle(Usuario $usuario) : Response
    {
        $this->denyAccessUnlessGranted(UsuarioVoter::VIEW, $usuario);

        return $this->render('usuario/detalle.html.twig', [
            'usuario' => $usuario
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/cambiar-password', name: 'cambiar_password')]
    public function cambiarPassword(Request $request, UserPasswordHasherInterface $passwordHasher, UsuarioRepository $usuarioRepository): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(CambiarClaveType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('newPassword')->getData())
            );
            $usuarioRepository->save();
            $this->addFlash('success', 'Clave actualizada con éxito');
            return $this->redirectToRoute('usuario_detalle', ['id' => $user->getId()]);
        }

        return $this->render('usuario/cambiarPassword.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/cambiar-password/{id}', name: 'cambiar_user_password', requirements: ['id'=>'\d+'])]
    public function cambiarUserPassword(Request $request, UserPasswordHasherInterface $passwordHasher, UsuarioRepository $usuarioRepository, Usuario $user): Response
    {
        $form = $this->createForm(CambiarClaveType::class, $user, [
            'admin' => $user !== $this->getUser()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('newPassword')->getData())
            );
            $usuarioRepository->save();
            $this->addFlash('success', 'Clave actualizada con éxito');
            return $this->redirectToRoute('usuario_detalle', ['id' => $user->getId()]);
        }
        return $this->render('usuario/cambiarPassword.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
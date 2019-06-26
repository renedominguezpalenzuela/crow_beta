<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CreateInitialUserData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    function new (Request $request, UserPasswordEncoderInterface $encoder, CreateInitialUserData $datos_iniciales): Response {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            //...
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            //Obteniendo el kingdom seleccionado por el usuario
            $kingdom = $form['kingdom']->getData();
            $user->setKingdom($kingdom);

            //Encriptando password
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            //escribiendo datos del usuario en la BD
            $em->persist($user);
            $em->flush();

            //            $file = $form['file']->getData();
            //            $filename = sha1(md5(uniqid().microtime())).'.'.$file->getClientOriginalExtension();
            //            $file->move($this->getParameter('kernel.root_dir').'/../public/images/avatars', $filename);
            //            $user->setPhoto($filename);

            //-----------------------------------------------------
            //  Creando resto de los datos del usuario
            //-----------------------------------------------------
            $datos_iniciales->crear($user);

            $this->addFlash('success', 'Welcome ' . $user->getUsername() . '!');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        if ($this->getUser() != $user) {
            throw $this->createNotFoundException();
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }



    //-----------------------------------------------------------------------------------------------------
    // Modificar datos del usuario
    //-----------------------------------------------------------------------------------------------------
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        if ($this->getUser() != $user) {
            throw $this->createNotFoundException();
        }

     

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //actualizando el password
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}

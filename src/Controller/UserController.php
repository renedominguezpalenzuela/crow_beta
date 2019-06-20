<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

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
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            //...
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $kingdom = $form['kingdom']->getData();

            $em = $this->getDoctrine()->getManager();
//            $file = $form['file']->getData();
//            $filename = sha1(md5(uniqid().microtime())).'.'.$file->getClientOriginalExtension();

//            $file->move($this->getParameter('kernel.root_dir').'/../public/images/avatars', $filename);

            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
//            $user->setPhoto($filename);

            $em->persist($user);
            $em->flush();

            //busco el id de los tipos de castillo
            $castle_type_lv1 = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level'=>1]);
            $castle_type_lv2 = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level'=>2]);

            //busco si existe un castillo lv1 para el team de ese usuario
            $castillo =  $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType'=>$castle_type_lv1]);

            //busco si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
            if ($castillo==null){
                $castillo =  $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType'=>$castle_type_lv2]);
            }

            //si no existe castillo lvl1 ni lv2 lo creo
            if ($castillo==null){

                $castillo = new Building();
                $castillo->setUser($user);
                $castillo->setBuildingType($castle_type_lv1);

                $castillo->setDefenseRemaining($castle_type_lv1->getDefense());

                $em->persist($castillo);
            }

            //insert row to team
            $team = new Team();
            $team->setKingdom($kingdom);
            $team->setUser($user);
            $team->setGold(500000);

            //verify if kingdom has leader and id_player_boss

            if($kingdom->getIdKingdomBoss() == 0){
                $kingdom->setIdKingdomBoss($user->getId());
                $em->persist($kingdom);
            }
            $em->persist($team);
            $em->flush();

            $this->addFlash('success', 'Welcome '.$user->getUsername().'!');

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
        if($this->getUser() != $user){
            throw $this->createNotFoundException();
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        if($this->getUser() != $user){
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}

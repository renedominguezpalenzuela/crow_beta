<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    /**
     * @Route("/team/{security}", name="team_new")
     */
    public function index(Request $request, User $user)
    {
        $team = new Team();
        $form = $this->createForm('App\Form\TeamType', $team);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $team->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            $this->addFlash('You register successfully!');

            return $this->redirectToRoute('login');
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form->createView()
        ]);

        return $this->render('team/new.html.twig', [

        ]);
    }
}

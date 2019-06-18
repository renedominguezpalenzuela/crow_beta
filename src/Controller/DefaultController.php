<?php

namespace App\Controller;

use App\Entity\Kingdom;
use App\Entity\Team;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function comboKindom(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder()
            ->add('kingdom', EntityType::class, [
                'class' => 'App\Entity\Team',
                'placeholder' => 'Go to kingdom',
                'query_builder' => function (EntityRepository $er) use ($user){
                    return $er->createQueryBuilder('t')
                        ->where('t.user = :user')
                        ->setParameter('user', $user);
                }
            ])->getForm();

        $form->handleRequest($request);

        return $this->render('default/combo_kingdom.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Route("/go-kingdom/", name="go_kingdom")
     */
    public function goKingdom(Request $request)
    {
        $team = $this->getDoctrine()->getManager()->getRepository(Team::class)->find($request->get('form_kingdom'));

        return $this->render('default/kingdom.html.twig', [
            'team' => $team
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\BuildingType;
use App\Entity\Troop;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('login');
        }
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/player-army", name="dashboard_player_army")
     */
    public function playerArmyAction()
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('login');
        }

        $em = $this->getDoctrine()->getManager();
        //verify if user has any castle to create at the first time...

        $buildingCastle =  $em->getRepository(Building::class)->findCastle($this->getUser()->getId());
        $troops = $em->getRepository(Troop::class)->findBy(['user' => $this->getUser()]);

        if(!$buildingCastle){
            $building = new Building();
            $building->setBuildingType($em->getRepository(BuildingType::class)->findOneBy(['name' => 'Castle', 'level' => 1]));
            $building->setUser($this->getUser());
            $building->setDefenseRemaining(25000);
            $em->persist($building);

            $em->flush();

            $buildingCastle =  $em->getRepository(Building::class)->findCastle($this->getUser()->getId());
        }

        //

        return $this->render('dashboard/player_army.html.twig', [
            'buildingCastle' => $buildingCastle,
            'troops' => $troops
        ]);
    }

    /**
     * @Route("/kingdom-court", name="dashboard_kingdom_court")
     */
    public function kingdomCourtAction()
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('login');
        }

        return $this->render('dashboard/kingdom_court.html.twig');
    }

    /**
     * @Route("/world-map", name="dashboard_world_map")
     */
    public function worldMapAction()
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('login');
        }

        return $this->render('dashboard/world_map.html.twig');
    }
}

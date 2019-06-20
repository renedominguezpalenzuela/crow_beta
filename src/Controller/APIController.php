<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIController
 * @package App\Controller
 * @Route("/api")
 */
class APIController extends AbstractController
{

    /**
     * @param Request $request
     * @Route("/list_team_kingdom", name="list_team_kingdom")
     */
    public function TeamkingdomList(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getManager();
            $teams = $em->getRepository(Team::class)->findBy(['user' => $this->getUser()]);
            $list = [];

            foreach ($teams as $team) {
                $list[] = [
                    'id' => $team->getUser()->getId(),
                    'player' => $team->getUser()->getUsername(),
                    'gold' => $team->getGold(),
                    'role' => $team->getUser()->getRole() == 'ROLE_ADMIN' ? 'Administrator' : 'Player',
                    'kingdom_id' => $team->getKingdom()->getId(),
                    'kingdom_name' => $team->getKingdom()->getName(),
                    'kingdom_image' => $team->getKingdom()->getImage(),
                    'kingdom_boss' => $em->getRepository(User::class)->find($team->getKingdom()->getIdKingdomBoss())->getUsername(),
                ];
            }

            return $this->json($list, Response::HTTP_OK);
        }

        $message = [
            'result' => 'Error',
            'message' => 'User not authenticated',
        ];

        return $this->json($message, Response::HTTP_OK);
    }
}

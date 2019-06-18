<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{
    /**
     * @Route("/move_troops", name="move_troops")
     */
    public function move_troops()
    {
        $respuesta=null;

        return new Response(json_encode($respuesta));
      
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class APIController extends AbstractController
{
    /**
     * @Route("/move_troops", name="move_troops")
     */
    public function move_troops(Request $request)
    {

        if($this->isGranted('IS_AUTHENTICATED_FULLY')){

            $parametersAsArray = [];
            if ($content = $request->getContent()) {
                $parametersAsArray = json_decode($content, true);
            }



        $respuesta=array(
            'result'=>'error',
            'message'=>'Hay error',
        );

        return $this->json($respuesta);
    }


    //ERROR usuario no autenticado
    $respuesta=array(
        'result'=>'error',
        'message'=>'user not authenticated',
    );

    return $this->json($respuesta);
      
    }
}

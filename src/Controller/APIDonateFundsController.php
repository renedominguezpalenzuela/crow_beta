<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\GlobalConfig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class APIDonateFundsController extends AbstractController
{
    /**
     * @Route("/donatefunds", name="donate_funds")
     */
    public function index(Request $request, GlobalConfig $global_config)
    {
       
        $mensaje_error = "Not error found";
        $error = false;
        $resultado = '';

        $em = $this->getDoctrine()->getManager();
        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
            $user = $fake_user;

        } else {

            //--------------------------------------------------------------------------------------------------
            // Validando si usuario autenticado correctamente
            //--------------------------------------------------------------------------------------------------

            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {

                $respuesta = array(
                    'error' => true,
                    'message' => "User not authenticated",
                );

                return $this->json($respuesta, Response::HTTP_OK);

            }
            //usuario real si testing mode = false
            $user = $this->getUser();
        }

        //-------------------------------------------------------------------------------------------------
        //(2) Obteniendo los datos que vienen en la peticion
        //--------------------------------------------------------------------------------------------------
        $parametersAsArray = [];
        $content = $request->get("peticion");

        //variable que contendra el json como un arreglo
        $parametersAsArray = json_decode($content, true);

        //Datos del ataque: arreglo con tropas atacantes, id del edificio a atacar
         $Total_a_Donar = $parametersAsArray["total_a_donar"];


        
        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
            'totaladonar' =>$Total_a_Donar
        );

     
        return $this->json($respuesta, Response::HTTP_OK);

    }
}

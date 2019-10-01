<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\GlobalConfig;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;


//SOlo muestra la pagina, los datos son obtenidos con JS
//Funcionalidades de JS
//Mostrar lista de usuarios
//Al hacer clic sobre un usuario, mostrar sus recursos

class AdminAreaController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;

    }

    /**
     * @Route("/resources", name="lista-recursos")
     */
    public function adminAreaAction(Request $request, GlobalConfig $global_config)
    {

        

        $mensaje_error = "Not error found";
        $error = false;
        $resultado = '';

        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $this->em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
            $user = $fake_user;

        } else {

            //--------------------------------------------------------------------------------------------------
            // Validando si usuario autenticado correctamente
            //--------------------------------------------------------------------------------------------------
           /* if (!$this->isGranted('ROLE_ADMIN')) {
              return $this->redirectToRoute('login');
            }*/

            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {

                $respuesta = array(
                    'error' => true,
                    'message' => "User not authenticated",
                );

                //return $this->json($respuesta, Response::HTTP_OK);
                return $this->redirectToRoute('login');

            }
            //usuario real si testing mode = false
            $user = $this->getUser();
        }


        
       

        return $this->render('admin_area/admin_area.html.twig');

    }

}

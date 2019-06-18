<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\TroopBuilding;

/**
 * Class APIController
 * @package App\Controller
 * @Route("/api")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/move_troops", name="move_troops")
     */
    public function move_troops(Request $request)
    {
         $mensaje_error="Not error found";
         $error=false;

      //  if($this->isGranted('IS_AUTHENTICATED_FULLY')){

            $parametersAsArray = [];
           // if ($content = $request->getContent("peticion")) {
               $content = $request->get("peticion");
               $parametersAsArray = json_decode($content, true);
           // }


           //si longitud de $parametersAsArray<=0 then $error=true;

          //  var_dump($parametersAsArray);
                $em = $this->getDoctrine()->getManager();
           
                foreach ($parametersAsArray as $undato) {
                $from = $undato['from'];
                $to = $undato['to'];
                $troops = $undato['troops'];

                foreach ($troops as $unatropa) {
                    $troop_id=$unatropa['troops_id'];
                    $total_a_mover=$unatropa['total'];
                    var_dump($from.' '.$to.' '.$troop_id.' '.$total_a_mover);
 
                    //1) buscar en que edificio esta la tropa ubicada
                    //buscar en troop_building

          
                    //Validar en cada edificio solo debe existir una tupla troops - building
                    $tropa_edificio_old = $em->getRepository(TroopBuilding::class)->findOneBy(['troops'=>$troop_id, 'building'=>$from]);
                    //$tropa_edificio = $em->getRepository(TroopBuilding::class)->findAll();
                     var_dump($tropa_edificio_old);

                    //si no lo encuentra -- ERROR Terminar
                    if ($tropa_edificio_old==null) {
                        $mensaje_error="Troop don't exist on building";
                        $error=true;
                        break; //interrumpir el ciclo
                    }
                    //break;

                    //$tropa_edificio = $array_tropa_edificio[0];
                    

                    //2) comprobar que el total de tropas ubicadas sea mayor al que se quiere mover
                    
                       $total_edificio_actual = $tropa_edificio_old->getTotal();
                        if ($total_edificio_actual<$total_a_mover) {
                            $mensaje_error="Total Troop on building minor than movement";
                            $error=true;
                            break; 

                        }
                    


                    //3) comprobar que el edificio al que se quiere mover sea del equipo
                       //buscar en la tabla edificio y comprobar que el team es el mismo

                    //4) comprobar la capacidad del edificio si caben las tropas
                    //buscar en building type el id
                    //se obtiene la capacidad


                    //5) modificar el total del edificio donde se encuentran actualmente (si queda en cero, borrar la tupla)
                    $resto_edificio_old = $total_edificio_actual-$total_a_mover;
                    if ($resto_edificio_old<=0){
                        //Borrar
                        //falta
                    } else {
                        $tropa_edificio_old->setTotal($resto_edificio_old);
                    }
                    //6) escribir la nueva tupla en el nuevo edificio (si existia una tupla modificar su valor, si no crear una nueva tupla)
                    $tropa_edificio_new = new TroopBuilding();
                    $tropa_edificio_new=$tropa_edificio_old;
                    $tropa_edificio_new->setTotal($total_a_mover);

                    $em->persist($resto_edificio_old);
                    $em->persist($tropa_edificio_new);


                }
                // var_dump($from.' '.$to);

                

                if ( $error) {
                    break;
                }


                
            }
            

            $respuesta = array(
                'result'=>'ok',
                'message'=>'Success',
            );

<<<<<<< HEAD
            if (!$error) {
                $em->flush();

            }
=======
            return $this->json($respuesta);
        }
>>>>>>> 26402a327d155dc3917fd3b43006ea90de1dda86

        //ERROR usuario no autenticado
        $respuesta=array(
<<<<<<< HEAD
            'result'=>$error,
            'message'=>$mensaje_error,
        );

        return $this->json($respuesta);
    //}
=======
            'result'=>'error',
            'message'=>'User not authenticated',
        );

        return $this->json($respuesta);
      
    }
>>>>>>> 26402a327d155dc3917fd3b43006ea90de1dda86

    /**
     * @param Request $request
     * @Route("/list_team_kingdom", name="list_team_kingdom")
     */
    public function TeamkingdomList(Request $request)
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $em = $this->getDoctrine()->getManager();
            $teams = $em->getRepository(Team::class)->findBy(['user' => $this->getUser()]);
            $list = [];

            foreach ($teams as $team){
                $list [] = [
                    'id' => $team->getUser()->getId(),
                    'player' => $team->getUser()->getUsername(),
                    'gold' => $team->getGold(),
                    'role' => $team->getUser()->getRole() == 'ROLE_ADMIN' ? 'Administrator' : 'Player',
                    'kingdom_id' => $team->getKingdom()->getId(),
                    'kingdom_name' => $team->getKingdom()->getName(),
                    'kingdom_image' => $team->getKingdom()->getImage(),
                    'kingdom_boss' => $em->getRepository(User::class)->find($team->getKingdom()->getIdKingdomBoss())->getUsername()
                ];
            }

            return $this->json($list, Response::HTTP_OK);
        }

        $message = [
            'result' => 'Error',
            'message' => 'User not authenticated'
        ];

        return $this->json($message, Response::HTTP_OK);
    }
}

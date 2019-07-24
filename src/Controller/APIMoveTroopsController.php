<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Troop;
use App\Entity\TroopBuilding;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIMoveTroopsController
 * @package App\Controller
 * @Route("/api")
 */
class APIMoveTroopsController extends AbstractController
{


    //TODO: si el edificio From es un SQUAD y se queda vacio borrarlo
    //Extructura nueva
    //["{"troops_id":13,"total":2,from":16,"to":16}"]
    //
    /**
     * @Route("/move_troops", name="move_troops", methods={"POST"})
     */
    
    public function move_troops(Request $request)
    {
        $mensaje_error = "Not error found";
        $error = false;

        //--------------------------------------------------------------------------------------------------
        // Validando si usuario autenticado correctamente
        //--------------------------------------------------------------------------------------------------

        /*  if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){

        $respuesta=array(
        'error'=>false,
        'message'=>"User not authenticated",
        );

        return $this->json($respuesta, Response::HTTP_OK);

        }*/

        //-------------------------------------------------------------------------------------------------
        //(1) Obteniendo los datos que vienen en la peticion
        //--------------------------------------------------------------------------------------------------

        $parametersAsArray = [];
        $content = $request->get("peticion");
       
    
       

        //Arreglo que contendra el json como un arreglo
        
        $parametersAsArray = json_decode($content, true);

        //si longitud de $parametersAsArray<=0 then $error=true;

      
          
        $em = $this->getDoctrine()->getManager();

        //--------------------------------------------------------------------------------
        //(2) Recorriendo los datos
        //--------------------------------------------------------------------------------
        foreach ($parametersAsArray as $undato) {

            $from = $undato['from'];
            $to = $undato['to'];       
            $troop_id = $undato['troops_id'];
            $total_a_mover = $undato['total'];


          

         //   foreach ($troops as $unatropa) {

                //$resto_edificio_old = $total_edificio_old - $total_a_mover;

                if ($from == $to) {
                    $mensaje_error = "From building same as To Building from id" . $from . " to id " . $to;
                    $error = true;
                    break;
                }

               
                //var_dump("From " . $from . ' To ' . $to . ' Troop ' . $troop_id . ' ' . $total_a_mover);

                //todo: Validar que numeros no sean negativos ni cero

                //2.1) buscar en que edificio esta la tropa ubicada
                //buscar en troop_building

                //TODO: Validar en cada edificio solo debe existir una tupla troops - building
                $tropa_edificio_old = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $from]);

                //Obtener nombres de edificios para mensajes de error
                //$edificio_old = new Building();
                // $edificio_old =$tropa_edificio_old->getBuilding();
                $edificio_old = $em->getRepository(Building::class)->find($from);
                if ($edificio_old == null) {
                    $mensaje_error = "From building don't exist: (From building id: " . $from . " )";
                    $error = true;
                    break;
                }

                if ($tropa_edificio_old == null) {
                    $mensaje_error = "Troops don't exist on From building: (from building id: " . $from . " troop id: " . $troop_id . " )";
                    $error = true;
                    break;
                }

                $edificio_old_name = $edificio_old->getBuildingType()->getName();

                $edificio_new = $em->getRepository(Building::class)->find($to);

                if ($edificio_new == null) {
                    $mensaje_error = "To building don't exist: (To building id: " . $to . " )";
                    $error = true;
                    break;
                }

                $edificio_new_name = $edificio_new->getBuildingType()->getName();

                //Obtener nombre de la tropa para mensajes de error
                $troops = $em->getRepository(Troop::class)->find($tropa_edificio_old->getTroops());

                //Validar: que exista la tropa en el edificio
                if ($tropa_edificio_old == null) {
                    $mensaje_error = "Troop don't exist on building " . $edificio_old_name;
                    $error = true;
                    break; //interrumpir el ciclo
                };

                //2.2) comprobar que el total de tropas ubicadas sea mayor al que se quiere mover
                // si es menor -- error terminar

                $total_edificio_old = $tropa_edificio_old->getTotal();

               // var_dump("total_edificio_old " . $total_edificio_old . " total a mover " . $total_a_mover);

                //VALIDAR: si total de tropas a mover es mayor que las que hay en el edificio error
                if ($total_edificio_old < $total_a_mover) {

                    $nombre_edificio = $tropa_edificio_old->getBuilding()->getName2();
                    $mensaje_error = "Total Troop on building minor than movement. Building id: " . strval($tropa_edificio_old->getID()) . " " . $nombre_edificio;
                    $error = true;
                    break;

                }

                //3) comprobar que el edificio al que se quiere mover sea del equipo
                //buscar en la tabla edificio y comprobar que el team es el mismo

                //4) comprobar la capacidad del edificio si caben las tropas
                //buscar en building type el id
                //se obtiene la capacidad

                //5) modificar el total del edificio donde se encuentran actualmente (si queda en cero, borrar la tupla)
                $resto_edificio_old = $total_edificio_old - $total_a_mover;

                //var_dump("resto_edificio_old " . $resto_edificio_old);

                if ($resto_edificio_old > 0) {
                    $tropa_edificio_old->setTotal($resto_edificio_old);
                }

                //6) escribir la nueva tupla en el nuevo edificio (si existia una tupla modificar su valor, si no crear una nueva tupla)

                //Verificar si existe la tupla del edificio nuevo con las tropas, si existe se actualiza
                //si no existe se crea

                //TODO: Validar en cada edificio solo debe existir una tupla troops - building
                $tropa_edificio_new = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $to]);

                if ($tropa_edificio_new == null) {
                    $tropa_edificio_new = new TroopBuilding();
                    $tropa_edificio_new->setTroops($troops);
                    $tropa_edificio_new->setBuilding($edificio_new);
                    $tropa_edificio_new->setTotal(0);
                }

                //Calculo la nueva cantidad de tropas en el edificio new
                $total_inicial_edificio_new = $tropa_edificio_new->getTotal() + $total_a_mover;

                $tropa_edificio_new->setTotal($total_inicial_edificio_new);

                //si llega a 0 las tropas en el edificio old la tupla se elimina
                if ($resto_edificio_old == 0) {
                    //var_dump("borrando");
                  
                    $em->remove($tropa_edificio_old);
                    $em->flush();
                } else {
                    $em->persist($tropa_edificio_old);
                }

                $em->persist($tropa_edificio_new);

          //  } //END Ciclo   foreach ($troops as $unatropa)

            if ($error) {
                break;
            }

        } //END Ciclo  foreach ($parametersAsArray as $undato)

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
        );

        if (!$error) {
            $em->flush();

        }
        return $this->json($respuesta, Response::HTTP_OK);

    }




    //Extructura vieja de json
    //[{"from":17,"to":19, "troops":[{"troops_id":16,"total":10}]}]
    /**
     * @Route("/move_troopsOLD", name="move_troopsOLD", methods={"POST"})
     */
    
    public function move_troopsOLD(Request $request)
    {
        $mensaje_error = "Not error found";
        $error = false;

        //--------------------------------------------------------------------------------------------------
        // Validando si usuario autenticado correctamente
        //--------------------------------------------------------------------------------------------------

        /*  if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){

        $respuesta=array(
        'error'=>false,
        'message'=>"User not authenticated",
        );

        return $this->json($respuesta, Response::HTTP_OK);

        }*/

        //-------------------------------------------------------------------------------------------------
        //(1) Obteniendo los datos que vienen en la peticion
        //--------------------------------------------------------------------------------------------------

        $content = $request->get("peticion");

        //Arreglo que contendra el json como un arreglo
        $parametersAsArray = [];
        $parametersAsArray = json_decode($content, true);

        //si longitud de $parametersAsArray<=0 then $error=true;

        //  var_dump($parametersAsArray);
        $em = $this->getDoctrine()->getManager();

        //--------------------------------------------------------------------------------
        //(2) Recorriendo los datos
        //--------------------------------------------------------------------------------
        foreach ($parametersAsArray as $undato) {

            $from = $undato['from'];
            $to = $undato['to'];
            $troops = $undato['troops'];

            foreach ($troops as $unatropa) {

                //$resto_edificio_old = $total_edificio_old - $total_a_mover;

                if ($from == $to) {
                    $mensaje_error = "From building same as To Building from id" . $from . " to id " . $to;
                    $error = true;
                    break;
                }

                $troop_id = $unatropa['troops_id'];
                $total_a_mover = $unatropa['total'];
                //var_dump("From " . $from . ' To ' . $to . ' Troop ' . $troop_id . ' ' . $total_a_mover);

                //todo: Validar que numeros no sean negativos ni cero

                //2.1) buscar en que edificio esta la tropa ubicada
                //buscar en troop_building

                //TODO: Validar en cada edificio solo debe existir una tupla troops - building
                $tropa_edificio_old = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $from]);

                //Obtener nombres de edificios para mensajes de error
                //$edificio_old = new Building();
                // $edificio_old =$tropa_edificio_old->getBuilding();
                $edificio_old = $em->getRepository(Building::class)->find($from);
                if ($edificio_old == null) {
                    $mensaje_error = "From building don't exist: (From building id: " . $from . " )";
                    $error = true;
                    break;
                }

                if ($tropa_edificio_old == null) {
                    $mensaje_error = "Troops don't exist on From building: (from building id: " . $from . " troop id: " . $troop_id . " )";
                    $error = true;
                    break;
                }

                $edificio_old_name = $edificio_old->getBuildingType()->getName();

                $edificio_new = $em->getRepository(Building::class)->find($to);

                if ($edificio_new == null) {
                    $mensaje_error = "To building don't exist: (To building id: " . $to . " )";
                    $error = true;
                    break;
                }

                $edificio_new_name = $edificio_new->getBuildingType()->getName();

                //Obtener nombre de la tropa para mensajes de error
                $troops = $em->getRepository(Troop::class)->find($tropa_edificio_old->getTroops());

                //Validar: que exista la tropa en el edificio
                if ($tropa_edificio_old == null) {
                    $mensaje_error = "Troop don't exist on building " . $edificio_old_name;
                    $error = true;
                    break; //interrumpir el ciclo
                };

                //2.2) comprobar que el total de tropas ubicadas sea mayor al que se quiere mover
                // si es menor -- error terminar

                $total_edificio_old = $tropa_edificio_old->getTotal();

               // var_dump("total_edificio_old " . $total_edificio_old . " total a mover " . $total_a_mover);

                //VALIDAR: si total de tropas a mover es mayor que las que hay en el edificio error
                if ($total_edificio_old < $total_a_mover) {

                    $nombre_edificio = $tropa_edificio_old->getBuilding()->getName();
                    $mensaje_error = "Total Troop on building minor than movement. Building id: " . strval($tropa_edificio_old->getID()) . " " . $nombre_edificio;
                    $error = true;
                    break;

                }

                //3) comprobar que el edificio al que se quiere mover sea del equipo
                //buscar en la tabla edificio y comprobar que el team es el mismo

                //4) comprobar la capacidad del edificio si caben las tropas
                //buscar en building type el id
                //se obtiene la capacidad

                //5) modificar el total del edificio donde se encuentran actualmente (si queda en cero, borrar la tupla)
                $resto_edificio_old = $total_edificio_old - $total_a_mover;

                //var_dump("resto_edificio_old " . $resto_edificio_old);

                if ($resto_edificio_old > 0) {
                    $tropa_edificio_old->setTotal($resto_edificio_old);
                }

                //6) escribir la nueva tupla en el nuevo edificio (si existia una tupla modificar su valor, si no crear una nueva tupla)

                //Verificar si existe la tupla del edificio nuevo con las tropas, si existe se actualiza
                //si no existe se crea

                //TODO: Validar en cada edificio solo debe existir una tupla troops - building
                $tropa_edificio_new = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $to]);

                if ($tropa_edificio_new == null) {
                    $tropa_edificio_new = new TroopBuilding();
                    $tropa_edificio_new->setTroops($troops);
                    $tropa_edificio_new->setBuilding($edificio_new);
                    $tropa_edificio_new->setTotal(0);
                }

                //Calculo la nueva cantidad de tropas en el edificio new
                $total_inicial_edificio_new = $tropa_edificio_new->getTotal() + $total_a_mover;

                $tropa_edificio_new->setTotal($total_inicial_edificio_new);

                //si llega a 0 las tropas en el edificio old la tupla se elimina
                if ($resto_edificio_old == 0) {
                    //var_dump("borrando");
                  
                    $em->remove($tropa_edificio_old);
                    $em->flush();
                } else {
                    $em->persist($tropa_edificio_old);
                }

                $em->persist($tropa_edificio_new);

            } //END Ciclo   foreach ($troops as $unatropa)

            if ($error) {
                break;
            }

        } //END Ciclo  foreach ($parametersAsArray as $undato)

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
        );

        if (!$error) {
            $em->flush();

        }
        return $this->json($respuesta, Response::HTTP_OK);

    }

}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIListUserResourcesController
 * @package App\Controller
 * @Route("/api")
 */
class APIListUserResourcesController extends AbstractController
{
    /**
     * @Route("/list_user_resources", name="list_user_resources")
     */
    public function list_user_resources(Request $request)
    {
        $mensaje_error = "Not error found";
        $error = false;

        /*  if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){

        $respuesta=array(
        'error'=>false,
        'message'=>"User not authenticated",
        );

        return $this->json($respuesta);

        }*/

        $parametersAsArray = [];

        
        

        //Obtengo user_id de la peticion


        /*
        { 
            { 
             "team" : [ {"team_id": id_team, "kingdom_name" : "nombre_kingdom"}],
             "castle" : [{"kingdom_id": id_castillo, "level": level_castillo, "capacity": capacidad, "defense_remaining": defense_remaining}],
             "buildings" : [
                             {"building_id" : id, "building_name":"name", "capacity":x , "filled":capacidades_ocupadas, "defense_remaining": defense_remaining, "level":level },                            
                 
                           ]

             "troops" : [
                            {"troop_id" : id, "troop_name":"name", "level":level, "total":total, "attack":attack, "defense":defense, "damage":damage, "speed":speed                                
                        ]
             ""troops_location":[
                        {"troop_id" : id, "troop_location":id_building, "total":x },

             ]
                                      

             }

            */


        //Respuesta
        $respuesta=array(
            'castle'=>false,
            'message'=>"User not authenticated",
            );
    
       return $this->json($respuesta);

    }

}

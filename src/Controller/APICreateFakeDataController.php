<?php

namespace App\Controller;

use App\Controller\API\CreateTestUser;
use App\Entity\Kingdom;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\BuildingType;
use App\Entity\Building;
use App\Entity\UnitType;
use App\Entity\Troop;
use App\Entity\TroopBuilding;

/**
 * Class APICreateFakeDataController
 * @package App\Controller
 * @Route("/api")
 */
class APICreateFakeDataController extends AbstractController
{

    /**
     * @Route("/create_fake_data", name="create_fake_data")
     */
    public function create_fake_data(UserPasswordEncoderInterface $encoder)
    {

        $this->borrarDatosPrueba();
        $em = $this->getDoctrine()->getManager();

        $mensaje_error = "Not error found";
        $error = false;

        //https://symfony.com/doc/4.0/security/password_encoding.html

        // Creando un usuario
        $user = new User();

        $user->setEmail("axl@correo.cu");
        $user->setUsername("axl");
        $user->setName("axl");

        $plainPassword = '123';
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);

        $em->persist($user);
        $em->flush();

        //Asignar al primer kingdom
        $kingdom = $em->getRepository(Kingdom::class)->findOneBy(['name' => 'Test Kingdom']);

        //verify if kingdom has leader and id_player_boss
       // if ($kingdom->getIdKingdomBoss() == 0) {
            $kingdom->setIdKingdomBoss($user->getId());
            $em->persist($kingdom);
        //}

        //Creando el Team
        $team = new Team();
        $team->setKingdom($kingdom);
        $team->setUser($user);
        $team->setGold(500000);
        $em->persist($team);

        $em->flush();

        //Guardarlo en BD

        $em->persist($user);
        $em->flush();

        //Crear el resto de los datos del usuario
        $this->CreateFakeBuildings($user);

        //Crear tropas ubiacarlas inicialmente en la barraca
        $this->CreateFakeTroops($user);

        //Respuesta
        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
        );

        //$this->borrarDatosPrueba();
        return $this->json($respuesta);

    }

    public function list_fake_user_resources(Request $request)
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

        $usuario = new CreateTestUser();
        $usuario->CrearUsuario("123", "axl@correo.cu");

        //Respuesta
        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
        );

        return $this->json($respuesta);

    }

    private function borrarDatosPrueba()
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['name' => 'axl']);

        //Borrar el team del user
        if ($user != null) {
            $team = $em->getRepository(Team::class)->findOneBy(['user' => $user->getID()]);
            if ($team != null) {
                $em->remove($team);
            }
        }
      

        //Borrar los troops en edificios del user


    

          //borrar los edificios del user (no aplicar en produccion puede borrar el castillo)

          //borrar las tropas (y borrarlas de los edificios)
          $troops = $em->getRepository(Troop::class)->findBy(['user' => $user]);
          foreach ($troops as $untroop) {
              //eliminar todos las tropas del usuario
              $troopsallocated = $em->getRepository(TroopBuilding::class)->findBy(['troops' => $untroop]);
              foreach ($troopsallocated as $untroopallocated) {
                  $em->remove($untroopallocated);    
              }
              $em->remove($untroop);           
          }


              //borrar los edificios del user (no aplicar en produccion puede borrar el castillo)
        $buildings = $em->getRepository(Building::class)->findBy(['user' => $user]);
        foreach ($buildings as $unbuilding) {
            //eliminar todos los edificios del usuario
            $em->remove($unbuilding);           
        }
  


        //borrar el user
        if ($user != null) {
            $em->remove($user);
        }

        $em->flush();

    }



    private function CreateFakeBuildings(User $user){
          //Crear el castillo del usuario si no existe
          //buscar para el usuario si existe el castillo
          $em = $this->getDoctrine()->getManager();
          //busco usuario
          $user = $em->getRepository(User::class)->findOneBy(['name' => 'axl']);
          
          //busco team
          $team= $em->getRepository(Team::class)->findOneBy(['user' => $user->getID()]);
          
          //busco el Castle 
          //busco el boss
          $kingdom = $team-> getKingdom();

          $id_user_boss = $kingdom->getIdKingdomBoss();

         // var_dump("Kingdom boss id ".$user_boss_id);

         //busco el id de los tipos de castillo
         $castle_type_lv1 = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level'=>1]);
         $castle_type_lv2 = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level'=>2]);
         
         //busco si existe un castillo lv1 para el team de ese usuario
         $castillo =  $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType'=>$castle_type_lv1]);

         //busco si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
         if ($castillo==null){
            $castillo =  $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType'=>$castle_type_lv2]);
         }

         //si no existe castillo lvl1 ni lv2 lo creo
         if ($castillo==null){

             $castillo = new Building();
             $castillo->setUser($user);
             $castillo->setBuildingType($castle_type_lv1);

             $castillo->setDefenseRemaining($castle_type_lv1->getDefense());

             $em->persist($castillo);
             $em->flush();
         }


         //Crear barracks
          $barrack_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Barrack', 'level'=>1]);
          $barrack = new Building();

          $barrack->setUser($user);
          $barrack->setBuildingType($barrack_type);

          $barrack->setDefenseRemaining($barrack_type->getDefense());

          $em->persist($barrack);
          $em->flush();
                 

          //Crear tres camps
          $camp_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Camp', 'level'=>1]);
          for ($i=0; $i <3 ; $i++) { 
            
            $camp = new Building();
  
            $camp->setUser($user);
            $camp->setBuildingType($camp_type);
  
            $camp->setDefenseRemaining($camp_type->getDefense());
  
            $em->persist($camp);
              
          }
          
          $em->flush();
    }



    //Crear tropas ubicarlas inicialmente en la barraca
    private function CreateFakeTroops(User $user){
        $em = $this->getDoctrine()->getManager();

        $troops_type = $em->getRepository(UnitType::class)->findBy(['level' => '1']);

        //Buscar la barraca
        $barrack_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Barrack', 'level'=>1]);
        $barraca =  $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType'=>$barrack_type]);

      


        foreach ($troops_type as $untrooptype) {
            $troop = new Troop();
            $troop->setUser($user);
            $troop->setLevel($untrooptype->getLevel());
            $troop->setTotal($untrooptype->getTotalInitial());
            $troop->setAttack($untrooptype->getAttack());
            $troop->setDefense($untrooptype->getDefense());
            $troop->setDamage($untrooptype->getDamage());
            $troop->setSpeed($untrooptype->getSpeed());
            $troop->setUnitType($untrooptype);

            $em->persist($troop);

            $tropa_ubicada = new TroopBuilding();
            $tropa_ubicada->setTroops($troop);
            $tropa_ubicada->setBuilding($barraca);
            $tropa_ubicada->setTotal($troop->getTotal());

            $em->persist($tropa_ubicada);
          
        }

        $em->flush();
        



    }



}

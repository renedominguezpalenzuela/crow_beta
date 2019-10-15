<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Kingdom;
use App\Entity\BuildingType;
use App\Entity\UnitType;
use App\Entity\Config;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\EventType;
use App\Entity\TimeEvent;
use App\Service\Utiles;



class AppFixtures extends Fixture
{


    private $encoder;
    private $utiles;

    public function __construct( UserPasswordEncoderInterface $encoder,Utiles $utiles ){
        $this->utiles = $utiles;
        $this->encoder = $encoder;
    }

    //public function crearUsuarioAdmin(ObjectManager $manager, ){

    
  
    public function load(ObjectManager $manager)
    {
        

        //------------------------------------------------------------------------
        //Configuracion global
        //------------------------------------------------------------------------
        $config = new Config();
        $config->setTesting(false);
        $config->setGoldIni(500000);
        $config->setGoldIncrement(100000);
        $config->setTest_user('axl');
        $manager->persist($config);


        //------------------------------------------------------------------------
        //Creando los kingdoms
        //------------------------------------------------------------------------
        $kingdoms = array(           
            ['id'=>'1', 'name'=>'Red Kingdom', 'image'=>'castillo1.jpg', 'color_class'=>'card-header-danger'] ,           
            ['id'=>'2', 'name'=>'Blue Kingdom', 'image'=>'castillo2.jpg', 'color_class'=>'card-header-info'] ,
            ['id'=>'3', 'name'=>'Orange Kingdom', 'image'=>'castillo3.jpg', 'color_class'=>'card-header-warning'] ,
            ['id'=>'4', 'name'=>'White Kingdom', 'image'=>'castillo4.jpg', 'color_class'=>'card-header-white'] ,
            ['id'=>'5', 'name'=>'Green Kingdom', 'image'=>'castillo5.jpg', 'color_class'=>'card-header-success'] ,                   
            ['id'=>'6', 'name'=>'Test Kingdom', 'image'=>'castillo6.jpg', 'color_class'=>'card-header-white']                                
        );
  
        foreach ($kingdoms as $unkingdom) {
            $kingdom = new Kingdom();
            $kingdom->setName($unkingdom['name']);
            $kingdom->setImage($unkingdom['image']);
            $kingdom->setIdKingdomBoss(0);
            $kingdom->setColor_class($unkingdom['color_class']);
            $manager->persist($kingdom);   
        }

        $manager->flush();

        //------------------------------------------------------------------------
        //Creando los tipos de edificios
        //------------------------------------------------------------------------
        $buildings = array(
            ['id'=>1, 'name'=>'Castle', 'cost'=>0, 'level'=>1, 'capacity'=>25000, 'defense'=>500000, 'minimalUnit'=>0],
            ['id'=>2, 'name'=>'Castle', 'cost'=>10000000, 'level'=>2, 'capacity'=>25000, 'defense'=>1000000, 'minimalUnit'=>0],
            ['id'=>3, 'name'=>'Barrack', 'cost'=>0, 'level'=>1, 'capacity'=>0, 'defense'=>0, 'minimalUnit'=>0],
            ['id'=>4, 'name'=>'Camp', 'cost'=>1000000, 'level'=>1, 'capacity'=>5000, 'defense'=>50000, 'minimalUnit'=>1000],
            ['id'=>5, 'name'=>'Camp', 'cost'=>2000000, 'level'=>2, 'capacity'=>10000, 'defense'=>100000, 'minimalUnit'=>0]           
        );

//        ['id'=>6, 'name'=>'Squad', 'cost'=>0, 'level'=>1, 'capacity'=>0, 'defense'=>0, 'minimalUnit'=>0],

        foreach ($buildings as $unbuilding) {
            $building = new BuildingType();
            $building->setName($unbuilding['name']);
            $building->setCost($unbuilding['cost']);
            $building->setLevel($unbuilding['level']);
            $building->setCapacity($unbuilding['capacity']);
            $building->setDefense($unbuilding['defense']);
            $building->setMinimalUnit($unbuilding['minimalUnit']);
            
            $manager->persist($building);  
        
        }


        //------------------------------------------------------------------------
        //Creando los tipos de tropas: UnitType
        //------------------------------------------------------------------------
    

        $units_data = array(
            ['id'=>1, 'name'=>'Archers', 'level'=>1, 'attack'=>30, 'defense'=>20, 'damage'=>0, 'speed'=>6, 'cost'=>50, 'total_ini'=>200],
            ['id'=>2, 'name'=>'Spearman', 'level'=>1, 'attack'=>50, 'defense'=>40, 'damage'=>10, 'speed'=>5, 'cost'=>100, 'total_ini'=>150],
            ['id'=>3, 'name'=>'Axemen', 'level'=>1, 'attack'=>50, 'defense'=>50, 'damage'=>100, 'speed'=>4, 'cost'=>200, 'total_ini'=>100],            
            ['id'=>4, 'name'=>'Light Cavalry', 'level'=>1, 'attack'=>100, 'defense'=>100, 'damage'=>50, 'speed'=>10, 'cost'=>2000, 'total_ini'=>50],                                    
        );

        foreach ($units_data as $ununit) {
            $unit = new UnitType();
            $unit->setName($ununit['name']);
            $unit->setLevel($ununit['level']);
            $unit->setAttack($ununit['attack']);
            $unit->setDefense($ununit['defense']);
            $unit->setDamage($ununit['damage']);
            $unit->setSpeed($ununit['speed']);
            $unit->setCost($ununit['cost']);
            $unit->setTotalInitial($ununit['total_ini']);
           
            $manager->persist($unit);   
        }


        //------------------------------------------------------------------------
        // Tipos de Eventos
        //------------------------------------------------------------------------

        $event_type_data = array(
            ['id'=>1, 'name'=>'increment_gold', 'periodic'=>true, 't_ejec_d'=>1, 't_ejec_h'=>0, 't_ejec_m'=>0, 't_ejec_s'=>0, 'explanation'=>'Daily increment of gold for all users'],
            ['id'=>2, 'name'=>'repair_building', 'periodic'=>false, 't_ejec_d'=>0, 't_ejec_h'=>0, 't_ejec_m'=>5, 't_ejec_s'=>0, 'explanation'=>'Repair a building']          
        );

        foreach ($event_type_data as $uneventtype) {
            $eventtype = new EventType();
            $eventtype->setName($uneventtype['name']);
            $eventtype->setPeriodic($uneventtype['periodic']);
            $eventtype->setTEjecD($uneventtype['t_ejec_d']);
            $eventtype->setTEjecH($uneventtype['t_ejec_h']);
            $eventtype->setTEjecM($uneventtype['t_ejec_m']);
            $eventtype->setTEjecS($uneventtype['t_ejec_s']);
            
           
            $manager->persist($eventtype);   
        }

        $manager->flush();

        //------------------------------------------------------------------------
        // Agregando Eventos Periodicos
        //------------------------------------------------------------------------

        //Obtener todos los tipos de eventos
//        $tropa_edificio_old = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $from]);

        $TiposdeEventos = $manager->getRepository(EventType::class)->findBy(['periodic' => true]);




        $FechaAhora = new \DateTime();
       // $fecha_ejecucion = new \DateTime();

       
       
      // echo "Time zone ". $FechaAhora->getTimezone()->getName();

        foreach ($TiposdeEventos as $uneventtype) {

           $fecha_ejecucion =  $this->utiles->sumaraFecha($FechaAhora,1,1,5,5);

            $eventos = new TimeEvent();
            $eventos->setEventType($uneventtype);
            $eventos->setActive(true);
            $eventos->setTIni($FechaAhora);
           // $eventos->setTLastCheck($FechaAhora);

          
            $eventos->setTejec($fecha_ejecucion);
           
           
           
            $manager->persist($eventos);   
        }




        //------------------------------------------------------------------------
        //Usuario Admin
        //------------------------------------------------------------------------
        
       //actualizando el password
       $user = new User();
       $user->setUsername("admin");
       $user->setName("admin");
       $user->setRole("ROLE_ADMIN");
       
       $encoded = $this->encoder->encodePassword($user, "admin");
       $user->setPassword($encoded);
       $kingdom = $manager->getRepository(Kingdom::class)->findOneBy(['name' => 'Test Kingdom']);
       $user->setKingdom($kingdom);

       $manager->persist($user);


    

        //------------------------------------------------------------------------
        //escribiendo en BD
        //------------------------------------------------------------------------
    

        $manager->flush();
    }


   

}

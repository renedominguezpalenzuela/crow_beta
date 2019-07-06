<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Kingdom;
use App\Entity\BuildingType;
use App\Entity\UnitType;
use App\Entity\Config;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        

        //------------------------------------------------------------------------
        //Configuracion global
        //------------------------------------------------------------------------
        $config = new Config();
        $config->setTesting(false);
        $config->setGoldIni(500000);
        $manager->persist($config);


        //------------------------------------------------------------------------
        //Creando los kingdoms
        //------------------------------------------------------------------------
        $kingdoms = array(
            ['id'=>'1', 'name'=>'White Kingdom', 'image'=>'castillo1.jpg'] ,
            ['id'=>'2', 'name'=>'Black Kingdom', 'image'=>'castillo2.jpg'] ,
            ['id'=>'3', 'name'=>'Red Kingdom', 'image'=>'castillo3.jpg'] ,
            ['id'=>'4', 'name'=>'Blue Kingdom', 'image'=>'castillo4.jpg'] ,
            ['id'=>'5', 'name'=>'Yellow Kingdom', 'image'=>'castillo5.jpg'],
            ['id'=>'6', 'name'=>'Test Kingdom', 'image'=>'castillo5.jpg']                    
        );
          //['id'=>'6', 'name'=>'Green Kingdom'], 
            //['id'=>'7', 'name'=>'Orange Kingdom']    
        foreach ($kingdoms as $unkingdom) {
            $kingdom = new Kingdom();
            $kingdom->setName($unkingdom['name']);
            $kingdom->setImage($unkingdom['image']);
            $kingdom->setIdKingdomBoss(0);
            $manager->persist($kingdom);   
        }


        //------------------------------------------------------------------------
        //Creando los tipos de edificios
        //------------------------------------------------------------------------
        $buildings = array(
            ['id'=>1, 'name'=>'Castle', 'cost'=>0, 'level'=>1, 'capacity'=>25000, 'defense'=>500000, 'minimalUnit'=>0],
            ['id'=>2, 'name'=>'Castle', 'cost'=>10000000, 'level'=>2, 'capacity'=>25000, 'defense'=>1000000, 'minimalUnit'=>0],
            ['id'=>3, 'name'=>'Barrack', 'cost'=>0, 'level'=>1, 'capacity'=>0, 'defense'=>0, 'minimalUnit'=>0],
            ['id'=>4, 'name'=>'Camp', 'cost'=>1000000, 'level'=>1, 'capacity'=>5000, 'defense'=>50000, 'minimalUnit'=>1000],
            ['id'=>5, 'name'=>'Camp', 'cost'=>2000000, 'level'=>2, 'capacity'=>10000, 'defense'=>100000, 'minimalUnit'=>0],
            ['id'=>6, 'name'=>'Squad', 'cost'=>0, 'level'=>1, 'capacity'=>0, 'defense'=>0, 'minimalUnit'=>0],
        );

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
        //escribiendo en BD
        //------------------------------------------------------------------------
    

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Kingdom;
use App\Entity\BuildingType;
use App\Entity\UnitType;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $kingdoms = array(
            ['id'=>'1', 'name'=>'White Kingdom', 'image'=>'castillo1.jpg'] ,
            ['id'=>'2', 'name'=>'Black Kingdom', 'image'=>'castillo2.jpg'] ,
            ['id'=>'3', 'name'=>'Red Kingdom', 'image'=>'castillo3.jpg'] ,
            ['id'=>'4', 'name'=>'Blue Kingdom', 'image'=>'castillo4.jpg'] ,
            ['id'=>'5', 'name'=>'Yellow Kingdom', 'image'=>'castillo5.jpg']                 
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


        $buildings = array(
            ['id'=>1, 'name'=>'Castle', 'cost'=>0, 'level'=>1, 'capacity'=>25000, 'defense'=>50000, 'minimalUnit'=>0],
            ['id'=>2, 'name'=>'Castle', 'cost'=>10000000, 'level'=>2, 'capacity'=>25000, 'defense'=>1000000, 'minimalUnit'=>0],
            ['id'=>3, 'name'=>'Barrack', 'cost'=>0, 'level'=>1, 'capacity'=>0, 'defense'=>0, 'minimalUnit'=>0],
            ['id'=>4, 'name'=>'Camp', 'cost'=>1000000, 'level'=>1, 'capacity'=>5000, 'defense'=>50000, 'minimalUnit'=>1000],
            ['id'=>5, 'name'=>'Camp', 'cost'=>2000000, 'level'=>2, 'capacity'=>10000, 'defense'=>100000, 'minimalUnit'=>0],
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


        $units = array(
            ['id'=>1, 'name'=>'Archers', 'level'=>1, 'attack'=>30, 'defense'=>20, 'damage'=>0, 'speed'=>6, 'cost'=>50],
            ['id'=>2, 'name'=>'Spearman', 'level'=>1, 'attack'=>50, 'defense'=>40, 'damage'=>10, 'speed'=>5, 'cost'=>100],
            ['id'=>3, 'name'=>'Axemen', 'level'=>1, 'attack'=>50, 'defense'=>50, 'damage'=>100, 'speed'=>4, 'cost'=>200],            
            ['id'=>4, 'name'=>'Light Cavalry', 'level'=>1, 'attack'=>100, 'defense'=>100, 'damage'=>50, 'speed'=>10, 'cost'=>2000],                                    
        );

        foreach ($units as $ununit) {
            $unit = new UnitType();
            $unit->setName($ununit['name']);
            $unit->setLevel($ununit['level']);
            $unit->setAttack($ununit['attack']);
            $unit->setDefense($ununit['defense']);
            $unit->setDamage($ununit['damage']);
            $unit->setSpeed($ununit['speed']);
            $unit->setCost($ununit['cost']);
           
            $manager->persist($unit);   
        }



        $manager->flush();
    }
}

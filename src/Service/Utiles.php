<?php
namespace App\Service;

class Utiles
{

    public function sumaraFecha($fecha_inicial, $dias, $horas, $minutos, $segundos)
    {

        $fecha_en_time_stamp = $fecha_inicial->getTimestamp();

        //unixtime stamp esta en segundos
        //para adicionar un dia adicionar el total de segundo contenidos en 24 horas
        $dias_a_adicionar = $dias * 24 * 60 * 60;
        $horas_a_adicionar = $horas * 60 * 60;
        $minuto_a_adicionar = $minutos * 60;
        $segundos_a_adicionar = $segundos;

        $fecha_en_time_stamp = $fecha_en_time_stamp + $dias_a_adicionar + $horas_a_adicionar + $minuto_a_adicionar+$segundos_a_adicionar;



        $fecha_final = new \DateTime();
        $fecha_final->setTimestamp($fecha_en_time_stamp);

        //$fecha_final->modify('+'.$dias.' days');

        return $fecha_final;
    }
}

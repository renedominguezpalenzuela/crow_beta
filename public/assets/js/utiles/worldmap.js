
//------------------------------------------------------------------------------------------------------
// Variables globales a enviar al realizar el ataque
//------------------------------------------------------------------------------------------------------
let id_edificio_atacado=null;   //seteado en:  botonIniciarAtaque();
let lista_tropas_atacantes=[];            //inicializado en:  botonIniciarAtaque(); Seteado en: crearFuncionalidadBotonAddTroopsToAttack();


$(".cerrar_resultados_ataque").click(
    function (event) {
        event.preventDefault();
   // console.log("Cerrar");
    //$('#myModal').modal('hide');
    $('#ventana_resultado_ataque').removeClass('show');
    location.reload();
    }


);

//------------------------------------------------------------------------------------------------------
// funcion principal, dibuja todos los datos de la pagina
//------------------------------------------------------------------------------------------------------
function pintarEdificios(ruta_lista_edificios, ruta_accion, id_lista_edificios, id_lista_ventana_ocultas, imagen_accion, imagen_add, imagen_del) {

    let edificios = [];
    let troopLocation = [];
    let castle_troops = [];
    let myid = '';
    let id = '';

    //lista_edificios_enemigos
    let lista_edificios_html = $("#" + id_lista_edificios);

    //ventanas_ocultas_html
    let ventanas_ocultas_html = $("#" + id_lista_ventana_ocultas);

    $.getJSON(ruta_lista_edificios, function (data) {

        edificios = data.datos.buildings;
        castle_troops = data.datos.castle_troops;

        lista_edificios_html.text('');

        for (unedificio of edificios) {

            id = unedificio.building_name + unedificio.building_id.toString();

          

            myid = ID();
            //  console.log(unedificio);
            // color_class = "card-header-danger";

            //console.log(unedificio.color_class);
            lista_edificios_html.append(
                unedificioHTML(
                    unedificio.building_id,
                    unedificio.building_name2,
                    unedificio.kingdom_name,
                    unedificio.level,
                    unedificio.defense_remaining,
                    myid,
                    unedificio.color_class)
            );


            //Mostrar Lista de tropas defendiendo el edificio
            lista_tropas_html = $("#tropa" + myid);
            let tropas = unedificio.troops_location;
            for (una_tropa of tropas) {
                if (lista_tropas_html != null) {
                    lista_tropas_html.append(unaTropaHtml(una_tropa.troop_name, una_tropa.total));
                }
            }


            //Creando Ventana oculta atacar:
            let idFormulario = 'formulario' + ID();
            let id_lista_tropas_seleccionadas = 'tropa_seleccionada' + ID();

            let cadena_html = ventanaOcultaSeleccionarTropasHTML(myid,
                unedificio.building_name2,
                unedificio.kingdom_name,
                idFormulario,
                id_lista_tropas_seleccionadas);

         
                ventanas_ocultas_html.append(cadena_html);



                crearFormularioEscogerTropasAtaque(idFormulario, id_lista_tropas_seleccionadas, castle_troops, imagen_add, imagen_del);


        } //fin del ciclo





        botonAtacar(ruta_attack);
        botonIniciarAtaque();


    });


}

//------------------------------------------------------------------------------------------------------
// asigno a variable global, el id del edificio atacado
// para poder enviarlo al servidor
//------------------------------------------------------------------------------------------------------
function botonIniciarAtaque() {
    $(".boton_iniciar_ataque").click(
        function (event) {
            event.preventDefault();


            var id_edificio = $(this).attr('edificio_atacar');       
            id_edificio_atacado = id_edificio;    

            //inicializando la lista de tropas
            lista_tropas_atacantes=[];
        }
    );

}

//------------------------------------------------------------------------------------------------------
//  Dibuja una tropa en ventana de escoger tropas
//------------------------------------------------------------------------------------------------------
function unaTropaHtml(troop_name, total) {
    return '<tr><td><p> <span class="ml-1">' + troop_name + ': ' + total + '</span></p></td></tr>'
}

//------------------------------------------------------------------------------------------------------
// Dibuja un edificio en ventana inicial
//------------------------------------------------------------------------------------------------------
function unedificioHTML(  id_edficio_atacar, building_name2, kingdom_name, level, defense_remaining, myid, color_class) {


    if (color_class == "card-header-white") {
        color_class = color_class + " text-dark";
    }

    return '<div class="card">' +
                '<div class="' + color_class + '">' +
                    '<h5>' + building_name2 + ', ' + kingdom_name + '</h5>' +
                '</div>' +
                '<div class="card-body">' +
                        // '<p><span class="subrayado">Kingdom</span></p>' +
                        // '<p>' + kingdom_name + '</p>' +
                    '<p><span class="subrayado">Stats</span></p>' +
                        '<p>level: ' + level + '</p>' +
                        //'<p>Capacity: '+unedificio.filled+'/'+unedificio.capacity+'</p>'+
                        '<p>defense: ' + defense_remaining + '</p>' +
                        //Listado de tropas
                        '<p><span class="subrayado">Troops</span></p>' +
                        '<table id="tropa' + myid + '">' +
                        '</table>' +

                        //Boton de atacar
                        '<div class="row ml-2">' +
                        // '<a href="#" id="' + id + '" class="boton_attack col-sm-4">' +
                        // '<img src="' + imagen_attack + '" height="30" alt=""> </a>' +              
                        '<button type="button" class="btn btn-primary boton_iniciar_ataque" data-toggle="modal" data-target="#ventana' + myid + '" edificio_atacar="'+unedificio.building_id+'">' +
                        'Attack' +
                        '</button>' +

                    '</div>' +
                '</div>' +
            '</div>';
}

//------------------------------------------------------------------------------------------------------
// Dibuja ventanas ocultas para seleccionar tropas para el ataque
//------------------------------------------------------------------------------------------------------
function ventanaOcultaSeleccionarTropasHTML(myid, building_name2, kingdom_name, idFormulario, id_lista_tropas_seleccionadas) {
    let cadena_html = 
        '<div class="modal fade ventana_crear_ataque" id="ventana' + myid + '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">' +
           '<div class="modal-dialog modal-dialog-centered" role="document">' +
                '<div class="modal-content card">' +
                 
                    '<div class="modal-header card-header-primary ">' +
                        '<h5 class="modal-title" id="LongTitle' + myid + '">Attack: ' + unedificio.building_name2 + ', ' + unedificio.kingdom_name + '</h5>' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                    '</div>' +

                
                    '<div class="modal-body">' +
                        '<div id="' + idFormulario + '" class="form_planning_troops"></div>' +
                            '<p><span class="subrayado">Attacking Squad</span></p>' +
                            '<form id="' + id_lista_tropas_seleccionadas + '" class="form execution_troops_attack"></form>' +
                        '</div>' +
                   

                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                            '<button type="button" class="btn btn-primary boton_attack" id=attack_btn"' + myid + '">Attack</button>' +        
                        '</div>' +
                    '</div>' +

                 
               '</div>' +
            '</div>' +
        '</div>';


    return cadena_html;
}



//------------------------------------------------------------------------------------------------------
//  Crea en la ventana oculta, la lista de tropas del castillo 
//------------------------------------------------------------------------------------------------------
function crearFormularioEscogerTropasAtaque(idFormulario, id_lista_tropas_seleccionadas, troops, imagen_add, image_del) {

    // lista_tropas_html = $(".form_planning_troops");
    lista_tropas_html = $("#" + idFormulario);
    // imagen_add = '/images/iconos/add.png';

    // lista_tropas_html.append
    for (unatropa of troops) {

        //let id = unedificio.building_name+unedificio.building_id.toString();



        let troop_id = unatropa.troop_id;
        let boton_id = 'boton_add' + ID();
        let total_id = 'total_tropas' + ID();


        //unatropa.troop_id
        //unatropa.troop_name
        //
        lista_tropas_html.append(
            '<form class="form">' +
                '<div class="form-row">' +
                    '<label class="col-form-label col-sm-2" id="tropa_name_' + troop_id + '" for="troop_total' + troop_id + '">' + unatropa.troop_name + '</label>' +
                    '<span class="bmd-form-group-sm">' +

                    '<input type="number" value="' + unatropa.total + '" min="1" max="' + unatropa.total + '"  step="1" pattern="\d+" id="' + total_id + '" class="form-control mb-sm-2" />' +
                    '</span>' +

                    '<a href="#" id="' + boton_id + '" tropa_id="' + troop_id + '" class="boton_add_troops_to_attack ml-2">' +
                    '<img src="' + imagen_add + '" height="30" alt=""> </a>' +
                '</div>' +
            '</form>'
        );


        crearFuncionalidadBotonAddTroopsToAttack(boton_id, id_lista_tropas_seleccionadas, total_id, imagen_del);



    }



    if (troops.length == 0) {
        lista_tropas_html.append("<h4>Move troops to your castle to start an attack</h4>")
        //TODO: Deshabilitar boton atacar
        //TODO: cambiar color del texto
        //TODO: Validar que el total no exeda el maximo
        //TODO: validar que no se agreguen mas tropas de las existentes en el castillo
    }


}


//------------------------------------------------------------------------------------------------------
// Boton mas de cada tropa, agregar tropa al escudron de ataque
//  setear variable global lista_tropas[] para enviar al servidor
//------------------------------------------------------------------------------------------------------
function crearFuncionalidadBotonAddTroopsToAttack(boton_id, id_lista_tropas_seleccionadas, total_id, imagen_del) {
    //$(".boton_add_troops_to_attack").click(
    $("#" + boton_id).click(
        function (event) {


            event.preventDefault();

           //Obteniendo el id del input que contiene el total de tropas
            var id_tropas = $(this).attr('tropa_id');
            let tropa_label_id = 'tropa_name_' + id_tropas;

            //obteniendo el total introducido en el input 
            let total_tropas = $('#' + total_id).val();

            //obteniendo el nombre de la tropa
            let tropa_name = $('#' + tropa_label_id).text();




            //console.log(tropa_name + ' : ' +total_tropas);
            //Agregar a la tabla

            //{"from":17,"to":16, "troops":[{"troops_id":13, "total":10}]}
            //Cadena final es un arreglo [{},{}]
            // let cadena_api = '{ "troops":[{"troops_id":' + id_tropas + ',"total":' + total_tropas + '}]}';
            let cadena_api = '{"troops_id":' + id_tropas +
                               ',"total":' + total_tropas +
                               '}';

            //Agregar en variable global, el id de la tropa y el total 
            lista_tropas_atacantes.push(JSON.parse(cadena_api));

            let id_unico = ID();

            //console.log(cadena_api);

            //Validaciones
            let validaciones_ok = true;

            if (total_tropas <= 0) {
                validaciones_ok = false;
            }


            // if (validaciones_ok) {
            $("#" + id_lista_tropas_seleccionadas).append(

                '<div class="form-row" id="linea' + id_unico + '">' +
                '<label class="col-form-label col-sm-4">' + tropa_name + " : " + total_tropas + '</label>' +

                '<a href="#" id="' + id_unico + '" class="boton_del_execute_troops_movements">' +
                '<img src="' + imagen_del + '" height="30" alt=""> </a>' +
                '</div>'

            );
            //}



            crearFuncionalidadBotonDel();


        }

    );

}

//-------------------------------------------------------------------------------------------------------
// Eliminar tropa del escudron de ataque
//-------------------------------------------------------------------------------------------------------
function crearFuncionalidadBotonDel() {

    $(".boton_del_execute_troops_movements").click(
        function (event) {
            event.preventDefault();
            //console.log('borrando');

            var id_tropas = $(this).attr('id');
            //console.log(id_tropas);
            //eliminando
            $('#linea' + id_tropas).remove();
        }
    );

}



//-------------------------------------------------------------------------------------------------------
// Crea lista de tropas para seleccionar 
//-------------------------------------------------------------------------------------------------------
function crearFormularioSelectTropas(troops, buildings, imagen) {

    lista_tropas_html = $("#world_map_create_troops");

    lista_tropas_html.text('');

    let cadena_buildings_html = '';

    for (unabuilding of buildings) {

        if (unabuilding.building_name != 'Squad') {
            cadena_buildings_html = cadena_buildings_html +
                '<option value="' + unabuilding.building_id + '">' + unabuilding.building_name2.trim() + '</option>';
        }
    }



    lista_tropas_html.append(
        '<form>' +
        '<div class="form-row">' +
        '<label class="col-form-label" for="squadname">Name:</label>' +
        '<input class="col-sm-6 form-control mb-2 mr-sm-2" id="squadname" placeholder="Squad Name" type="text">' +
        '</div>'
    );

    for (unatropa of troops) {


        let troop_id = unatropa.troop_id;


        lista_tropas_html.append(

            '<div class="form-row">' +
            '<label class="col-form-label col-sm-1 " id="tropa_name_' + troop_id + '" for="troop_total' + troop_id + '">' + unatropa.troop_name + '</label>' +
            //'<span class="bmd-form-group-sm">' +
            '<input type="number" min="0" step="1" pattern="\d+" id="troop_total' + troop_id + '" class="col-sm-1 form-control mb-2 ml-2 " placeholder="0"/>' +
            //'</span>' +

            '<label class="col-form-label col-sm-2 ml-2" for="from_select_' + troop_id + '">From</label>' +
            //'<span class="bmd-form-group-sm is-filled">' +
            '<select class="form-control mb-2 mr-sm-2 col-sm-3" id="from_select_' + troop_id + '">' +
            cadena_buildings_html +
            '</select>' +
            '</span>' +


            '<a href="#" id="boton_add' + troop_id + '" tropa_id="' + troop_id + '" class="boton_add_troop">' +
            '<img src="' + imagen + '" height="30" alt=""> </a>' +

            '</div>'


        );
    };

    lista_tropas_html.append(
        '</form>'
    );
}





//-------------------------------------------------------------------------------------------------------
// Dibuja las tropas en los edificios enemigos
//-------------------------------------------------------------------------------------------------------
function dibujarTropasenHTML(buildings) {

    // console.log(buildings);

    for (unedificio of buildings) {

        let lista_tropas_html = null;

        //se crea en cada edificio una tabla con id = "squad1", "squad2"...
        /*if (unedificio.building_name == "Squad") {
            let id = unedificio.building_name + unedificio.building_id.toString();
            lista_tropas_html = $("#" + id);
        }*/

        let tropas = unedificio.troop_location;


        for (una_tropa of tropas) {
            if (lista_tropas_html != null) {
                lista_tropas_html.append("<tr><td><p> <span>" + una_tropa.troop_name + ": " + una_tropa.total + "</span></p></td></tr>");

            }
        }


    }



}



//-------------------------------------------------------------------------------------------------------
// Realizar el ataque
//-------------------------------------------------------------------------------------------------------

//Datos a enviar

/*
   
peticion: {"troops":[{"troops_id":1,"total":13},{"troops_id":2,"total":3}],"attacked_building":"3"}

*/
function botonAtacar(ruta_attack) {
    $(".boton_attack").click(


        function (event) {
            //console.log('attack');


            //event.preventDefault();

            var id = $(this).attr('id');
           // console.log("prueba ok" + id);

            var peticion_all = {};

            //Enviar: 
            //Lista de tropas usadas en el ataque  

           
            peticion_all.troops = lista_tropas_atacantes;

            //id Edificio atacado
            //console.log(id_edificio_atacado);
            peticion_all.attacked_building = id_edificio_atacado;




            var datos_enviar = {
               peticion: JSON.stringify(peticion_all)
             // peticion: peticion_all
            }


           // console.log(datos_enviar);

            $.post(ruta_attack,
                datos_enviar

            ).done(
                function (data) {
                    
                    //location.reload();
                    //console.log("datos recibidos");
                    //console.log(data);
                    pintarResultadosAtaqueHTML(data);

                    //Ocultar ventana preparar ataque
                    $('.ventana_crear_ataque').removeClass('show');

                    //Mostrar ventana resultados del ataque
                    $('#ventana_resultado_ataque').modal('show'); 

                    //$('[href="#squads"]').tab('show');
                    //$("#battle_result").append("<h1>HOLA</h1>");
                    //console.log("done");
                    //$('[href="#world"]').removeClass('active');
                    //$('[href="#squads"]').addClass('active');
                }

            ).fail(
                function (data, textStatus, jqXHR) {
                    console.log('error');
                    console.log(textStatus + ' : ' + jqXHR);
                    
                }

            ).always(
                function () {
                   // console.log("always");
                }

            );


        }
    );



}


function pintarResultadosAtaqueHTML(data){
    //console.log("resultados");

    //Nombre del edificio atacado
    $("#attacked_building").html(data.attacked_building);

    //Tropas defensoras
    pintarTropasDefensoras(data.troops_defenders);

    //Tropas atacantes

    pintarTropasAtacantes(data.troops_attacker);

    //Defensa inicial del castillo
    $("#castle_initial_defense").html(data.resultados_ataque.building_initial_defense);

    //Texto resultados del ataque
    
    $("#resultado_defender").html(data.resultados_ataque.texto_resultado_defender);
    $("#resultado_attacker").html(data.resultados_ataque.texto_resultado_attacker);


//Force Strength
    $("#defending_force_strength").html(data.resultados_ataque.defending_force_strength);
    $("#attacking_force_strenght").html(data.resultados_ataque.attacking_force_strenght);

    //Porcientos
    
    $("#victory").html(data.resultados_ataque.defender_chance_of_victory);
    $("#defeat").html(data.resultados_ataque.attacker_chance_of_victory);
    $("#stalemate").html(data.resultados_ataque.stale_chance);

    //Tropas bajas    
    pintarBajasHTML(data.resultados_ataque.bajas_atacante, "bajas_attacker");
    pintarBajasHTML(data.resultados_ataque.bajas_defensor, "bajas_defender");

}



function pintarBajasHTML(tropas, id_tabla){

    console.log(tropas);
    let lista_tropas_html = $("#"+id_tabla);

    lista_tropas_html.text('');

    for (una_tropa of tropas) {
        lista_tropas_html.append("<tr><td><p> <span>" + una_tropa.name + ": " + una_tropa.total+ ", " +una_tropa.user + "</span></p></td></tr>");
    }
}


function pintarTropasDefensoras(tropas){

    lista_tropas_html = $("#troops-castle");

    lista_tropas_html.text('');

    for (una_tropa of tropas) {
        lista_tropas_html.append("<tr><td><p> <span>" + una_tropa.name + ": " + una_tropa.total + "</span></p></td></tr>");
    }
}


function pintarTropasAtacantes(tropas){

    lista_tropas_html = $("#attacker_squad");

    lista_tropas_html.text('');

    for (una_tropa of tropas) {
        lista_tropas_html.append("<tr><td><p> <span>" + una_tropa.name + ": " + una_tropa.total + "</span></p></td></tr>");
    }

  
}







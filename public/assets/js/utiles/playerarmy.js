

//-------------------------------------------------------------------------
// Datos del kingdom y del user
//-------------------------------------------------------------------------
function dibujarDatosHTML(team, recursos) {

    $('#kingdom_name').html(team.kingdom_name);
    $('#kingdom_points').html(team.kingdom_points);
    $('#user_points').html(recursos.user_points);
    $('#user_gold').html(recursos.gold);
}


//-------------------------------------------------------------------------
// Datos del Castillo
//-------------------------------------------------------------------------
function setCastilloData(datos_castillo) {
    
    $('#main_castle').html("Main Castle:"+datos_castillo.castle_id)
    $('#level-castle').html(datos_castillo.level);
    $('#defense-castle').html(datos_castillo.defense_remaining);
    $('#capacity-castle').html(datos_castillo.capacity);
};


function dibujarEdificiosenHTML(buildings) {
    lista_edificios_html = $("#lista_edificios");

    //limpiar contenido 

    

    for (unedificio of buildings) {
        if (unedificio.building_name != "Barrack") {


            // if (unedificio.building_name != "Castle" && unedificio.building_name != "Barrack") {

            let id = unedificio.building_name + unedificio.building_id.toString();
            if (!unedificio.main_castle) {
              
                lista_edificios_html.append(
                    '<div class="card">' +
                    '<div class="card-header-primary">' +
                    '<h5>' + unedificio.building_name2 + '</h5>' +
                    '</div>' +
                    '<div class="card-body">' +
                    '<p><span class="subrayado">Troops</span></p>' +
                    '<table id="' + id + '">' +

                    '</table>' +
                    '</div>' +
                    '</div>'
                );
            }
        }

    }

}


//-------------------------------------------------------------------------
// formulario de mover tropas: Troops Movement
//-------------------------------------------------------------------------
function crearFormularioMoverTropas(troops, buildings, imagen_add, imagen_del) {

    lista_tropas_html_formulario = $("#form_planning_troops");

    let cadena_buildings_html = '';

    for (unabuilding of buildings) {
        cadena_buildings_html = cadena_buildings_html +
            '<option value="' + unabuilding.building_id + '">' + unabuilding.building_name2.trim() + '</option>';
    }

    // lista_tropas_html.append
    for (unatropa of troops) {
        //let id = unedificio.building_name+unedificio.building_id.toString();
        //console.log(id);
        let troop_id = unatropa.troop_id;
        //unatropa.troop_id
        //unatropa.troop_name
        lista_tropas_html_formulario.append(
            '<form class="form">' +
            '<div class="form-row">' +
            '<label class="col-form-label col-sm-2" id="tropa_name_' + troop_id + '" for="troop_total' + troop_id + '">' + unatropa.troop_name + '</label>' +
            '<span class="bmd-form-group-sm">' +
            '<input type="number" min="0" step="1" pattern="\d+" id="troop_total' + troop_id + '" class="col-sm-4 form-control mb-2 mr-sm-2" placeholder="0"/>' +
            '</span>' +

            '<label class="col-form-label col-sm-1" for="from_select_' + troop_id + '">From</label>' +
            '<span class="bmd-form-group-sm is-filled">' +
            '<select class="form-control mb-2 mr-sm-2" id="from_select_' + troop_id + '">' +
            cadena_buildings_html +
            '</select>' +
            '</span>' +

            '<label class="col-form-label col-sm-1" for="to_select_' + troop_id + '">To</label>' +
            '<span class="bmd-form-group-sm is-filled">' +
            '<select class="form-control mb-2 mr-sm-2" id="to_select_' + troop_id + '">' +
            cadena_buildings_html +
            '</select>' +
            '</span>' +

            '<a href="#" id="boton_add' + troop_id + '" tropa_id="' + troop_id + '" class="boton_plan_troops_movements">' +
            '<img src="' + imagen_add + '" height="30" alt=""> </a>' +


            '</div>' +
            '</form>'

        );

        //crearFuncionalidadBotonFormularioPlanMovements(imagen_del);

    }
}





/* Crea listado de tropas para revisa antes de enviar */
function crearFuncionalidadBotonFormularioPlanMovements(imagen_del) {


    $(".boton_plan_troops_movements").click(
        function (event) {


            event.preventDefault();

            var id_tropas = $(this).attr('tropa_id');

            //Obteniendo el id del input que contiene el total de tropas
            //  var tropa_id = 'tropa_id_' + id_tropas;

            // var tropa_name =  $('#tropa_'+tropa_id).text();
            let tropa_label_id = 'tropa_name_' + id_tropas;
            let tropa_name = $('#' + tropa_label_id).text();


            //obteniendo el total introducido en el input 
            let total_tropa_id = 'troop_total' + id_tropas;
            let total_tropas = $('#' + total_tropa_id).val();
            //console.log(total_tropa_id);

            //ID del select from   //valor seleccionado from
            var from_id = 'from_select_' + $(this).attr('tropa_id');
            var from_select = $('#' + from_id + ' option:selected').val();
            var from_name = $('#' + from_id + ' option:selected').text();

            //ID del select to     //valor seleccionado from
            var to_id = 'to_select_' + $(this).attr('tropa_id');
            var to_select = $('#' + to_id + ' option:selected').val();
            var to_name = $('#' + to_id + ' option:selected').text();



            //console.log(tropa_name + ' : ' + from_select + " : " + to_select + ' : ' + to_name);
            //Agregar a la tabla

            //{"from":17,"to":16, "troops":[{"troops_id":13, "total":10}]}
            //Cadena final es un arreglo [{},{}]
            // let cadena_api = '{"from":' + from_select + ',"to":' + to_select + ', "troops":[{"troops_id":' + id_tropas + ',"total":' + total_tropas + '}]}';
            let cadena_api = '{"troops_id":' + id_tropas +
                ',"total":' + total_tropas +
                ',"from":' + from_select +
                ',"to":' + to_select +
                '}';


            let id_unico = id_tropas + ID();

            //console.log(cadena_api);

            //Validaciones
            let validaciones_ok = true;

            if (total_tropas <= 0) {
                validaciones_ok = false;
            }



            if (validaciones_ok) {
                $("#execution_troops_movement").append(
                    '<tr id=' + id_unico + ' cadena_api=' + cadena_api + '>' +
                    '<td><p>' +
                    tropa_name + " : " + total_tropas + " From: " + from_name + " To: " + to_name +

                    '</p></td>' +
                    '<td><p>' +
                    '<a href="#" id="boton_del' + id_unico + '" class="boton_del_execute_troops_movements">' +
                    '<img src="' + imagen_del + '" height="30" alt=""> </a>' +

                    '<p></td>' +
                    '</tr>'
                );
            }




            let id_boton = 'boton_del' + id_unico;
            //console.log(id_boton);

            crearFuncionalidadBotonDelPlanMovements(id_boton, id_unico);


        }

    );

}



function crearFuncionalidadBotonDelPlanMovements(id_boton, id_unico) {

    $("#" + id_boton).click(
        function (event) {
            event.preventDefault();


            //var id_tropas = $(this).attr('id');
            // console.log(id_boton);
            //eliminando
            $('#' + id_unico).remove();
        }
    );

}




function dibujarTropasenHTML(buildings) {

//console.log(buildings);
    for (unedificio of buildings) {

        let lista_tropas_html = null;
        //si es el castillo principal
        //console.log(unedificio.building_id+' '+unedificio.main_castle);
        if (unedificio.main_castle) {
            lista_tropas_html = $("#castle_troops");  
            //lista_tropas_html.html("AAA" + unedificio.building_id.toString()) ;          
        } else 
 
        //si es la barraca
        if (unedificio.building_name == "Barrack") {
            lista_tropas_html = $("#barrack_troops");
        } else 

        //if (unedificio.building_name != "Castle" && unedificio.building_name != "Barrack") {
        if (unedificio.building_name != "Barrack") {
            let id = unedificio.building_name + unedificio.building_id.toString();
            lista_tropas_html = $("#" + id);
        }


        let tropas = unedificio.troop_location;
        


        for (una_tropa of tropas) {

            
            if (lista_tropas_html != null) {

                lista_tropas_html.append("<tr><td><p> <span>" + 
                                          una_tropa.troop_name + ": " +
                                          una_tropa.total +
                                          "</span></p></td></tr>");
             
            }

            
        }


    }



}


//boton_go, realizar el movimiento
//datos a enviar
//[{"troops_id":1,"total":1,"from":1,"to":1}]
function crearBotonGo(ruta_move_troops) {
    $("#boton_go").click(

        function (event) {
            event.preventDefault();
            //Recorrer la tabla de datos
            //id="execution_troops_movement"
            //formar el json
            let cadena = '';
            var datos = [];
            $("#execution_troops_movement tr").each(function () {
                cadena = $(this).attr('cadena_api')

                datos.push(cadena);

            });

            console.log(datos);




            //enviarlo al servidor

            var datos_enviar = {
                peticion: '[' + datos.toString() + ']'
            }


            $.post(ruta_move_troops,
                datos_enviar

                ,
                function (data, textStatus, jqXHR) {


                }
            ).done(
                function () {
                    //console.log("done");
                    location.reload();
                }

            ).fail(
                function (data, textStatus, jqXHR) {
                    console.log(textStatus + ' : ' + jqXHR);
                }

            ).always(
                function () {
                    //console.log("always");
                }

            );

            //escribir mensaje de confirmacion al usuario
            // console.log("sss"+ruta_move_troops);


        }
    );
}




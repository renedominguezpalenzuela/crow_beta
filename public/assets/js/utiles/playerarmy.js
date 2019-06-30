
function setCastilloData(datos_castillo) {
    $('#level-castle').html(datos_castillo.level);
    $('#defense-castle').html(datos_castillo.defense_remaining);
    $('#capacity-castle').html(datos_castillo.capacity);
};


//boton_go
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
              //datos = datos +cadena;
            });

           // console.log(datos);
        


         
            //enviarlo al servidor

            var datos_enviar ={
                peticion : '['+datos.toString()+']'
            }
          

            $.post(ruta_move_troops,
                datos_enviar

            ,
                function (data, textStatus, jqXHR) {
                    //console.log(data);
                    console.log('ok');
                    location.reload();

                }
            ).done(
                function(){
                    console.log("done");
                }

            ).fail(
                function(data, textStatus,jqXHR ){
                    console.log(textStatus + ' : '+jqXHR);
                }

            ).always(
                function(){
                    console.log("always");
                }

            );

              //escribir mensaje de confirmacion al usuario
            // console.log("sss"+ruta_move_troops);


        }
    );
}




//Preparar Arreglo de Tropas copiarlo en edificios (hacerlo en server?)
function setTroopOnBuildings(buildings, troopLocation) {

    // Buscando troop location
    for (let y = 0; y < buildings.length; y++) { // puedo pintar aqui el card del edificio

        let unBuilding = buildings[y];
        let building_id = unBuilding['building_id'];
        unBuilding['troop_location'] = [];


        // recorrer todos los troops location buscando si esta el building

        for (let i = 0; i < troopLocation.length; i++) {
            let untroop_location = troopLocation[i];
            // console.log("Building_id "+building_id);
            // console.log("trooplocation building_id "+untroop_location['building_id']);

            if (untroop_location['building_id'] == building_id) {
                unBuilding['troop_location'].push(untroop_location);
                //   console.log('match');
                //console.log(troopLocation)
                // Puedo pintar aqui un troop del edificio [untroop_location]
                //si lo pinto aqui no hago el push anterior aunque seria bueno mantener la extructura
                //de forma global en la pagina para realizar validaciones en los movimientos de tropas
                //y no hacer peticiones de mas al servidor

                //----------------------------------------
                //ES MAS EFICIENTE AQUI CREO
                //----------------------------------------
                /* $('<table class="table table-hover"><tr><td><p>'+untroop_location['troop_name']+': '+
                 untroop_location['total']+'</p></td></tr></table>').appendTo('#troop_location'+untroop_location['building_id']);*/
            }
        }

        buildings[y] = unBuilding;
        // console.log(unBuilding.troop_location);
        /*  
        for (let k = 0; k < unBuilding.troop_location.length; k++){
              // console.log(unBuilding.troop_location[k]['troop_name']);
              $('<table class="table table-hover"><tr><td><p>'+unBuilding.troop_location[k]['troop_name']+': '+unBuilding.troop_location[k]['total']+'</p></td></tr></table>').appendTo('#troop_location'+unBuilding.troop_location[k]['building_id']);
          }
          */
    }

    //O Puedo al final de preparar la extructura de datos empezar a pintar los cards
    //revisa en la consola para que veas
    // console.log(data.datos);
};


function crearFormularioMoverTropas(troops, buildings, imagen) {

    lista_tropas_html = $("#form_planning_troops");

    let cadena_buildings_html = '';

    for (unabuilding of buildings) {
        cadena_buildings_html = cadena_buildings_html +
            '<option value="' + unabuilding.building_id + '">' + unabuilding.building_name2.trim() + '</option>';
    }



    lista_tropas_html.append
    for (unatropa of troops) {

        //let id = unedificio.building_name+unedificio.building_id.toString();

        //console.log(id);

        let troop_id = unatropa.troop_id;


        //unatropa.troop_id
        //unatropa.troop_name
        //
        lista_tropas_html.append(
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
            '<img src="' + imagen + '" height="30" alt=""> </a>' +



            '</div>' +
            '</form>'

        );

        //crearFuncionalidadBotonFormularioPlanMovements();




    }
}







function crearFuncionalidadBotonFormularioPlanMovements() {





    $(".boton_plan_troops_movements").click(

        function (event, imagen_del) {

            imagen_del = '/images/iconos/delete.png';
            //console.log("sss" + imagen_del);

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
                    '<a href="#" id="' + id_unico + '" class="boton_del_execute_troops_movements">' +
                    '<img src="' + imagen_del + '" height="30" alt=""> </a>' +

                    '<p></td>' +
                    '</tr>'
                );
            }





            crearFuncionalidadBotonDelPlanMovements();


        }

    );

}



function crearFuncionalidadBotonDelPlanMovements() {

    $(".boton_del_execute_troops_movements").click(
        function (event) {
            event.preventDefault();

            var id_tropas = $(this).attr('id');
            //console.log(id_tropas);
            //eliminando
            $('#' + id_tropas).remove();
        }
    );

}



function dibujarEdificiosenHTML(buildings) {
    lista_edificios_html = $("#lista_edificios");

    //limpiar contenido 

    //console.log(buildings);

    for (unedificio of buildings) {



        if (unedificio.building_name != "Castle" && unedificio.building_name != "Barrack") {

            let id = unedificio.building_name + unedificio.building_id.toString();

            //console.log(id);
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

function dibujarTropasenHTML(buildings) {


    for (unedificio of buildings) {

        let lista_tropas_html = null;
        //si es el castillo
        if (unedificio.building_name == "Castle") {
            lista_tropas_html = $("#castle_troops");

        }

        //si es la barraca
        if (unedificio.building_name == "Barrack") {
            lista_tropas_html = $("#barrack_troops");
        }

        if (unedificio.building_name != "Castle" && unedificio.building_name != "Barrack") {

            let id = unedificio.building_name + unedificio.building_id.toString();
            lista_tropas_html = $("#" + id);
        }


        let tropas = unedificio.troop_location;
        for (una_tropa of tropas) {
            if (lista_tropas_html != null) {
                lista_tropas_html.append("<tr><td><p> <span>" + una_tropa.troop_name + ": " + una_tropa.total + "</span></p></td></tr>");

            }
        }


    }



}


// Generate unique IDs for use as pseudo-private/protected names.
// Similar in concept to
// <http://wiki.ecmascript.org/doku.php?id=strawman:names>.
//
// The goals of this function are twofold:
// 
// * Provide a way to generate a string guaranteed to be unique when compared
//   to other strings generated by this function.
// * Make the string complex enough that it is highly unlikely to be
//   accidentally duplicated by hand (this is key if you're using `ID`
//   as a private/protected name on an object).
//
// Use:
//
//     var privateName = ID();
//     var o = { 'public': 'foo' };
//     o[privateName] = 'bar';
var ID = function () {
    // Math.random should be unique because of its seeding algorithm.
    // Convert it to base 36 (numbers + letters), and grab the first 9 characters
    // after the decimal.
    return '_' + Math.random().toString(36).substr(2, 9);
};



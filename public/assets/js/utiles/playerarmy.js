
function setCastilloData(datos_castillo) {
    $('#level-castle').html(datos_castillo.level);
    $('#defense-castle').html(datos_castillo.defense_remaining);
    $('#capacity-castle').html(datos_castillo.capacity);
};




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
            '<label class="col-form-label col-sm-2" for="troop_total' + troop_id + '">' + unatropa.troop_name + '</label>' +
            '<span class="bmd-form-group-sm">' +
            '<input type="text" id="troop_total' + troop_id + '" class="col-sm-2 form-control mb-2 mr-sm-2" placeholder="0"/>' +
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


            // <a href="#" id="boton_add04" tropa_id="04" class="boton_plan_troops_movements">
            // <img src="{{ asset('images/iconos/add.png') }}" height="40" alt=""> </a>

            '<a href="#" id="boton_add' + troop_id + '" tropa_id="' + troop_id + '" class="boton_plan_troops_movements">' +
            '<img src="' + imagen + '" height="40" alt=""> </a>' +



            '</div>' +
            '</form>'

        );

        //crearFuncionalidadBotonFormularioPlanMovements();



      
    }
}


function crearFuncionalidadBotonFormularioPlanMovements(){
    $(".boton_plan_troops_movements").click(

        function (event) {

            event.preventDefault();

            //Obteniendo el id del input que contiene el total de tropas
            var tropa_id = 'tropa_id_' + $(this).attr('tropa_id');
            //obteniendo el total introducido en el input 
            var total_Tropas = $('#' + tropa_id).val();

            //ID del select from
            var from_id = 'from_select_' + $(this).attr('tropa_id');
            //valor seleccionado from
            var from_select = $('#' + from_id + ' option:selected').val();

            //ID del select to
            var to_id = 'to_select_' + $(this).attr('tropa_id');
            //valor seleccionado from
            var to_select = $('#' + to_id + ' option:selected').val();

            //var from_select =  $('#from_select_04 option:selected').val();

            console.log(from_select + " : " + to_select);


            //console.log(tropa_id +": "+ total_Tropas + " : "+from_id+" : "+from_select);

        }

    )

}


function dibujarEdificiosenHTML(buildings) {
    lista_edificios_html = $("#lista_edificios");

    //limpiar contenido 

    console.log(buildings);

    for (unedificio of buildings) {



        if (unedificio.building_name != "Castle" && unedificio.building_name != "Barrack") {

            let id = unedificio.building_name + unedificio.building_id.toString();

            console.log(id);
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



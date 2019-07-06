
/*
$("#boton_create_squad").click(

    function (event) {
        event.preventDefault();
        console.log("Hi");
    }
);*/

/*
$(document).ready(function(){

    console.log("inicializando");
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        $('.nav-tabs a').removeClass('active');
    }
});*/


//Crea lista de tropas para seleccionar 
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
        '<form  class="form">' +
        '<div class="form-row">' +
        '<label class="col-form-label col-sm-2" for="squadname">Name:</label>' +
        '<input class="col-sm-6 form-control mb-2 mr-sm-2" id="squadname" placeholder="Squad Name" type="text">' +
        '</div>'
    );

    for (unatropa of troops) {


        let troop_id = unatropa.troop_id;


        lista_tropas_html.append(

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


            '<a href="#" id="boton_add' + troop_id + '" tropa_id="' + troop_id + '" class="boton_add_troop">' +
            '<img src="' + imagen + '" height="30" alt=""> </a>' +

            '</div>'


        );
    };

    lista_tropas_html.append(
        '</form>'
    );
}



//Boton signo mas de cada tropa, adiciona tropa a la selecion
function crearFuncionalidadBotonAddTroop() {


    $(".boton_add_troop").click(

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

            //console.log(tropa_name + ' : ' + from_select + " : " + to_select + ' : ' + to_name);
            //Agregar a la tabla

            //{"from":17,"to":16, "troops":[{"troops_id":13, "total":10}]}
            //Cadena final es un arreglo [{},{}]
            // let cadena_api = '{"from":' + from_select + ',"to":' + to_select + ', "troops":[{"troops_id":' + id_tropas + ',"total":' + total_tropas + '}]}';
            let cadena_api = '{"troops_id":' + id_tropas +
                ',"total":' + total_tropas +
                ',"from":' + from_select +
                '}';


            let id_unico = id_tropas + ID();

            //console.log(cadena_api);

            //Validaciones
            let validaciones_ok = true;

            if (total_tropas <= 0) {
                validaciones_ok = false;
            }


            if (validaciones_ok) {
                $("#world_map_tropas_seleccionadas").append(
                    '<tr id=' + id_unico + ' cadena_api=' + cadena_api + '>' +
                    '<td><p>' +
                    tropa_name + " : " + total_tropas + " From: " + from_name +

                    '</p></td>' +
                    '<td><p>' +
                    '<a href="#" id="' + id_unico + '" class="boton_del_selected_troop">' +
                    '<img src="' + imagen_del + '" height="30" alt=""> </a>' +

                    '<p></td>' +
                    '</tr>'
                );
            }
            BotonDelTroop();

        }

    );



}

//elimina tropa de la seleccion
function BotonDelTroop() {

    $(".boton_del_selected_troop").click(
        function (event) {


            event.preventDefault();

            var id_tropas = $(this).attr('id');
            //console.log(id_tropas);
            //eliminando
            $('#' + id_tropas).remove();
        }
    );

}


//boton_go, realizar el movimiento
function botonCrearSquad(ruta_create_squad) {
    $("#boton_create_squad").click(
        function (event) {
            event.preventDefault();

            //Objeto JavaScript que contendra todos los datos a enviar
            var peticion_all = {};



            //Obtengo nombre del Squad a Crear
            //TODO: validar que no exista ya
            nombre_new_squad = $("#squadname").val();
            peticion_all.name = nombre_new_squad;

            if (nombre_new_squad=='') {
                //Error nombre del squad no puede estar vacio
                return;
            }

            //Recorrer la tabla de datos
            //id="execution_troops_movement"
            //formar el json
            //Datos de tropas a mover 
            var datos_tropas = [];

            let cadena = '';
            $("#world_map_tropas_seleccionadas tr").each(function () {
                cadena = $(this).attr('cadena_api')
                // console.log(cadena);
                //convierto de cadena a objeto JS
                datos_tropas.push(JSON.parse(cadena));
                //datos = datos +cadena;
            });

            peticion_all.datos_tropas = datos_tropas;

            //console.log(JSON.stringify(peticion_all));



            //enviarlo al servidor



            var datos_enviar = {
                peticion: JSON.stringify(peticion_all)
            }



            $.post(ruta_create_squad,
                datos_enviar

                ,
                function (data, textStatus, jqXHR) {
                    console.log('ok');
                    console.log(data);
                    

                   
                    //pintarSquads(squads);

                }
            ).done(
                function () {
                   
                  
                    $('[href="#squads"]').tab('show');

                    
                    location.reload();
                    console.log("done");
                    $('[href="#world"]').removeClass('active');
                    $('[href="#squads"]').addClass('active');
                }

            ).fail(
                function (data, textStatus, jqXHR) {
                    console.log(textStatus + ' : ' + jqXHR);
                }

            ).always(
                function () {
                    console.log("always");
                }

            );

            //escribir mensaje de confirmacion al usuario
            // console.log("sss"+ruta_move_troops);


        }
    );
}


function Test() {
    var my_objeto = {};

    var name = [];
    var age = [];
    name.push('nombre 1');
    name.push('nombre 2');

    age.push('24');
    age.push('30');

    var vtitulo = 'Este es el titulo';
    my_objeto.titulo = vtitulo;

    var unjson_en_cadena = '{"troops_id":1,"total":2,"from":1}';
    my_objeto.json_cadena = JSON.parse(unjson_en_cadena);

    var arreglo_de_cadenas = '[{"troops_id":1,"total":2,"from":1},{"troops_id":1,"total":2,"from":1}]';
    my_objeto.arreglo_json_cadena = JSON.parse(arreglo_de_cadenas);




    my_objeto.name = name;
    my_objeto.age = age;


    //Referencia a arreglos
    my_objeto.name[1] = "FFF";







    console.log(JSON.stringify(my_objeto));

}

function pintarEdificiosEnemigos(ruta_edificios_enemigos) {

    var edificios = [];

    $.getJSON(ruta_edificios_enemigos, function (data) {

        edificios = data.datos.buildings;

        var lista_edificios_enemigos_html = $("#lista_edificios_enemigos");
        lista_edificios_enemigos_html.text('');

        for (unedificio of edificios) {

            let id = unedificio.building_name + unedificio.building_id.toString();

            //console.log(id);
            lista_edificios_enemigos_html.append(
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

    });







    /*
    <div class="card">
                            <div class="card-header-primary ">
                                {# <div class="card-icon">
                                                                        <i class="material-icons"></i>
                                                                    </div>
                                                                   
                                                                    <p class="card-category">Revenue</p>
                                                                    <h3 class="card-title">$34,245</h3>#}
                                <h5>Castle</h5>
                            </div>
 
                            <img alt="Card image cap" class="card-img-top" src="...">
                            <div class="card-body">
                                <h5 class="card-title">Card title</h5>
                                <p class="card-text">This is a longer card with supporting text below as a natural
                                    lead-in to additional content. This content is a little bit longer.</p>
                                <p class="card-text">
                                    <small class="text-muted">Last updated 3 mins ago</small>
                                </p>
                            </div>
                        </div>
    */



}


function pintarSquads(squads, image_del) {

    console.log('Pintar squad');
    
    var lista_squads_html = $("#lista_squads");
    lista_squads_html.text('');

    for (unsquad of squads) {

        let id = unsquad.building_name + unsquad.building_id.toString();

        //console.log(id);
        lista_squads_html.append(
            '<div class="card" id="squad"'+id+'>' +
            '<div class="card-header-primary">' +
            '<h4 class="card-title">'+
             unsquad.building_name2 +            
            '<div class="float-right">'+
            '<a href="#" id="' + id + '" class="boton_del_squad">' +
            '<img src="' + imagen_del + '" height="30" alt=""> </a>' +
            '</div>'+
            '</h4>'+
            '</div>'+  
            '<div class="card-body">' +
            '<p><span class="subrayado">Troops</span></p>' +
            '<table id="' + id + '">' +
            '</table>' +
            '</div>' +
            '</div>'
        );

    }
}



//elimina tropa de la seleccion
function BotonDelSquad() {

    $(".boton_del_squad").click(
        function (event) {


            event.preventDefault();

            var id_squad = $(this).attr('id');
            //console.log(id_tropas);
            //eliminando
            $('#squad' + id_squad).remove();
        }
    );

}


function inicializarVistaSquads(ruta_listar_resources, ruta_create_squad){

    $.getJSON(ruta_listar_resources, function (data) { // obteniendo los datos recibidos de la peticion
        datos = data.datos;
        buildings = datos.buildings;
        troops = datos.troops;

        squads = datos.squads;

        //Mostrar listado de Squads propios


        //Mostrar listado de edificios solo los enemigos

        //Formulario seleccionar tropas
        crearFormularioSelectTropas(troops, buildings, imagen_add);
        crearFuncionalidadBotonAddTroop();

        //Funcionalidad del boton GO
      
        botonCrearSquad(ruta_create_squad, squads);

        pintarSquads(squads, imagen_del);

        BotonDelSquad();

        console.log(squads);

       





    });



}
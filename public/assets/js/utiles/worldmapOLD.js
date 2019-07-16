
//Dibuja la lista de edificios enemigos
function pintarEdificiosEnemigosOLD(ruta_edificios_enemigos, ruta_attack, imagen_attack, imagen_add, imagen_del) {

    let edificios = [];
    let troopLocation = [];

    let castle_troops = [];




    $.getJSON(ruta_edificios_enemigos, function (data) {

        edificios = data.datos.buildings;
        castle_troops = data.datos.castle_troops;

        // console.log(castle_troops);


        var lista_edificios_enemigos_html = $("#lista_edificios_enemigos");
        var ventanas_atacar_ocultas_html = $("#ventanas_atacar_ocultas");

        lista_edificios_enemigos_html.text('');

        for (unedificio of edificios) {

            let id = unedificio.building_name + unedificio.building_id.toString();


            myid = ID();
            //  console.log(unedificio);
            lista_edificios_enemigos_html.append(
                '<div class="card">' +
                '<div class="card-header-primary">' +
                '<h5>' + unedificio.building_name2 + '</h5>' +
                '</div>' +
                '<div class="card-body">' +
                '<p><span class="subrayado">Kingdom</span></p>' +
                '<p>' + unedificio.kingdom_name + '</p>' +
                '<p><span class="subrayado">Stats</span></p>' +
                '<p>level: ' + unedificio.level + '</p>' +
                //'<p>Capacity: '+unedificio.filled+'/'+unedificio.capacity+'</p>'+
                '<p>defense: ' + unedificio.defense_remaining + '</p>' +



                '<p><span class="subrayado">Troops</span></p>' +
                '<table id="tropa' + myid + '">' +
                '</table>' +

                '<div class="row ml-2">' +
                // '<a href="#" id="' + id + '" class="boton_attack col-sm-4">' +
                // '<img src="' + imagen_attack + '" height="30" alt=""> </a>' +              
                '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ventana' + myid + '">' +
                'Attack' +
                '</button>' +




                '</div>' +
                '</div>' +
                '</div>'
            );


            //Lista de tropas defendiendo el edificio
            lista_tropas_html = $("#tropa" + myid);

            let tropas = unedificio.troops_location;
            for (una_tropa of tropas) {

                if (lista_tropas_html != null) {
                    lista_tropas_html.append('<tr><td><p> <span class="ml-1">' + una_tropa.troop_name + ': ' + una_tropa.total + '</span></p></td></tr>');

                }
            }


            //Creando Ventana oculta atacar:

            let idFormulario = 'formulario' + ID();
            let id_lista_tropas_seleccionadas = 'tropa_seleccionada' + ID();

            ventanas_atacar_ocultas_html.append(
                '<div class="modal fade" id="ventana' + myid + '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">' +
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
                '</div>'
            );







            crearFormularioEscogerTropasAtaque(idFormulario, id_lista_tropas_seleccionadas, castle_troops, imagen_add, imagen_del);






        } //fin del ciclo





        botonAtacar(ruta_attack);


    });

}

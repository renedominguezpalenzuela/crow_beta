
function CrearOnClickUsuarios(ruta) {
    $(".user_list").click(
        function (event) {

           // console.log("sss");
            event.preventDefault();

            var id_usuario = $(this).attr('id');

            var nombre_usuario = $(this).attr('name');
            console.log("Nombre " + nombre_usuario);
            //Buscar los datos del usuario
            let nombre_usuario_html = $("#user_name");
            nombre_usuario_html.html(nombre_usuario);

            getListaTropasFromAPI(id_usuario, ruta)

        }
    );
}

//------------------------------------------------------------------------------------------------------
//  Dibuja datos de una usuario
//------------------------------------------------------------------------------------------------------
function unUsuarioHTML(usuario) {
    let cadena = '<tr>' +
        '<td>' +
        usuario.user_id +
        '</td>' +
        '<td>' +
        '<a href="#" id="' + usuario.user_id + '" class="user_list" name="'+usuario.username+'">' +
        usuario.username +
        '</a>' +
        '</td>' +
        '<td>' +
        usuario.gold +
        '</td>' +
        '<td>' +
        usuario.points +
        '</td>' +
        '<td>' +
        usuario.kingdom +
        '</td>' +
        '</tr>';

    return cadena;
}



function dibujarListaUsuariosHTML(users) {
    let lista_usuarios_html = $("#id_user_list");

    for (unUser of users) {
        lista_usuarios_html.append(unUsuarioHTML(unUser));
    }

}

//---------------------------------------------------------------------------------
// Hace la peticion al API para obtener la lista de tropas del usuario
//---------------------------------------------------------------------------------
function getListaTropasFromAPI(user_id, ruta) {

   // console.log('Usuario ' + user_id);
    var peticion_all = {};

    //Enviar: 
    peticion_all.user_id = user_id

    var datos_enviar = {
        peticion: JSON.stringify(peticion_all)
        // peticion: peticion_all
    }

    $.post(ruta, datos_enviar)
        .done(
            function (data) {

                let tropas = data.datos.troops;
                let buildings = data.datos.buildings;

                if (data.error != true) {

                    pintarTropasHTML(tropas);
                    pintarBuildingssHTML(buildings)
                   

                }
            }
        ).fail(
            function (data, textStatus, jqXHR) {
                console.log('error');
                console.log(textStatus + ' : ' + jqXHR);
            }
        ).always(
            function () {

            }
        );
}


function pintarTropasHTML(tropas) {
    //console.log("datos recibidos");
    //console.log(tropas);
    let lista_tropas_html = $("#user_troops");
    //{troop_id: 17, troop_name: "Archers", total: 200}

    lista_tropas_html.html("");
    for (unaTropa of tropas) {
       // console.log(unaTropa);
        lista_tropas_html.append(unaTropaHTML(unaTropa));
    }

}



function pintarBuildingssHTML(buildings) {
    //console.log("datos recibidos");
    //console.log(tropas);
    let lista_buildings_html = $("#user_buildingss");
    let main_castle_html = $("#main_castle");
    //{troop_id: 17, troop_name: "Archers", total: 200}

    lista_buildings_html.html("");
    main_castle_html.html("-");
    for (unbuilding of buildings) {
        console.log(unbuilding);

        if (unbuilding.main_castle) {
            
            main_castle_html.html(unbuilding.building_id)
        }
        
        lista_buildings_html.append(unBuildingHTML(unbuilding));
    }

}





//------------------------------------------------------------------------------------------------------
//  Dibuja datos de una Tropa
//------------------------------------------------------------------------------------------------------
function unaTropaHTML(tropa) {
    let cadena = '<tr>' +
        '<td>' +
        tropa.troop_id +
        '</td>' +
        '<td>' +
        tropa.troop_name +
        '</td>' +
        '<td>' +
        tropa.total +
        '</td>' +
        '</tr>';

    return cadena;
}


//------------------------------------------------------------------------------------------------------
//  Dibuja datos de un building
//------------------------------------------------------------------------------------------------------
/*"building_id": 1,"name": "Castle","defense": 500000,"name2": "Castle*/
function unBuildingHTML(building) {
    let cadena = '<tr>' +
        '<td>' +
        building.building_id +
        '</td>' +
        '<td>' +
        building.name +
        '</td>' +
        '<td>' +
        building.defense +
        '</td>' +
        '<td>' +
        building.name2 +
        '</td>' +
        '<td>' +
        building.kingdom +
        '</td>' +
        
        '</tr>';

    return cadena;
}






function boton_hire_troops(ruta){




    
$(".hire_troops").click(
    function (event) {
        event.preventDefault();
        var id_tropas = $(this).attr('id');

        console.log(id_tropas);

        var peticion_all = {};

        peticion_all.id_tropas = id_tropas;

        var datos_enviar = {
            peticion: JSON.stringify(peticion_all)
            // peticion: peticion_all
        }

        console.log(datos_enviar);

        $.post(ruta,
            datos_enviar

            ,
            function (data, textStatus, jqXHR) {


            }
        ).done(
            function (data) {
                //console.log("done");
                console.log(data);
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
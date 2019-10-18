
function boton_donate_money(ruta){




    
    $("#donate-money").click(
        function (event) {
            event.preventDefault();
          

            var total_a_donar = $("#money").val();
           
            console.log("Hi");
            console.log(total_a_donar);
    
            var peticion_all = {};
    
            peticion_all.total_a_donar= total_a_donar;
    
            var datos_enviar = {
                peticion: JSON.stringify(peticion_all)
                // peticion: peticion_all
            }
    
           // console.log(datos_enviar);
    
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


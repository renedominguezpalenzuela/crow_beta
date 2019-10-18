unit uCheckEvents;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils,uLog;



Type
  TCheckEvents = Class(TThread)
   public Terminar:Boolean;
   Procedure Execute; override;
 end;


implementation

uses
uConfig, uEventosTable, UListaEventos,uconexionBD, uevent_incrementargold, utiles, dateutils;

procedure TCheckEvents.Execute;
 Var

  vLog, verrorlog:Tlog;
  vPath:String;
  vConfig:TConfig;
  tablaeventos:TEventosTable;
  datosCon:TDBDatosConexion;
  unEvento:TunEvento;
  i:integer;

   mConexxion:TConexionBD;
   eventoIncrementGold:TIncrementarGold;
   fecha_ejecucion, Ahora:TdateTime;

   comparacion_fechas:Integer;

begin



       Ahora:=now;




      try
        try

            vlog:=Tlog.create('mylogs.log','/usr/sbin/crow/',false);
            vLog.info('Iniciando CheckEvents');



            vConfig:=TConfig.create('crow.conf');
            datosCon:=vConfig.getDBDatos;
            vConfig.free;


           mConexxion:=TConexionBD.Create(datosCon);
           mConexxion.Conectar(true);

         //Leer todos los datos desde la tabla
          tablaeventos:=TEventosTable.create(mConexxion);
          TablaEventos.getAllEventosFromDB();

          For i:=0 to tablaeventos.ListaEventos.GetTotalElementos()-1 do begin

              unEvento:=tablaeventos.ListaEventos.getEvento(i);

              fecha_ejecucion:=unEvento.t_ejec;

              comparacion_fechas:=CompareDateTime( fecha_ejecucion, Ahora) ;
              //si la fecha de ejecucion es menor que ahora --> no hacer nada
              if (comparacion_fechas>=0) and (fecha_ejecucion>0) then continue;



              //En dependencia del tipo de evento ejecutar accion

               Case unEvento.event_type_id of
                  1: begin
                      //Verificar si la fecha de ejecucion del evento es igual o mayor a la fecha actual

                      eventoIncrementGold:=TIncrementarGold.create();
                      eventoIncrementGold.ejecutar(mConexxion);
                      eventoIncrementGold.free();
                      ActualizarFechasEventos(1,mConexxion);
                      break;
                  end;
                  else begin

                  end;
               end;





          end;

          //Actualizo la ultima fecha en que se verificaron los eventos

          ActualizarFechasUltimaEjecucionConfig(mConexxion);



            Except
          on E: Exception do begin
            verrorlog:=Tlog.create('myerrorlogs.log','/usr/sbin/crow/',false);
            verrorlog.info('ERROR TCheckEvents: '+E.ClassName+' - '+E.Message);
            verrorlog.free;
            Terminar:=true;
          end;
        end;


      finally


        mConexxion.Conectar(false);
        mConexxion.free;

        //vLog.info('Totalz '+Inttostr(tablaeventos.ListaEventos.GetTotalElementos()));
        tablaeventos.free;
        vLog.free;
      end;


end;



end.


unit UHiloPrincipal;

{$mode objfpc}{$H+}

interface


Uses  Classes,SysUtils, uLog;

Type
  THiloPrincipal = Class(TThread)
  public Terminar:Boolean;
  Procedure Execute; override;
end;





implementation
uses
uConfig, uCheckEvents;

procedure THiloPrincipal.Execute;
 Var
  C : Integer;
  vLog, verrorlog:Tlog;
  vPath:String;
  vConfig:TConfig;

  mMainCicleTime:Integer; //tiempo en milisegundos
  checkEvents: TCheckEvents;
begin
  terminar:=false;
  C:=0;
  vlog:=Tlog.create('mylogs.log','/usr/sbin/crow/',true);

  vConfig:=TConfig.create('crow.conf');
  mMainCicleTime:=vConfig.mMainCicleTime;
   vLog.info('Cicle time '+InTtoStr(mMainCicleTime) );

  vConfig.free;
    TODO: Leer configuracion de config en cada ciclo no solo al princiio
  Repeat

      try
       //Hilo de Revision de Eventos

          checkEvents:=TCheckEvents.Create(False);
          checkEvents.FreeOnTerminate:=true;
          checkEvents.Resume;   //inicio
          checkEvents.WaitFor;  //Esperar a que termine







        Sleep(mMainCicleTime); //cada 5 segundo se ejecuta el hilo
        inc(c);
        vLog.info('INFO '+ Format('Tick : %d',[C]));

      Except
        on E: Exception do begin
          verrorlog:=Tlog.create('myerrorlogs.log','/usr/sbin/crow/',false);
          verrorlog.info('ERROR Finalizando servicio: '+E.ClassName+' - '+E.Message);
          verrorlog.free;
          Terminar:=true;
        end;
      end;

  Until terminar = true;

  vLog.free;

end;


end.


unit uconexionBD;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, ZConnection,uConfig;
type
  TConexionBD = class
    mConexxion:TZConnection;
    Constructor Create(datosCon:TDBDatosConexion);
    Destructor destroy;override;
    Procedure Conectar(conecta:boolean);
  end;

implementation

Constructor TConexionBD.Create(datosCon:TDBDatosConexion);
begin
   mConexxion:=TZConnection.Create(nil);
   mConexxion.HostName:=datosCon.mDB_HostName;
   mConexxion.Protocol:='mysql';
   mConexxion.Database:=datosCon.mDB_name;
   mConexxion.User:=datosCon.mDB_usr;
   mConexxion.Password:=datosCon.mDB_pass;
   mConexxion.Port:=datosCon.mDB_port;
end;


Procedure TConexionBD.Conectar(conecta:boolean);
begin
   if conecta then
   mConexxion.Connect
   else
   mConexxion.Disconnect;
end;

Destructor TConexionBD.destroy();
begin
  mConexxion.Disconnect;
  mConexxion.free;
end;

end.

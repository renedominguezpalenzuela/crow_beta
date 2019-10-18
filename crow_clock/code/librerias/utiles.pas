unit utiles;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, uconexionBD;

Procedure ActualizarFechasEventos(codigo_evento_tipo_evento:integer; conn:TConexionBD);
Procedure ActualizarFechasUltimaEjecucionConfig(conn:TConexionBD);
Procedure BuscarIncrementodeFecha( codigo_evento_tipo_evento:integer; conn:TConexionBD; var D:Integer; var H:Integer; var M:Integer; var S:Integer);

implementation
uses
  ZDataset, dateutils;



Procedure ActualizarFechasEventos(codigo_evento_tipo_evento:integer; conn:TConexionBD);
var
 Zquery:TZquery;
 Ahora, ProximaEjecucion:TDateTime;
  D:Integer;  H:Integer;  M:Integer;  S:Integer;
begin

  BuscarIncrementodeFecha(codigo_evento_tipo_evento, conn, D, H, M, S);
  Ahora:=now;
  ProximaEjecucion:=Ahora;
  if D<>0 then ProximaEjecucion:=IncDay(Ahora, D);
   if H<>0 then ProximaEjecucion:=IncHour(Ahora, H);
    if M<>0 then ProximaEjecucion:=IncMinute(Ahora, M);
     if S<>0 then ProximaEjecucion:=IncSecond(Ahora, S);

  Zquery:=TZquery.Create(nil);
  ZQuery.Connection:=conn.mConexxion;
  Zquery.sql.add('update time_event set t_ini=:ejecutado,t_ejec=:proxima_ejecucion  where event_type_id=:event_type_id');
  Zquery.ParamByName('ejecutado').AsDateTime:=Ahora;
  Zquery.ParamByName('proxima_ejecucion').ASDateTime:=ProximaEjecucion;
  Zquery.ParamByName('event_type_id').AsInteger:=codigo_evento_tipo_evento;

  ZQuery.ExecSQL;

  Zquery.close;
  Zquery.Connection:=nil;
  ZQuery.Free;


end;


Procedure BuscarIncrementodeFecha( codigo_evento_tipo_evento:integer; conn:TConexionBD; var D:Integer; var H:Integer; var M:Integer; var S:Integer);
var
 Zquery:TZquery;
begin
    Zquery:=TZquery.Create(nil);
  ZQuery.Connection:=conn.mConexxion;
  Zquery.sql.add('select * from event_type where id=:event_type_id');
  Zquery.ParamByName('event_type_id').AsInteger:=codigo_evento_tipo_evento;

  D:=0; H:=0; M:=0;S:=0;
  ZQuery.Open;
  if Zquery.RecordCount>0 then begin
    D:=Zquery.FieldByName('t_ejec_d').asInteger;
    H:=Zquery.FieldByName('t_ejec_h').asInteger;
    M:=Zquery.FieldByName('t_ejec_m').asInteger;
    S:=Zquery.FieldByName('t_ejec_s').asInteger;
  end;

  Zquery.close;
  Zquery.Connection:=nil;
  ZQuery.Free;

end;

//Mover a utiles
Procedure ActualizarFechasUltimaEjecucionConfig(conn:TConexionBD);
var
 Zquery:TZquery;


 Ahora:TDateTime;
begin

  Ahora:=Now;


  Zquery:=TZquery.Create(nil);
  ZQuery.Connection:=conn.mConexxion;
  Zquery.sql.add('update config set t_last_clock_check=:ejecutado');
  Zquery.ParamByName('ejecutado').AsDateTime:=Ahora;


  ZQuery.ExecSQL;

  Zquery.close;
  Zquery.Connection:=nil;
  ZQuery.Free;

end;





end.


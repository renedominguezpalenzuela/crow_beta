unit UListaEventos;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils;

type
  PunEvento = ^TunEvento;

  TunEvento = record
    event_type_id:Integer;
    t_ini:TDateTime;
    t_ejec:TdateTime;
    active:Boolean;
    extra_data:String;
  end;


  TListaEventos = Class
      private

      vListaInterna: TList;

      public

      Constructor create();
      Destructor destroy; override;
      Procedure Limpiar();
      Procedure addEvento(unEvento:TunEvento);
      Function getEvento(i:integer):TUnEvento;
      Function GetTotalElementos():integer;


  end;



implementation

  Function TListaEventos.GetTotalElementos():integer;
  begin
    result:= vListaInterna.Count;
  end;

Constructor TListaEventos.create();
begin
  vListaInterna:=TList.create();
end;


Destructor TListaEventos.destroy;

begin
  //Limpiando Memoria
  self.Limpiar();

   vListaInterna.Free;

end;

Procedure TListaEventos.addEvento(unEvento:TunEvento);
var
  unEventoPuntero:PunEvento;
begin
  new(unEventoPuntero);
  unEventoPuntero^:=unEvento;


 { unEventoPuntero^.Nombre:=unEvento.Nombre;
  unEventoPuntero^.Tipo:=unEvento.Tipo;
  unEventoPuntero^.FechaHora:=unEvento.FechaHora;}

  vListaInterna.add(unEventoPuntero);

end;

Function TListaEventos.getEvento(i:integer):TUnEvento;
var
  unEvento:TunEvento;
  unEventoPuntero:PunEvento;
begin
  unEventoPuntero:=vListaInterna.Items[i];
  unEvento:=unEventoPuntero^;
result:=unEvento;;

end;

Procedure TListaEventos.Limpiar();
var
  unEventoPuntero:PunEvento;
  X:Integer;

begin
    for x := 0 to (vListaInterna.Count - 1) do    begin
     unEventoPuntero := vListaInterna.Items[X];
     Dispose(unEventoPuntero);
   end;
end;

end.


unit uConfig;

{$mode objfpc}{$H+}

interface


uses
  Classes, SysUtils;

type

TDBDatosConexion = record
   mDB_name,
   mDB_usr,
   mDB_pass,
   mDB_HostName: string ;
   mDB_port:integer;

end;

TConfig = class
   mFullPAth:String;
   mUsarProxy:Boolean;
   mProxyIP:String;
   mProxyPort:Integer;
   mDB_name,
   mDB_usr,
   mDB_pass,
   mDB_HostName: string ;
   mDB_port:integer;


   mMainCicleTime:Integer;
   mDebug:Boolean;

   Constructor create(NomFichero:String='config.conf'; PathFichero:String='/usr/sbin/crow/');
   Destructor Destroy; Override;
   procedure CreateConfigFileForFirstTime(FullPAth:String);
   Function getDBDatos():TDBDatosConexion;
end;


implementation
uses
  inifiles;
//lee el fichero de config

Constructor TConfig.create(NomFichero:String='config.conf'; PathFichero:String='/usr/sbin/crow/');
var
   MiFichero : Tinifile;

begin

// mNomFichero:='gdss.conf';
// mPathFichero:=ApplicationName ;
// mPathFichero:=ParamStr(0);
// mPathFichero:='/sbin/gdss/';

mFullPAth:=PathFichero+NomFichero;

if NOT(FileExists(mFullPAth)) then begin
   CreateConfigFileForFirstTime(mFullPAth) ;
end;

MiFichero:=TIniFile.Create(mFullPAth);

mMainCicleTime:=MiFichero.ReadInteger('main', 'cicle_time_minutes', 5);
        mDebug:=MiFichero.ReadBool('main', 'debug', false);

     mUSarProxy:=MiFichero.ReadBool('main', 'use_proxy', false);
     mProxyIP:= MiFichero.ReadString('main', 'proxy_ip', '127.0.0.1');
     mProxyPort:= MiFichero.ReadInteger('main', 'proxy_port', 9090);


     mDB_name:= MiFichero.ReadString('main','db_name','crow_db');
     mDB_usr:=MiFichero.ReadString('main','dp_usr','root');
     mDB_pass:= MiFichero.ReadString('main','db_pass','123');
     mDB_HostName := MiFichero.ReadString('main','db_hostname','localhost');
     mDB_port:=MiFichero.ReadInteger('main','db_port',3306);



    MiFichero.free;



     if mdebug then begin
       mMainCicleTime:=mMainCicleTime*1000; //si debug el tiempo en segundos
     end else begin
       mMainCicleTime:= mMainCicleTime*60*1000; //si no debug tiempo en minutos
     end;



  end;

Function TConfig.getDBDatos():TDBDatosConexion;
var
   datos:TDBDatosConexion;
begin

    datos.mDB_HostName:=mDB_HostName;
    datos.mDB_name:=mDB_name;
    datos.mDB_pass:=mDB_pass;
    datos.mDB_port:=mDB_port;
    datos.mDB_usr:=mDB_usr;

    result:= datos;
end;



Destructor TConfig.Destroy();
begin
  //
end;


//Ejutando por primera ves, creando el .conf si no existe
procedure TConfig.CreateConfigFileForFirstTime(FullPAth:String);
var
  MyFile: TIniFile;
begin
   MyFile := TIniFile.Create(FullPAth);

      MyFile.WriteBool('main', 'debug', false);
      MyFile.WriteInteger('main', 'cicle_time_minutes', 5);

      MyFile.WriteBool('main','use_proxy',false);
      MyFile.WriteString('main','proxy_ip','127.0.0.1');
      MyFile.WriteInteger('main','proxy_port',9090);

      MyFile.WriteString('main','db_name','crow_db');
      MyFile.WriteString('main','dp_usr','root');
      MyFile.WriteString('main','db_pass','123');
      MyFile.WriteString('main','db_hostname','localhost');
      MyFile.WriteInteger('main','db_port',3306);

   MyFile.Free;
end;


end.


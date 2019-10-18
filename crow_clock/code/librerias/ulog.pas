unit uLog;

{$mode objfpc}{$H+}

{
USO

var
   log:Tlog;

   log:=Tlog.create('mylogs.log','/usr/sbin/crow/',true);
   log.info('hola mundo');
   log.free;


}

interface
uses
 Classes, SysUtils;


type
 TLOG = class

   mFullPAth:String;  //Camino completo al fichero de logs
   mFull_Path_Done:String; //Camino backup de los logs completo
   mFileName:String; //solo el nombre
   mFilePath:String; //solo el camino
   mdebug:boolean;
   FTimeFormat:String;

   Constructor Create(NombreFichero:String;CaminoFichero:String=''; NewLog:Boolean=false);

   Destructor Destroy; override;
   Procedure info(texto:String);
   Procedure debug(texto:String);
   Procedure setDebugTrue();
   Procedure backupLog(camino_logs_done:string);




 end;



implementation
uses
 FileUtil;

Destructor TLOG.Destroy;
begin

end;

Procedure TLOG.backupLog(camino_logs_done:string);
var
     TS:String;
begin
     //mover fichero a logs_done, agregandole la fecha
  mFull_Path_Done:= camino_logs_done + TS+'_'+mFileName;
  CopyFile(mFullPAth, mFull_Path_Done);
  DeleteFile(mFullPAth);

end;

 Constructor TLOG.Create(NombreFichero:String;CaminoFichero:String=''; NewLog:Boolean=false);
 var
    mTextF: TextFile;

begin

  FTimeFormat:='yyyy-mm-dd hh:nn:ss';

  mdebug:=false;
  //camino_logs:='/var/log/gdss/';
  //camino_logs_done:=camino_logs+'done/';

  //verificar si existe la carpeta logs, si no existe crearla
 { if NOT(DirectoryExists(camino_logs)) then begin
    CreateDir(camino_logs);
  end;

    //verificar si existe la carpeta logs, si no existe crearla
  if NOT(DirectoryExists(camino_logs_done)) then begin
    CreateDir(camino_logs_done);
  end;}


  mFullPAth:= CaminoFichero + NombreFichero;

  AssignFile(mTextF,mFullPAth);
  if NewLog then
     Rewrite(mTextF)
  else begin

    if FileExists(mFullPAth) then begin
       Append(mTextF);
    end else begin
       Rewrite(mTextF)
    end;

  end;
 // Writeln(mTextF, TS+ '---------------  Iniciando sistema de logs ---------------');
  CloseFile(mTextF);

end;

Procedure TLOG.info(texto:String);
var

    mTextF: TextFile;
    TS:String;
begin


    TS:=FormatDateTime(FTimeFormat,Now);
    AssignFile(mTextF,mFullPAth);
    Append(mTextF) ;
    Writeln(mTextF, TS+ ' ' + texto);
    CloseFile(mTextF);
end;


Procedure TLOG.debug(texto:String);
var
    TS: String;
    mTextF: TextFile;
begin
    if mdebug=false then exit;



    TS:=FormatDateTime(FTimeFormat,Now);
    AssignFile(mTextF,mFullPAth);
    Append(mTextF) ;
    Writeln(mTextF, TS+ ' ' + texto);
    CloseFile(mTextF);
end;


Procedure TLOG.setDebugTrue();
begin
 mdebug:=true;
end;

end.


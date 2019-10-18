Program crow_clock;

Uses
   HeapTrc,
  CThreads,
  cmem,

  DaemonApp, lazdaemonapp, DaemonMapperUnit1, DaemonUnit1,  uLog,
UHiloPrincipal, uConfig, uCheckEvents, uEventosTable, UListaEventos,
uevent_incrementargold, uconexionBD, utiles;

begin
  SetHeapTraceOutput('trace.log');
  Application.Title:='Daemon application';
  Application.Initialize;
  Application.Run;
end.

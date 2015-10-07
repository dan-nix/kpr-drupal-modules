@echo off 

:: don't know what this is:
::if "%!%"=="!" goto bigenv
::set !=!|%comspec% /e:2048 /c %0 %1 %2 %3 %4 %5 %6 %7 %8 %9
::set !=|exit
::bigenv

:: verify that the input file is .wav
if %~x1 neq .wav goto :exit

::	Edit these four parameters:
set ftphost=129.237.213.244
set ftpuser=kpruploader
set ftppass=T3mppass!
set ftppath=/

echo Filename is %1

echo open %ftphost%>ftpin
echo %ftpuser%>>ftpin
echo %ftppass%>>ftpin
echo type binary>>ftpin
echo cd %ftppath%>>ftpin
echo send %1>>ftpin
echo ls -a>>ftpin
echo bye>>ftpin
ftp -i -s:ftpin
:end
if exist ftpin del ftpin>nul
:stop

:exit
exit
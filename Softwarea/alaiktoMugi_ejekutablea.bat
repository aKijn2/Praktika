@echo off
REM Helburuko direktorioa ezarri
set "HELBURU_DIR=%~dp0alaiktoMugi"

REM Direktorioa existitzen den egiaztatu
if not exist "%HELBURU_DIR%" (
    echo Direktiorioa ez da aurkitu: %HELBURU_DIR%
    pause
    exit /b 1
)

REM Direktoriora joan
cd /d "%HELBURU_DIR%"

REM JAR fitxategia existitzen den egiaztatu
if not exist "alaiktoMugi.jar" (
    echo JAR fitxategia ez da aurkitu: alaiktoMugi.jar
    pause
    exit /b 1
)

REM Java aplikazioa abiarazi
java -jar alaiktoMugi.jar

pause

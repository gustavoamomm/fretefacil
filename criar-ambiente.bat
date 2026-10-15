
@echo off
setlocal
cd /d "%~dp0"

if not exist ".env" (
    echo ERRO: Arquivo .env nao encontrado.
    pause
    exit /b 1
)

docker image inspect fretefacil-php-apache:1.0 >nul 2>&1
if errorlevel 1 (
    echo Execute primeiro criar-imagens.bat.
    pause
    exit /b 1
)

docker image inspect fretefacil-mysql:8.4 >nul 2>&1
if errorlevel 1 (
    echo Execute primeiro criar-imagens.bat.
    pause
    exit /b 1
)

echo Verificando a network...

docker network inspect fretefacil_net >nul 2>&1
if errorlevel 1 (
    docker network create fretefacil_net
    if errorlevel 1 goto erro
)

echo Criando o volume persistente do banco...
docker volume create fretefacil_db_data
if errorlevel 1 goto erro

echo.
echo Criando o container MySQL...

docker run -d ^
  --name fretefacil_db ^
  --network fretefacil_net ^
  --network-alias db ^
  --env-file .env ^
  --mount source=fretefacil_db_data,target=/var/lib/mysql ^
  --health-cmd="mysqladmin ping -h 127.0.0.1 --silent" ^
  --health-interval=5s ^
  --health-timeout=5s ^
  --health-retries=20 ^
  fretefacil-mysql:8.4

if errorlevel 1 goto erro

echo.
echo Aguardando o banco ficar pronto...

set /a tentativa=0

:esperar_banco
set /a tentativa+=1
set "status="

for /f "delims=" %%H in ('docker inspect --format="{{.State.Health.Status}}" fretefacil_db 2^>nul') do set "status=%%H"

if /i "%status%"=="healthy" goto iniciar_app
if %tentativa% GEQ 30 goto erro_banco

timeout /t 2 /nobreak >nul
goto esperar_banco

:iniciar_app
echo.
echo Criando o container PHP + Apache...

docker run -d ^
  --name fretefacil_app ^
  --network fretefacil_net ^
  --publish 8080:80 ^
  --env-file .env ^
  fretefacil-php-apache:1.0

if errorlevel 1 goto erro

echo.
echo Ambiente criado!
echo Acesse: http://localhost:8080

docker ps
start "" "http://localhost:8080"
pause
exit /b 0

:erro_banco
echo ERRO: O banco nao ficou pronto a tempo.
docker logs fretefacil_db
pause
exit /b 1

:erro
echo.
echo ERRO: Falha ao criar o ambiente.
echo Verifique os comandos e os logs dos containers.
pause
exit /b 1

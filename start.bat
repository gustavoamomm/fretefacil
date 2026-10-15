
@echo off
setlocal
cd /d "%~dp0"

echo.
echo 1. Verificando o Docker...
docker info >nul 2>&1
if errorlevel 1 (
    echo ERRO: Inicie o Docker Desktop.
    pause
    exit /b 1
)

echo.
echo 2. Verificando configuracao...
if not exist ".env" (
    echo ERRO: Arquivo .env nao encontrado.
    echo Crie o arquivo usando o modelo .env.example.
    pause
    exit /b 1
)

echo.
echo 3. Criando a rede, se necessario...
docker network inspect fretefacil_net >nul 2>&1
if errorlevel 1 (
    docker network create fretefacil_net
    if errorlevel 1 goto erro
)

echo.
echo 4. Criando o volume, se necessario...
docker volume inspect fretefacil_db_data >nul 2>&1
if errorlevel 1 (
    docker volume create fretefacil_db_data
    if errorlevel 1 goto erro
)

echo.
echo 5. Verificando a imagem MySQL...
docker image inspect fretefacil-mysql:8.0 >nul 2>&1
if errorlevel 1 (
    echo Imagem MySQL nao encontrada. Construindo...
    docker build -f dockerfile-mysql -t fretefacil-mysql:8.0 .
    if errorlevel 1 goto erro
) else (
    echo Imagem MySQL ja existe.
)

echo.
echo 6. Verificando a imagem PHP + Apache...
docker image inspect fretefacil-php-apache:1.0 >nul 2>&1
if errorlevel 1 (
    echo Imagem PHP + Apache nao encontrada. Construindo...
    docker build -f dockerfile-apache -t fretefacil-php-apache:1.0 .
    if errorlevel 1 goto erro
) else (
    echo Imagem PHP + Apache ja existe.
)

echo.
echo 7. Verificando o container MySQL...
docker container inspect fretefacil_db >nul 2>&1
if errorlevel 1 (
    echo Container MySQL nao existe. Criando...
    docker run -d --name fretefacil_db --network fretefacil_net --network-alias db -p 3306:3306 --env-file .env --mount source=fretefacil_db_data,target=/var/lib/mysql fretefacil-mysql:8.0
    if errorlevel 1 goto erro
) else (
    docker ps --filter "name=^/fretefacil_db$" --filter "status=running" -q | findstr . >nul
    if errorlevel 1 (
        echo Iniciando container MySQL...
        docker start fretefacil_db
        if errorlevel 1 goto erro
    ) else (
        echo Container MySQL ja esta em execucao.
    )
)

echo.
echo 8. Aguardando o MySQL ficar pronto...
set "TENTATIVAS=0"

:aguardar_mysql
docker exec fretefacil_db mysqladmin ping -h 127.0.0.1 --silent >nul 2>&1
if not errorlevel 1 goto banco_pronto

set /a TENTATIVAS+=1
if %TENTATIVAS% GEQ 60 goto mysql_erro

timeout /t 2 /nobreak >nul
goto aguardar_mysql

:mysql_erro
echo.
echo ERRO: O MySQL nao respondeu dentro do tempo esperado.
docker logs --tail 30 fretefacil_db
pause
exit /b 1

:banco_pronto
echo MySQL pronto!

echo.
echo 9. Verificando o container PHP + Apache...
docker container inspect fretefacil_app >nul 2>&1
if errorlevel 1 (
    echo Container PHP nao existe. Criando...
    docker run -d --name fretefacil_app --network fretefacil_net -p 8080:80 --env-file .env fretefacil-php-apache:1.0
    if errorlevel 1 goto erro
) else (
    docker ps --filter "name=^/fretefacil_app$" --filter "status=running" -q | findstr . >nul
    if errorlevel 1 (
        echo Iniciando container PHP + Apache...
        docker start fretefacil_app
        if errorlevel 1 goto erro
    ) else (
        echo Container PHP + Apache ja esta em execucao.
    )
)

echo.
echo 10. Verificando os containers...
docker ps

echo.
echo Ambiente iniciado!
echo Site: http://localhost:8080
echo MySQL Workbench: 127.0.0.1, porta 3306
echo.

start "" "http://localhost:8080"
pause
exit /b 0

:erro
echo.
echo ERRO ao configurar ou iniciar o ambiente Docker.
pause
exit /b 1

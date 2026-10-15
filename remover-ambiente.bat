
@echo off
setlocal

echo ATENCAO: Este script removera os containers,
echo as imagens do projeto e a network.
echo O volume com os dados do banco sera preservado.
echo.

set /p confirmar=Deseja continuar? (S/N):
if /i not "%confirmar%"=="S" exit /b 0

echo Removendo containers...
docker rm -f fretefacil_app >nul 2>&1
docker rm -f fretefacil_db >nul 2>&1

echo Removendo network...
docker network rm fretefacil_net >nul 2>&1

echo Removendo imagem PHP + Apache...
docker rmi fretefacil-php-apache:1.0

echo Removendo imagem personalizada MySQL...
docker rmi fretefacil-mysql:8.0

echo.
echo Remocao concluida.
echo O volume fretefacil_db_data foi preservado.
pause


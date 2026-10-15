
@echo off
setlocal
cd /d "%~dp0"

docker info >nul 2>&1
if errorlevel 1 (
    echo ERRO: Inicie o Docker Desktop primeiro.
    pause
    exit /b 1
)

echo Construindo a imagem PHP + Apache...

docker build -t fretefacil-php-apache:1.0 -f Dockerfile .
if errorlevel 1 goto erro

echo Construindo a imagem personalizada MySQL...

docker build -t fretefacil-mysql:8.4 -f Dockerfile.mysql .
if errorlevel 1 goto erro

echo.
echo Imagens criadas com sucesso!
docker images
pause
exit /b 0

:erro
echo.
echo ERRO: Falha na criacao de uma imagem.
pause
exit /b 1

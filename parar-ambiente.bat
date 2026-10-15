
@echo off
setlocal

echo Parando o container da aplicacao...
docker stop fretefacil_app >nul 2>&1

echo Parando o container do banco...
docker stop fretefacil_db >nul 2>&1

echo.
echo Containers parados.
echo Os containers, as imagens e os dados foram preservados.
pause

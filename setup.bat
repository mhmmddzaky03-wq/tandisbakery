@echo off
echo === Tandi's Bakery - Setup ===
cd /d "%~dp0"

call "%~dp0artisan.bat" migrate --force
if errorlevel 1 exit /b 1

call "%~dp0artisan.bat" db:seed --force
if errorlevel 1 (
    echo.
    echo Catatan: jika seed gagal karena data duplikat, coba:
    echo   artisan.bat migrate:fresh --seed
    exit /b 1
)

echo.
echo Selesai! Login: admin / admin123
echo Jalankan server: artisan.bat serve
pause

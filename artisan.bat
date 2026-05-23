@echo off
REM Jalankan artisan dengan PHP dari Laragon (tanpa perlu php di PATH)
set "LARAGON_PHP_ROOT=D:\Laragon\laragon\bin\php"
set "PHP_EXE="

if exist "%LARAGON_PHP_ROOT%" (
    for /f "delims=" %%D in ('dir /b /ad /o-n "%LARAGON_PHP_ROOT%\php-*" 2^>nul') do (
        if exist "%LARAGON_PHP_ROOT%\%%D\php.exe" (
            set "PHP_EXE=%LARAGON_PHP_ROOT%\%%D\php.exe"
            goto :found
        )
    )
)

:found
if not defined PHP_EXE (
    echo [ERROR] PHP Laragon tidak ditemukan di %LARAGON_PHP_ROOT%
    echo Pasang PHP lewat Laragon, atau edit path di artisan.bat
    exit /b 1
)

"%PHP_EXE%" "%~dp0artisan" %*

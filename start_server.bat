@echo off
title Imolites in Diaspora Social Club USA - PHP Server
echo ========================================================
echo   Imolites in Diaspora Social Club USA - Local Server
echo ========================================================
echo.
echo Starting PHP built-in web server...
echo URL: http://localhost:8000
echo.

where php >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PHP_BIN=php
) else if exist "C:\xampp\php\php.exe" (
    set PHP_BIN=C:\xampp\php\php.exe
) else (
    echo [ERROR] PHP executable was not found in PATH or at C:\xampp\php\php.exe.
    echo Please ensure PHP or XAMPP is installed.
    pause
    exit /b 1
)

start http://localhost:8000
"%PHP_BIN%" -S localhost:8000
pause


::[Bat To Exe Converter]
::
::YAwzoRdxOk+EWAjk
::fBw5plQjdCyDJGyX8VAjFDpaSBabAE+1EbsQ5+n//Na/skgTR90zd4jUlL2NL4A=
::YAwzuBVtJxjWCl3EqQJgSA==
::ZR4luwNxJguZRRnk
::Yhs/ulQjdF+5
::cxAkpRVqdFKZSDk=
::cBs/ulQjdF+5
::ZR41oxFsdFKZSDk=
::eBoioBt6dFKZSDk=
::cRo6pxp7LAbNWATEpCI=
::egkzugNsPRvcWATEpCI=
::dAsiuh18IRvcCxnZtBJQ
::cRYluBh/LU+EWAnk
::YxY4rhs+aU+IeA==
::cxY6rQJ7JhzQF1fEqQJhZkk0
::ZQ05rAF9IBncCkqN+0xwdVsFAlbi
::ZQ05rAF9IAHYFVzEqQIIOB5aX2Q=
::eg0/rx1wNQPfEVWB+kM9LVsJDGQ=
::fBEirQZwNQPfEVWB+kM9LVsJDGQ=
::cRolqwZ3JBvQF1fEqQIIOB5aX0SWLmq5DbAOiA==
::dhA7uBVwLU+EWHiK8FApDB5CLA==
::YQ03rBFzNR3SWATElA==
::dhAmsQZ3MwfNWATEphJifVt2VUSjMm+oH9U=
::ZQ0/vhVqMQ3MEVWAtB9wSA==
::Zg8zqx1/OA3MEVWAtB9wSA==
::dhA7pRFwIByZRRnk
::Zh4grVQjdCyDJE6F+VJmfCdDWxO+BHu/CKYg0Pj+4fnJp1UYNA==
::YB416Ek+ZG8=
::
::
::978f952a14a936cc963da21a135fa983
@echo off
:: Xpens Launcher v2.1
:: --------------------------------------------------
chcp 65001 >nul
setlocal EnableDelayedExpansion

:: ---------- 1. App identity ----------
set "OWNER=Andrianaivo Noé (Andry)"
set "APP_NAME=Xpens"
set "VERSION=2.1"
set "DESCRIPTION=Simple Expense Tracking App (Local Dev Mode)"
set "DEFAULT_PORT=8000"
set "ENV_FILE=.env"
set "LOG_FILE=logs\error.log"

:: ---------- 2. Colours ----------
for /f %%a in ('echo prompt $E^|cmd') do set "ESC=%%a"
set "_R=%ESC%[91m" & set "_G=%ESC%[92m" & set "_Y=%ESC%[93m" & set "_B=%ESC%[94m" & set "_RESET=%ESC%[0m"

:: ---------- 3. Header ----------
cls
echo %_B%---------------------------------------------------%_RESET%
echo     %_G%%APP_NAME%%_RESET% - %DESCRIPTION%
echo     Owner: %OWNER%
echo     Version: %VERSION%
echo %_B%---------------------------------------------------%_RESET%
echo.
echo [🔐] Authentication: Register, Login, Logout
echo [👤] User Management
echo [📋] List & Product Management
echo [💰] Purchase Tracking & Filtering
echo [📊] Total Expense Reporting
echo %_B%---------------------------------------------------%_RESET%
echo.

:: ---------- 4. .env file ----------
if not exist "%ENV_FILE%" (
    echo %_Y%⚠️  Missing %ENV_FILE% file in root.%_RESET%
    pause
    exit /b
)

:: ---------- 5. Port input ----------
set "port="
set /p "port=Enter port (blank = default [%DEFAULT_PORT%]): "
if "%port%"=="" set "port=%DEFAULT_PORT%"

:: ---------- 6. Check port availability ----------
netstat -ano | findstr /r /c:":%port% *[^ ]*:[^ ]* *LISTENING" >nul
if not errorlevel 1 (
    echo %_R%❌ Port %port% is already in use.%_RESET%
    pause
    exit /b
)

:: ---------- 7. Check PHP ----------
php -v >nul 2>&1
if errorlevel 1 (
    echo %_R%❌ PHP is not installed or not in PATH.%_RESET%
    pause
    exit /b
)

:: ---------- 8. Check MySQL / MariaDB ----------
mysql -h127.0.0.1 -P3306 -u root -e "SELECT 1;" >nul 2>&1
if errorlevel 1 (
    echo %_Y%⚠️  MySQL/MariaDB is not reachable on 127.0.0.1:3306%_RESET%
    pause
    exit /b
)

:: ---------- 9. Launch App ----------
echo.
echo %_G%✅ Starting %APP_NAME% on http://localhost:%port% ...%_RESET%
start "" http://localhost:%port%
php -S localhost:%port%

:: ---------- 10. Optional log file ----------
if exist "%LOG_FILE%" (
    choice /C YN /N /M "Open log file? [Y/N] "
    if not errorlevel 2 start notepad "%LOG_FILE%"
)

echo.
echo %_B%🛑 Server stopped.%_RESET%
pause
exit /b

@echo off
@cls
@for /f "tokens=3 delims=: " %%i  in ('netsh interface ip show config name^="Ethernet" ^| findstr "IP Address"') do SET myip=%%i%
title iExplorer
mode 75,35
color f
SETLOCAL EnableDelayedExpansion
echo    d8,                           d8b                                  
echo   `8P                            88P                                  
echo                                 d88                                   
echo    88b d8888b?88,  88P?88,.d88b,888   d8888b   88bd88b d8888b  88bd88b
echo    88Pd8b_,dP `?8bd8P'`?88'  ?88?88  d8P' ?88  88P'  `d8b_,dP  88P'  `
echo   d88 88b     d8P?8b,   88b  d8P 88b 88b  d88 d88     88b     d88     
echo  d88' `?888P'd8P' `?8b  888888P'  88b`?8888P'd88'     `?888P'd88'     
echo                         88P'                                          
echo                        d88                                            
echo                        ?8P                                           
echo.
echo        Autor:  Bill Rocha - https://billrocha.netlify.app
echo        Link:   http://%myip%
echo        Path:   %~dp0
echo.
echo  ----------------------------------------------------------------------
echo.
echo.
@explorer http://%myip%
@php -S 0.0.0.0:80 %~dp0iExplorer.phar
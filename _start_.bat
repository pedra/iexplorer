@echo off
@for /f "tokens=3 delims=: " %%i  in ('netsh interface ip show config name^="Ethernet" ^| findstr "IP Address"') do SET myip=%%i%
echo http://%myip%
php -S 0.0.0.0:80 -t %~dp0
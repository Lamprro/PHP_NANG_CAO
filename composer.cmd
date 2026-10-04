@echo off
setlocal
set "PATH=%~dp0.tools\php-8.4.24;%PATH%"
set "COMPOSER_HOME=%~dp0.tools\composer-home"
set "COMPOSER_CACHE_DIR=%~dp0.tools\composer-cache"
"%~dp0.tools\php-8.4.24\php.exe" "%~dp0.tools\composer.phar" %*

@echo off
setlocal
set "TASK_NODE_DIR=%USERPROFILE%\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\bin"
set "PATH=%TASK_NODE_DIR%;%PATH%"
"%TASK_NODE_DIR%\node.exe" "%~dp0.tools\npm\package\bin\npm-cli.js" %*

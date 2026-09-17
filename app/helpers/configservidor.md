# ejecutar desde windows
cmd.exe /c cd C:\laragon\www\mar && php artisan serve --host=0.0.0.0 --port=8008 > serve.log 2>&1


Set WshShell = CreateObject("WScript.Shell")
WshShell.CurrentDirectory = "C:\ruta\a\tu\proyecto"
WshShell.Run "cmd /c php artisan serve --host=0.0.0.0 --port=8008 > serve.log 2>&1", 0, False

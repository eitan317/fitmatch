@echo off
powershell -Command "(Get-Content .env) | ForEach-Object { $_ -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' -replace '# DB_HOST=127.0.0.1', 'DB_HOST=127.0.0.1' -replace '# DB_PORT=3306', 'DB_PORT=3306' -replace '# DB_DATABASE=laravel', 'DB_DATABASE=fitmatch' -replace '# DB_USERNAME=root', 'DB_USERNAME=root' -replace '# DB_PASSWORD=', 'DB_PASSWORD=' } | Set-Content .env"


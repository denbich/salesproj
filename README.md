## Jak zainstalować
Ten projekt jest w frameworku laravel i trzeba wykonać następujące czyności:

- Pobrać repozytorium w miejsce docelowe
- Użyć komendy **composer install** (Instalacja potrzebnych reporytoriów)
- Stworzyć i konfigurować plik .ENV (root folder)
- W konsoli użyć komendy: **php artisan key:generate** (Kenerowanie klucza)
- W konsoli użyć komendy: **php artisan migrate** (Migracja tabel do bazy danych)
- Wywołać dodanie przykładowych danych do bazy: **php artisan db:seed --class=SaleSeeder** (Importowanie pliku sql)

## Licencja

All Rights Reserved 2024 - Denis Bichler

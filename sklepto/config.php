<?php
// Konfiguracja aplikacji SklepTO (aplikacja testowa do zajęć z testowania).
// Domyślne ustawienia odpowiadają świeżej instalacji XAMPP (MySQL/MariaDB, użytkownik root bez hasła).
define('DB_HOST', getenv('SKLEPTO_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('SKLEPTO_DB_PORT') ?: '3306');
define('DB_NAME', getenv('SKLEPTO_DB_NAME') ?: 'sklepto');
define('DB_USER', getenv('SKLEPTO_DB_USER') ?: 'root');
define('DB_PASS', getenv('SKLEPTO_DB_PASS') ?: '');

// true  = aplikacja zawiera celowo wprowadzone defekty (wersja dla studentów),
// false = wersja poprawna (wzorcowa, dla prowadzącego).
define('BUGI', getenv('SKLEPTO_BUGI') === false ? true : getenv('SKLEPTO_BUGI') === '1');

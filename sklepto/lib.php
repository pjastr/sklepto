<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function pdo_server(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
    return new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo '<p id="blad-bazy">Brak połączenia z bazą danych. Uruchom MySQL w XAMPP '
                . 'i otwórz <a href="install.php">install.php</a>.</p>';
            exit;
        }
    }
    return $pdo;
}

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function flash(string $typ, string $tekst): void
{
    $_SESSION['flash'] = ['typ' => $typ, 'tekst' => $tekst];
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $st = db()->prepare('SELECT * FROM users WHERE id = ?');
    $st->execute([$_SESSION['user_id']]);
    $u = $st->fetch();
    return $u ?: null;
}

const STATUSY = [
    'NEW' => 'Nowe',
    'PAID' => 'Opłacone',
    'SHIPPED' => 'Wysłane',
    'DELIVERED' => 'Dostarczone',
    'CANCELLED' => 'Anulowane',
];

function naglowek(string $tytul): void
{
    $u = current_user();
    echo "<!DOCTYPE html>\n<html lang=\"pl\">\n<head>\n<meta charset=\"utf-8\">\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    echo '<title>' . h($tytul) . "</title>\n";
    echo '<link rel="stylesheet" href="style.css">' . "\n</head>\n<body>\n";
    echo '<header><h1 id="logo"><a href="index.php">SklepTO</a></h1>';
    echo '<nav id="menu">';
    echo '<a id="nav-home" href="index.php">Start</a>';
    echo '<a id="nav-rejestracja" href="rejestracja.php">Rejestracja</a>';
    echo '<a id="nav-kalkulator" href="kalkulator.php">Kalkulator</a>';
    echo '<a id="nav-zamowienia" href="zamowienia.php">Zamówienia</a>';
    echo '<a id="nav-kontakt" href="kontakt.php">Kontakt</a>';
    echo '<a id="nav-admin" href="admin.php">Administracja</a>';
    if ($u) {
        echo '<span id="zalogowany">Zalogowano: ' . h($u['login']) . '</span>';
        echo '<a id="nav-wyloguj" href="wyloguj.php">Wyloguj</a>';
    } else {
        echo '<a id="nav-logowanie" href="logowanie.php">Logowanie</a>';
    }
    echo "</nav></header>\n<main>\n";
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div id="flash" class="flash ' . h($f['typ']) . '">' . h($f['tekst']) . "</div>\n";
    }
}

function stopka(): void
{
    echo "\n</main>\n<footer id=\"stopka\">SklepTO – aplikacja do nauki testowania. Wersja 1.0</footer>\n";
    echo "</body>\n</html>\n";
}

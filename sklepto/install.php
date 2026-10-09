<?php
// Tworzy bazę danych i tabele oraz wstawia dane startowe.
// install.php          – utworzenie bazy (jeśli nie istnieje) i danych startowych
// install.php?reset=1  – usunięcie wszystkich danych i przywrócenie stanu początkowego
require_once __DIR__ . '/config.php';

function seed(PDO $pdo): void
{
    $users = [
        ['jan', 'jan@example.com', 30, 'Haslo123', 0, 'ACTIVE'],
        ['anna', 'anna@example.com', 25, 'Tajne456', 0, 'ACTIVE'],
        ['zablokowany', 'zb@example.com', 40, 'Haslo123', 3, 'LOCKED'],
    ];
    $st = $pdo->prepare('INSERT INTO users (login, email, wiek, password_hash, failed_attempts, status)'
        . ' VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($users as $u) {
        $u[3] = password_hash($u[3], PASSWORD_DEFAULT);
        $st->execute($u);
    }
    $orders = [[1, '100.00', 'NEW'], [1, '250.00', 'PAID'], [1, '80.00', 'SHIPPED'],
               [1, '40.00', 'DELIVERED'], [1, '60.00', 'CANCELLED'], [2, '120.00', 'NEW']];
    $st = $pdo->prepare('INSERT INTO orders (user_id, kwota, status) VALUES (?, ?, ?)');
    foreach ($orders as $o) {
        $st->execute($o);
    }
}

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci');
    $pdo->exec('USE `' . DB_NAME . '`');
    if (isset($_GET['reset'])) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['order_history', 'orders', 'users', 'messages'] as $t) {
            $pdo->exec("DROP TABLE IF EXISTS $t");
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
    $sql = file_get_contents(__DIR__ . '/sql/sklepto.sql');
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        $pdo->exec($stmt);
    }
    if ((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
        seed($pdo);
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="pl"><head><meta charset="utf-8"><title>Instalacja SklepTO</title></head><body>';
    echo '<p id="install-status">OK</p><p>Baza danych <b>' . DB_NAME . '</b> jest gotowa. ';
    echo '<a href="index.php">Przejdź do aplikacji</a></p></body></html>';
} catch (PDOException $e) {
    http_response_code(500);
    echo '<p id="install-status">BŁĄD</p><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>Sprawdź, czy moduł MySQL jest uruchomiony w XAMPP Control Panel.</p>';
}

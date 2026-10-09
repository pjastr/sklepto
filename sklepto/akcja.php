<?php
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/przejscia.php';
// Obsługuje zmianę statusu zamówienia. Parametry: id, akcja (POST lub GET).
$u = current_user();
if (!$u) {
    flash('error', 'Zaloguj się, aby zobaczyć zamówienia.');
    redirect('logowanie.php');
}
$id = (int)($_REQUEST['id'] ?? 0);
$akcja = (string)($_REQUEST['akcja'] ?? '');
$st = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$st->execute([$id, $u['id']]);
$o = $st->fetch();
if (!$o) {
    flash('error', 'Nie znaleziono zamówienia.');
    redirect('zamowienia.php');
}
$akcje = dozwolone_akcje($o['status']);
if (!isset($akcje[$akcja])) {
    flash('error', 'Niedozwolone przejście: ' . STATUSY[$o['status']] . ' → ' . $akcja . '.');
    redirect('zamowienia.php');
}
$nowy = $akcje[$akcja];
db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$nowy, $id]);
db()->prepare('INSERT INTO order_history (order_id, z_statusu, na_status) VALUES (?, ?, ?)')
    ->execute([$id, $o['status'], $nowy]);
flash('success', 'Zamówienie #' . $id . ': ' . STATUSY[$o['status']] . ' → ' . STATUSY[$nowy] . '.');
redirect('zamowienia.php');

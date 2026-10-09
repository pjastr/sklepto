<?php
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/przejscia.php';

$u = current_user();
if (!$u) {
    flash('error', 'Zaloguj się, aby zobaczyć zamówienia.');
    redirect('logowanie.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nowe'])) {
    $kwota = str_replace(',', '.', trim($_POST['kwota'] ?? ''));
    if (!is_numeric($kwota) || (float)$kwota <= 0) {
        flash('error', 'Kwota zamówienia musi być dodatnią liczbą.');
    } else {
        db()->prepare("INSERT INTO orders (user_id, kwota, status) VALUES (?, ?, 'NEW')")
            ->execute([$u['id'], round((float)$kwota, 2)]);
        flash('success', 'Utworzono zamówienie #' . db()->lastInsertId() . '.');
    }
    redirect('zamowienia.php');
}
$st = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id');
$st->execute([$u['id']]);
$orders = $st->fetchAll();
naglowek('SklepTO – zamówienia');
?>
<h2>Moje zamówienia</h2>
<form id="form-nowe" method="post" action="zamowienia.php">
  <label for="kwota">Kwota nowego zamówienia (zł)</label>
  <input type="text" id="kwota" name="kwota" value="100.00">
  <button type="submit" id="nowe-zamowienie" name="nowe" value="1">Nowe zamówienie</button>
</form>
<table id="zamowienia">
  <tr><th>Nr</th><th>Kwota</th><th>Status</th><th>Akcje</th></tr>
<?php foreach ($orders as $o): ?>
  <tr id="zam-<?= (int)$o['id'] ?>" data-status="<?= h($o['status']) ?>">
    <td class="nr"><?= (int)$o['id'] ?></td>
    <td class="kwota"><?= number_format((float)$o['kwota'], 2, '.', '') ?> zł</td>
    <td class="status"><?= h(STATUSY[$o['status']]) ?></td>
    <td class="akcje">
<?php foreach (dozwolone_akcje($o['status']) as $akcja => $cel): ?>
      <form method="post" action="akcja.php" class="inline">
        <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
        <button type="submit" class="akcja-<?= h($akcja) ?>" name="akcja" value="<?= h($akcja) ?>"><?= h(ETYKIETY_AKCJI[$akcja]) ?></button>
      </form>
<?php endforeach; ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>
<?php stopka();

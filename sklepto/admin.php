<?php
require_once __DIR__ . '/lib.php';
// Uproszczony panel administratora (celowo bez uwierzytelniania – aplikacja szkoleniowa).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['odblokuj'])) {
    $st = db()->prepare("UPDATE users SET status = 'ACTIVE', failed_attempts = 0 WHERE login = ?");
    $st->execute([$_POST['odblokuj']]);
    flash('success', 'Odblokowano konto ' . $_POST['odblokuj'] . '.');
    redirect('admin.php');
}
$users = db()->query('SELECT login, email, wiek, failed_attempts, status FROM users ORDER BY id')->fetchAll();
naglowek('SklepTO – administracja');
?>
<h2>Użytkownicy</h2>
<table id="uzytkownicy">
  <tr><th>Login</th><th>E-mail</th><th>Wiek</th><th>Nieudane próby</th><th>Status</th><th>Akcja</th></tr>
<?php foreach ($users as $u): ?>
  <tr data-login="<?= h($u['login']) ?>">
    <td class="login"><?= h($u['login']) ?></td>
    <td class="email"><?= h($u['email']) ?></td>
    <td class="wiek"><?= h($u['wiek']) ?></td>
    <td class="proby"><?= h($u['failed_attempts']) ?></td>
    <td class="status"><?= h($u['status']) ?></td>
    <td><?php if ($u['status'] === 'LOCKED'): ?>
      <form method="post" class="inline"><button type="submit" class="odblokuj" name="odblokuj"
        value="<?= h($u['login']) ?>">Odblokuj</button></form>
    <?php endif; ?></td>
  </tr>
<?php endforeach; ?>
</table>
<p><a id="reset-bazy" href="install.php?reset=1">Przywróć dane początkowe</a></p>
<?php stopka();

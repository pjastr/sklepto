<?php
require_once __DIR__ . '/lib.php';

/**
 * Wymagania (maszyna stanów konta):
 *  - poprawne hasło dla konta ACTIVE -> zalogowanie, licznik nieudanych prób = 0
 *  - błędne hasło dla konta ACTIVE -> licznik + 1; po 3. kolejnej nieudanej próbie konto LOCKED
 *  - konto LOCKED -> logowanie niemożliwe (nawet poprawnym hasłem)
 *  - administrator może odblokować konto (admin.php) -> ACTIVE, licznik = 0
 */
const MAX_PROB = 3;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';
    $st = db()->prepare('SELECT * FROM users WHERE login = ?');
    $st->execute([$login]);
    $u = $st->fetch();
    if (!$u) {
        flash('error', 'Błędny login lub hasło.');
        redirect('logowanie.php');
    }
    if ($u['status'] === 'LOCKED') {
        flash('error', 'Konto jest zablokowane. Skontaktuj się z administratorem.');
        redirect('logowanie.php');
    }
    if (password_verify($haslo, $u['password_hash'])) {
        if (!BUGI) {
            db()->prepare('UPDATE users SET failed_attempts = 0 WHERE id = ?')->execute([$u['id']]);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        flash('success', 'Zalogowano jako ' . $u['login'] . '.');
        redirect('index.php');
    }
    $proby = (int)$u['failed_attempts'] + 1;
    if ($proby >= MAX_PROB) {
        db()->prepare("UPDATE users SET failed_attempts = ?, status = 'LOCKED' WHERE id = ?")
            ->execute([$proby, $u['id']]);
        flash('error', 'Konto zostało zablokowane po 3 nieudanych próbach.');
    } else {
        db()->prepare('UPDATE users SET failed_attempts = ? WHERE id = ?')->execute([$proby, $u['id']]);
        flash('error', 'Błędny login lub hasło. Pozostało prób: ' . (MAX_PROB - $proby) . '.');
    }
    redirect('logowanie.php');
}

naglowek('SklepTO – logowanie');
?>
<h2>Logowanie</h2>
<form id="form-logowanie" method="post" action="logowanie.php">
  <label for="login">Login</label>
  <input type="text" id="login" name="login">
  <label for="haslo">Hasło</label>
  <input type="password" id="haslo" name="haslo">
  <button type="submit" id="zaloguj">Zaloguj</button>
</form>
<p class="podpowiedz">Konta testowe: <code>jan / Haslo123</code>, <code>anna / Tajne456</code>.</p>
<?php stopka();

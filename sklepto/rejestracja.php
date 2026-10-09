<?php
require_once __DIR__ . '/lib.php';

/**
 * Specyfikacja walidacji (wymagania):
 *  login  – 3..20 znaków, tylko małe litery a-z, cyfry i znak _, unikalny
 *  email  – poprawny adres e-mail
 *  wiek   – liczba całkowita z zakresu 18..99
 *  haslo  – 8..20 znaków, co najmniej jedna litera i jedna cyfra
 *  haslo2 – identyczne z haslo
 *  regulamin – musi być zaznaczony
 */
function waliduj_rejestracje(array $d): array
{
    $bledy = [];
    $login = $d['login'] ?? '';
    $wzorzec = BUGI ? '/^[a-z0-9_]{3,20}$/i' : '/^[a-z0-9_]{3,20}$/';
    if (!preg_match($wzorzec, $login)) {
        $bledy['login'] = 'Login musi mieć od 3 do 20 znaków (małe litery, cyfry, _).';
    } else {
        $st = db()->prepare('SELECT COUNT(*) FROM users WHERE login = ?');
        $st->execute([$login]);
        if ((int)$st->fetchColumn() > 0) {
            $bledy['login'] = 'Login jest już zajęty.';
        }
    }
    if (!filter_var($d['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $bledy['email'] = 'Niepoprawny adres e-mail.';
    }
    $wiek = trim($d['wiek'] ?? '');
    $max = BUGI ? 100 : 99;
    if (!preg_match('/^-?\d+$/', $wiek) || (int)$wiek < 18 || (int)$wiek > $max) {
        $bledy['wiek'] = 'Wiek musi być liczbą całkowitą z zakresu 18–99.';
    }
    $haslo = $d['haslo'] ?? '';
    $dl = function_exists('mb_strlen') ? mb_strlen($haslo, 'UTF-8') : strlen($haslo);
    if ($dl < 8 || $dl > 20 || !preg_match('/\d/', $haslo) || !preg_match('/\pL/u', $haslo)) {
        $bledy['haslo'] = 'Hasło musi mieć od 8 do 20 znaków i zawierać literę oraz cyfrę.';
    }
    if (($d['haslo2'] ?? '') !== $haslo) {
        $bledy['haslo2'] = 'Hasła nie są identyczne.';
    }
    if (empty($d['regulamin'])) {
        $bledy['regulamin'] = 'Musisz zaakceptować regulamin.';
    }
    return $bledy;
}

$bledy = [];
$sukces = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bledy = waliduj_rejestracje($_POST);
    if (!$bledy) {
        $st = db()->prepare('INSERT INTO users (login, email, wiek, password_hash) VALUES (?, ?, ?, ?)');
        $st->execute([$_POST['login'], $_POST['email'], (int)$_POST['wiek'],
            password_hash($_POST['haslo'], PASSWORD_DEFAULT)]);
        $sukces = true;
    }
}
$v = fn($k) => h($_POST[$k] ?? '');

naglowek('SklepTO – rejestracja');
?>
<h2>Rejestracja konta</h2>
<?php if ($sukces): ?>
<div id="sukces">Konto zostało utworzone. Możesz się zalogować.</div>
<?php endif; ?>
<?php if ($bledy): ?>
<ul id="bledy">
<?php foreach ($bledy as $pole => $tekst): ?>
  <li class="blad" data-pole="<?= h($pole) ?>"><?= h($tekst) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<form id="form-rejestracja" method="post" action="rejestracja.php" novalidate>
  <label for="login">Login</label>
  <input type="text" id="login" name="login" value="<?= $sukces ? '' : $v('login') ?>">
  <label for="email">E-mail</label>
  <input type="email" id="email" name="email" value="<?= $sukces ? '' : $v('email') ?>">
  <label for="wiek">Wiek</label>
  <input type="number" id="wiek" name="wiek" value="<?= $sukces ? '' : $v('wiek') ?>">
  <label for="haslo">Hasło</label>
  <input type="password" id="haslo" name="haslo">
  <label for="haslo2">Powtórz hasło</label>
  <input type="password" id="haslo2" name="haslo2">
  <label for="regulamin"><input type="checkbox" id="regulamin" name="regulamin" value="1"> Akceptuję regulamin</label>
  <button type="submit" id="zarejestruj">Zarejestruj</button>
</form>
<?php stopka();

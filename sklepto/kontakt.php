<?php
require_once __DIR__ . '/lib.php';
$wyslano = false;
$bledy = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tresc = trim($_POST['wiadomosc'] ?? '');
    if ($imie === '') {
        $bledy[] = 'Podaj imię.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $bledy[] = 'Niepoprawny adres e-mail.';
    }
    if ($tresc === '') {
        $bledy[] = 'Wpisz treść wiadomości.';
    }
    if (!$bledy) {
        db()->prepare('INSERT INTO messages (imie, email, tresc) VALUES (?, ?, ?)')->execute([$imie, $email, $tresc]);
        $wyslano = true;
    }
}
// Strona zawiera celowe naruszenia listy kontrolnej (gdy BUGI = true).
naglowek(BUGI ? '' : 'SklepTO – kontakt');
?>
<h2>Kontakt</h2>
<?php if ($wyslano): ?><div id="sukces">Dziękujemy za wiadomość.</div><?php endif; ?>
<?php if ($bledy): ?>
<ul id="bledy"><?php foreach ($bledy as $b): ?><li class="blad"><?= h($b) ?></li><?php endforeach; ?></ul>
<?php endif; ?>
<?php if (BUGI): ?>
<img src="logo.svg" width="60" height="60">
<?php else: ?>
<img src="logo.svg" width="60" height="60" alt="Logo SklepTO">
<?php endif; ?>
<form id="form-kontakt" method="post" action="kontakt.php" novalidate>
  <label for="imie">Imię</label>
  <input type="text" id="imie" name="imie" maxlength="60">
<?php if (BUGI): ?>
  <input type="text" id="email" name="email" placeholder="E-mail">
<?php else: ?>
  <label for="email">E-mail</label>
  <input type="email" id="email" name="email" maxlength="120">
<?php endif; ?>
  <label for="wiadomosc">Wiadomość</label>
  <textarea id="wiadomosc" name="wiadomosc" rows="5"></textarea>
  <button type="submit" id="wyslij">Wyślij</button>
</form>
<?php stopka();

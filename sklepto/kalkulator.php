<?php
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/kalkulator_logika.php';

$wynik = null;
$bledy = [];
$wartoscTxt = $_GET['wartosc'] ?? '';
$karta = !empty($_GET['karta']);
$kod = $_GET['kod'] ?? '';
$dostawa = $_GET['dostawa'] ?? 'kurier';
if (isset($_GET['oblicz'])) {
    $w = parsuj_wartosc($wartoscTxt);
    if ($w === null) {
        $bledy[] = 'Wartość zamówienia musi być liczbą z zakresu 0,01–10000,00.';
    }
    if (!array_key_exists($dostawa, KOSZTY_DOSTAWY)) {
        $bledy[] = 'Wybierz sposób dostawy.';
    }
    if (!$bledy) {
        $wynik = oblicz_zamowienie($w, $karta, $kod, $dostawa);
    }
}
$zl = fn(float $x) => number_format($x, 2, '.', '') . ' zł';
$chk = fn(string $v) => $dostawa === $v ? 'checked' : '';
naglowek('SklepTO – kalkulator zamówienia');
?>
<h2>Kalkulator zamówienia</h2>
<?php if ($bledy): ?>
<ul id="bledy">
<?php foreach ($bledy as $b): ?>
  <li class="blad"><?= h($b) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<form id="form-kalkulator" method="get" action="kalkulator.php">
  <label for="wartosc">Wartość koszyka (zł)</label>
  <input type="text" id="wartosc" name="wartosc" value="<?= h($wartoscTxt) ?>">
  <label for="karta"><input type="checkbox" id="karta" name="karta" value="1" <?= $karta ? 'checked' : '' ?>> Mam kartę stałego klienta</label>
  <label for="kod">Kod rabatowy</label>
  <input type="text" id="kod" name="kod" value="<?= h($kod) ?>">
  <fieldset id="dostawa">
    <legend>Sposób dostawy</legend>
    <label for="dostawa-kurier"><input type="radio" id="dostawa-kurier" name="dostawa" value="kurier" <?= $chk('kurier') ?>> Kurier (15,00 zł)</label>
    <label for="dostawa-paczkomat"><input type="radio" id="dostawa-paczkomat" name="dostawa" value="paczkomat" <?= $chk('paczkomat') ?>> Paczkomat (10,00 zł)</label>
    <label for="dostawa-odbior"><input type="radio" id="dostawa-odbior" name="dostawa" value="odbior" <?= $chk('odbior') ?>> Odbiór osobisty (0,00 zł)</label>
  </fieldset>
  <button type="submit" id="oblicz" name="oblicz" value="1">Oblicz</button>
</form>
<?php if ($wynik): ?>
<div id="wynik">
  <p>Rabat: <span id="rabat"><?= $wynik['rabat'] ?>%</span></p>
  <p>Koszt dostawy: <span id="koszt-dostawy"><?= $zl($wynik['koszt_dostawy']) ?></span></p>
  <p>Do zapłaty: <strong id="do-zaplaty"><?= $zl($wynik['do_zaplaty']) ?></strong></p>
<?php if (trim($kod) !== '' && !$wynik['kod_poprawny']): ?>
  <p id="kod-info">Nieprawidłowy kod rabatowy – został pominięty.</p>
<?php endif; ?>
</div>
<?php endif; ?>
<?php stopka();

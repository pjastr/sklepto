<?php
require_once __DIR__ . '/lib.php';
naglowek('SklepTO – strona główna');
?>
<h2>Witaj w SklepTO</h2>
<p id="opis">SklepTO to niewielka aplikacja internetowa przygotowana do ćwiczeń z testowania oprogramowania.</p>
<img src="logo.svg" alt="Logo aplikacji SklepTO" width="120" height="120">
<ul id="moduly">
  <li><a href="rejestracja.php">Rejestracja konta</a> – walidacja formularza</li>
  <li><a href="logowanie.php">Logowanie</a> – blokada konta po nieudanych próbach</li>
  <li><a href="kalkulator.php">Kalkulator zamówienia</a> – rabaty i koszt dostawy</li>
  <li><a href="zamowienia.php">Zamówienia</a> – cykl życia zamówienia</li>
  <li><a href="kontakt.php">Kontakt</a> – formularz kontaktowy</li>
</ul>
<?php stopka();

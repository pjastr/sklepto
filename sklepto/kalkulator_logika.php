<?php
/**
 * Reguły biznesowe kalkulatora zamówienia (wymaganie):
 *  R-1 Rabat: karta stałego klienta ORAZ poprawny kod "STUDENT10" -> 15%,
 *      tylko jedno z nich -> 10%, żadne -> 0%. Rabaty się nie sumują.
 *  R-2 Darmowa dostawa, gdy klient ma kartę LUB wartość koszyka (przed rabatem) >= 200,00 zł.
 *  R-3 Koszt płatnej dostawy: kurier 15,00 zł, paczkomat 10,00 zł, odbiór osobisty 0,00 zł.
 *  R-4 Wartość koszyka: liczba z zakresu 0,01 – 10 000,00 zł (dopuszczalny przecinek lub kropka).
 *  Do zapłaty = wartość * (100 - rabat) / 100 (zaokrąglone do grosza) + koszt dostawy.
 */
const KOD_RABATOWY = 'STUDENT10';
const KOSZTY_DOSTAWY = ['kurier' => 15.00, 'paczkomat' => 10.00, 'odbior' => 0.00];

function parsuj_wartosc(string $tekst): ?float
{
    $tekst = str_replace(',', '.', trim($tekst));
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $tekst)) {
        return null;
    }
    $w = (float)$tekst;
    if ($w < 0.01 || $w > 10000.00) {
        return null;
    }
    return $w;
}

function oblicz_zamowienie(float $wartosc, bool $karta, string $kod, string $dostawa): array
{
    $kodOk = strtoupper(trim($kod)) === KOD_RABATOWY;
    if ($karta && $kodOk) {
        $rabat = BUGI ? 20 : 15;
    } elseif ($karta || $kodOk) {
        $rabat = 10;
    } else {
        $rabat = 0;
    }
    $darmowa = $karta || $wartosc >= 200.00;
    $kosztDostawy = $darmowa ? 0.00 : (KOSZTY_DOSTAWY[$dostawa] ?? KOSZTY_DOSTAWY['kurier']);
    $poRabacie = round($wartosc * (100 - $rabat) / 100, 2);
    return [
        'rabat' => $rabat,
        'kod_poprawny' => $kodOk,
        'darmowa_dostawa' => $darmowa,
        'koszt_dostawy' => $kosztDostawy,
        'do_zaplaty' => round($poRabacie + $kosztDostawy, 2),
    ];
}

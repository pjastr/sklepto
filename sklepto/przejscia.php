<?php
/**
 * Maszyna stanów zamówienia (wymaganie):
 *   NEW      --oplac-->    PAID
 *   NEW      --anuluj-->   CANCELLED
 *   PAID     --wyslij-->   SHIPPED
 *   PAID     --anuluj-->   CANCELLED
 *   SHIPPED  --dostarcz--> DELIVERED
 *   DELIVERED, CANCELLED – stany końcowe
 * Każde inne przejście jest niedozwolone.
 */
const ETYKIETY_AKCJI = [
    'oplac' => 'Opłać',
    'wyslij' => 'Wyślij',
    'dostarcz' => 'Potwierdź dostawę',
    'anuluj' => 'Anuluj',
];

function tablica_przejsc(): array
{
    $t = [
        'NEW' => ['oplac' => 'PAID', 'anuluj' => 'CANCELLED'],
        'PAID' => ['wyslij' => 'SHIPPED', 'anuluj' => 'CANCELLED'],
        'SHIPPED' => ['dostarcz' => 'DELIVERED'],
        'DELIVERED' => [],
        'CANCELLED' => [],
    ];
    if (BUGI) {
        $t['SHIPPED']['anuluj'] = 'CANCELLED';
    }
    return $t;
}

function dozwolone_akcje(string $status): array
{
    return tablica_przejsc()[$status] ?? [];
}

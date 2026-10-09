# SklepTO – instalacja w XAMPP i opis aplikacji

SklepTO to niewielka aplikacja internetowa (PHP 8 + MySQL/MariaDB) przygotowana do ćwiczeń
z testowania oprogramowania. Działa lokalnie w pakiecie XAMPP – **bez uprawnień administratora**.

## 1. Wymagania

| Składnik | Wersja |
|---|---|
| System | Windows 10/11, konto standardowe |
| XAMPP | 8.2.12 portable (Apache 2.4.58, MariaDB 10.4.32, PHP 8.2.12, phpMyAdmin 5.2.1) |
| Przeglądarka | Chrome, Edge lub Firefox |
| Miejsce na dysku | ok. 1 GB |

XAMPP 8.2.12 (listopad 2023) jest najnowszym wydaniem XAMPP dla Windows. Strona
apachefriends.org oferuje tylko instalatory; wersje **portable** (ZIP/7z) znajdują się w archiwum
projektu na SourceForge: `https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/`.

## 2. Instalacja XAMPP portable (bez admina)

1. Pobierz `xampp-portable-windows-x64-8.2.12-0-VS16.zip` (ok. 226 MB) – albo ten sam pakiet
   w formacie `.7z` (ok. 132 MB; Windows 11 rozpakowuje `.7z` natywnie).
2. Prawy klik na archiwum ▸ **Wyodrębnij wszystkie…** ▸ folder docelowy `%USERPROFILE%`.
   Wynik: `C:\Users\<login>\xampp\xampp-control.exe`. Jeśli powstał folder `xampp\xampp`,
   przenieś wewnętrzny folder poziom wyżej. **Nie** rozpakowuj do `C:\` ani `C:\Program Files`.
3. Uruchom `C:\Users\<login>\xampp\setup_xampp.bat` – skrypt dopasowuje ścieżki w plikach
   konfiguracyjnych do nowej lokalizacji (jeśli zada pytanie, wybierz odświeżenie ścieżek).
   Skrypt uruchom ponownie po każdym przeniesieniu folderu `xampp`.
4. Uruchom `xampp-control.exe` (SmartScreen: *Więcej informacji ▸ Uruchom mimo to*;
   przy pierwszym starcie wybierz język). Komunikat o braku uprawnień administratora zignoruj
   i nie zaznaczaj pól w kolumnie *Service*.
5. **Start** przy Apache i MySQL. Okna Zapory Windows zamknij przyciskiem **Anuluj** –
   dostęp przez `localhost` działa bez reguły w zaporze.
6. Sprawdź `http://localhost` (strona powitalna XAMPP).

### Konflikty portów

| Problem | Rozwiązanie |
|---|---|
| port 80 zajęty | *Config ▸ Apache (httpd.conf)*: `Listen 8080`, `ServerName localhost:8080`; adres aplikacji `http://localhost:8080/sklepto` |
| port 443 zajęty | *Config ▸ Apache (httpd-ssl.conf)*: `Listen 4433`, `<VirtualHost _default_:4433>` |
| port 3306 zajęty | *Config ▸ my.ini*: `port=3307` w sekcjach `[client]` i `[mysqld]`; w `sklepto\config.php` zmień domyślny port `'3306'` na `'3307'`; w `phpMyAdmin\config.inc.php` dopisz `$cfg['Servers'][$i]['port'] = '3307';` |

## 3. Wdrożenie SklepTO

1. Skopiuj folder `sklepto` do `C:\Users\<login>\xampp\htdocs\`.
2. Otwórz `http://localhost/sklepto/install.php` – strona pokaże `OK` (element `#install-status`)
   i utworzy bazę `sklepto` z danymi startowymi.
3. Przejdź do `http://localhost/sklepto/`.

Reset danych do stanu początkowego: `http://localhost/sklepto/install.php?reset=1`
(albo link „Przywróć dane początkowe” na stronie Administracja). Reset usuwa wszystkie
zarejestrowane konta, zamówienia i wiadomości oraz czyści sesję przeglądarki w aplikacji.

Podgląd bazy: `http://localhost/phpmyadmin` ▸ baza `sklepto`
(tabele `users`, `orders`, `order_history`, `messages`).

Zatrzymanie: w panelu *Stop* przy Apache i MySQL, potem *Quit*.

## 4. Konfiguracja

Plik `config.php` – parametry połączenia z bazą (domyślnie jak w świeżym XAMPP):
host `127.0.0.1`, port `3306`, baza `sklepto`, użytkownik `root`, hasło puste.
Każdy parametr można nadpisać zmienną środowiskową serwera: `SKLEPTO_DB_HOST`,
`SKLEPTO_DB_PORT`, `SKLEPTO_DB_NAME`, `SKLEPTO_DB_USER`, `SKLEPTO_DB_PASS`.

Testy automatyczne odczytują adres aplikacji ze zmiennej `SKLEPTO_URL`
(domyślnie `http://localhost/sklepto`, patrz `wspolne/konfiguracja.py`).

## 5. Dane startowe

| Login | Hasło | E-mail | Wiek | Status |
|---|---|---|---|---|
| `jan` | `Haslo123` | jan@example.com | 30 | ACTIVE |
| `anna` | `Tajne456` | anna@example.com | 25 | ACTIVE |
| `zablokowany` | `Haslo123` | zb@example.com | 40 | LOCKED (3 nieudane próby) |

Zamówienia jana: #1 Nowe 100.00 zł, #2 Opłacone 250.00 zł, #3 Wysłane 80.00 zł,
#4 Dostarczone 40.00 zł, #5 Anulowane 60.00 zł; zamówienie #6 (Nowe, 120.00 zł) należy do anny.

## 6. Moduły aplikacji

Wspólne elementy: menu `nav#menu` (`#nav-home`, `#nav-rejestracja`, `#nav-kalkulator`,
`#nav-zamowienia`, `#nav-kontakt`, `#nav-admin`, `#nav-logowanie` lub `#zalogowany` i `#nav-wyloguj`),
komunikaty po przekierowaniu w `#flash` (klasa `success` lub `error`).

| Strona | Funkcja | Specyfikacja (skrót) | Elementy dla testów |
|---|---|---|---|
| `index.php` | strona główna | opis i lista modułów | `#opis`, `ul#moduly` |
| `rejestracja.php` | zakładanie konta (POST) | login 3–20 znaków `[a-z0-9_]`, unikalny; poprawny e-mail; wiek – liczba całkowita 18–99; hasło 8–20 znaków z literą i cyfrą; hasła identyczne; regulamin zaznaczony | `#login`, `#email`, `#wiek`, `#haslo`, `#haslo2`, `#regulamin`, `#zarejestruj`; błędy `ul#bledy li.blad[data-pole=…]`; sukces `#sukces` |
| `logowanie.php` | logowanie, blokada konta | poprawne hasło – zalogowanie i wyzerowanie licznika; 3 kolejne błędne hasła – konto LOCKED; konto LOCKED nie loguje się | `#login`, `#haslo`, `#zaloguj`, `#flash` |
| `wyloguj.php` | wylogowanie | komunikat „Wylogowano.” | `#flash` |
| `kalkulator.php` | koszt zamówienia (GET) | rabat: karta i kod `STUDENT10` – 15%, jedno z nich – 10%; darmowa dostawa przy karcie lub wartości ≥ 200 zł; kurier 15 zł, paczkomat 10 zł, odbiór 0 zł; wartość 0,01–10 000,00 | `#wartosc`, `#karta`, `#kod`, `#dostawa-*`, `#oblicz`, `#rabat`, `#koszt-dostawy`, `#do-zaplaty`, `#kod-info` |
| `zamowienia.php` | cykl życia zamówienia (po zalogowaniu) | NEW→PAID (Opłać), NEW→CANCELLED, PAID→SHIPPED (Wyślij), PAID→CANCELLED, SHIPPED→DELIVERED; inne przejścia niedozwolone | `#kwota`, `#nowe-zamowienie`, `tr#zam-<id>[data-status]`, `button.akcja-*` |
| `akcja.php` | zmiana statusu | `akcja.php?id=<id>&akcja=<oplac\|wyslij\|dostarcz\|anuluj>` (GET lub POST) | `#flash` |
| `kontakt.php` | formularz kontaktowy | imię, poprawny e-mail i treść wymagane | `#imie`, `#email`, `#wiadomosc`, `#wyslij`, `#sukces` |
| `admin.php` | lista kont, odblokowanie | bez logowania (aplikacja szkoleniowa) | `table#uzytkownicy tr[data-login]`, `td.status`, `button.odblokuj` |
| `install.php` | instalacja / reset bazy | `?reset=1` – stan początkowy | `#install-status` |

Szczegółowe wymagania są opisane w komentarzach na początku plików PHP
(`rejestracja.php`, `logowanie.php`, `kalkulator_logika.php`, `przejscia.php`).

## 7. Najczęstsze problemy

| Objaw | Przyczyna i rozwiązanie |
|---|---|
| „Brak połączenia z bazą danych” (`#blad-bazy`) | MySQL nie działa – *Start* w panelu XAMPP; zły port – `config.php` |
| `install.php` pokazuje `BŁĄD` | jw.; treść błędu MySQL jest wyświetlona pod komunikatem |
| `http://localhost/sklepto` – 404 | folder nie w `htdocs` lub zagnieżdżony (`htdocs\sklepto\sklepto`) |
| Apache startuje i od razu się zatrzymuje | zajęty port 80/443 – zob. „Konflikty portów” |
| MySQL: *shutdown unexpectedly* | poprzednio nie zatrzymano MySQL; spróbuj ponownie *Start*; w ostateczności świeże rozpakowanie XAMPP |
| Po przeniesieniu folderu XAMPP nic nie działa | uruchom ponownie `setup_xampp.bat` |

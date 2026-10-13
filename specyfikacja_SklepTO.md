# Specyfikacja wymagań – aplikacja SklepTO

| | |
|---|---|
| Dokument | Specyfikacja wymagań funkcjonalnych i ogólnych (podstawa testów) |
| Przedmiot testów | SklepTO 1.0 – aplikacja internetowa PHP 8.2 + MySQL/MariaDB (XAMPP) |
| Adres | `http://localhost/sklepto/` (strona główna `index.php`) |
| Wersja dokumentu | 1.0 (ćwiczenia 1) – obowiązuje na wszystkich kolejnych ćwiczeniach z SklepTO |

Każde wymaganie ma unikalny identyfikator (`REQ-<obszar>-<nr>`), do którego odwołujemy się
w przypadkach testowych, macierzy śledzenia i raportach defektów.
Obszary: **UI** – wymagania ogólne, **REJ** – rejestracja, **LOG** – logowanie i blokada konta,
**KAL** – kalkulator zamówienia, **ZAM** – zamówienia, **KON** – formularz kontaktowy.

Słowo „musi” oznacza wymaganie obowiązkowe. Teksty komunikatów podane w cudzysłowie
„…” są wymagane dosłownie (łącznie z kropką na końcu).

---

## 1. Wymagania ogólne (UI)

Wymagania REQ-UI-01 – REQ-UI-07 dotyczą stron aplikacji dostępnych z menu (Start, Rejestracja,
Logowanie, Kalkulator, Zamówienia, Kontakt, Administracja); nie dotyczą technicznej strony
instalacji `install.php`.

| ID | Wymaganie |
|---|---|
| REQ-UI-01 | Każda strona zawiera menu z odnośnikami: Start, Rejestracja, Kalkulator, Zamówienia, Kontakt, Administracja oraz – dla użytkownika niezalogowanego – Logowanie. Dla zalogowanego zamiast „Logowanie” wyświetlany jest napis „Zalogowano: &lt;login&gt;” i odnośnik „Wyloguj”. |
| REQ-UI-02 | Każda strona ma niepusty, opisowy tytuł (element `<title>`) w postaci „SklepTO – &lt;nazwa strony&gt;”, np. „SklepTO – rejestracja”. |
| REQ-UI-03 | Każdy obrazek ma niepusty tekst alternatywny (atrybut `alt`). |
| REQ-UI-04 | Każde widoczne pole formularza ma etykietę powiązaną z polem (`<label for="…">`). |
| REQ-UI-05 | Pole adresu e-mail w każdym formularzu ma typ `email`. |
| REQ-UI-06 | Komunikaty o wyniku operacji zakończonej przekierowaniem (logowanie, wylogowanie, operacje na zamówieniach, odblokowanie konta) są wyświetlane w wyróżnionym bloku komunikatu: zielonym (powodzenie) lub czerwonym (błąd). Komunikat znika po odświeżeniu strony. |
| REQ-UI-07 | Aplikacja działa w języku polskim; polskie znaki są wyświetlane poprawnie (UTF-8). Stopka każdej strony zawiera numer wersji aplikacji. |
| REQ-UI-08 | Wszystkie dane wejściowe są sprawdzane po stronie serwera (niezależnie od walidacji w przeglądarce). |
| REQ-UI-09 | Otwarcie adresu `install.php?reset=1` (lub odnośnika „Przywróć dane początkowe” w panelu administracyjnym) przywraca dane początkowe (Załącznik A) i wylogowuje użytkownika. Strona instalacji wyświetla status „OK”. |

## 2. Rejestracja konta (REJ) – strona „Rejestracja”

| ID | Wymaganie |
|---|---|
| REQ-REJ-01 | Formularz rejestracji zawiera pola: Login, E-mail, Wiek, Hasło, Powtórz hasło, pole wyboru „Akceptuję regulamin” oraz przycisk „Zarejestruj”. |
| REQ-REJ-02 | Login musi mieć od 3 do 20 znaków i składać się wyłącznie z małych liter łacińskich a–z, cyfr 0–9 i znaku podkreślenia `_`. W przeciwnym razie komunikat: „Login musi mieć od 3 do 20 znaków (małe litery, cyfry, _).” |
| REQ-REJ-03 | Login musi być unikalny. Jeśli konto o takim loginie istnieje, komunikat: „Login jest już zajęty.” |
| REQ-REJ-04 | E-mail musi być poprawnym adresem e-mail. W przeciwnym razie komunikat: „Niepoprawny adres e-mail.” |
| REQ-REJ-05 | Wiek musi być liczbą całkowitą z zakresu 18–99 (włącznie). W przeciwnym razie komunikat: „Wiek musi być liczbą całkowitą z zakresu 18–99.” |
| REQ-REJ-06 | Hasło musi mieć od 8 do 20 znaków (włącznie) i zawierać co najmniej jedną literę oraz co najmniej jedną cyfrę. W przeciwnym razie komunikat: „Hasło musi mieć od 8 do 20 znaków i zawierać literę oraz cyfrę.” |
| REQ-REJ-07 | Pole „Powtórz hasło” musi być identyczne z polem „Hasło”. W przeciwnym razie komunikat: „Hasła nie są identyczne.” |
| REQ-REJ-08 | Regulamin musi zostać zaakceptowany. W przeciwnym razie komunikat: „Musisz zaakceptować regulamin.” |
| REQ-REJ-09 | Po wysłaniu formularza z błędami wyświetlana jest lista **wszystkich** błędów (po jednym komunikacie dla każdego błędnego pola), konto nie jest tworzone, a pola Login, E-mail i Wiek zachowują wpisane wartości. |
| REQ-REJ-10 | Po wysłaniu poprawnego formularza tworzone jest aktywne konto (status ACTIVE, 0 nieudanych prób logowania), wyświetlany jest komunikat „Konto zostało utworzone. Możesz się zalogować.”, a formularz jest czyszczony. Nowy użytkownik może się od razu zalogować. |

## 3. Logowanie i blokada konta (LOG) – strony „Logowanie” i „Administracja”

| ID | Wymaganie |
|---|---|
| REQ-LOG-01 | Formularz logowania zawiera pola Login i Hasło oraz przycisk „Zaloguj”. |
| REQ-LOG-02 | Poprawny login i hasło dla konta aktywnego (ACTIVE) powodują zalogowanie, przejście na stronę główną i komunikat „Zalogowano jako &lt;login&gt;.” Licznik nieudanych prób logowania tego konta jest zerowany. |
| REQ-LOG-03 | Błędne hasło dla konta aktywnego zwiększa licznik nieudanych prób o 1 i wyświetla komunikat „Błędny login lub hasło. Pozostało prób: N.”, gdzie N = 3 − liczba kolejnych nieudanych prób. |
| REQ-LOG-04 | Trzecia kolejna nieudana próba (bez poprawnego logowania pomiędzy nimi) blokuje konto (status LOCKED) i wyświetla komunikat „Konto zostało zablokowane po 3 nieudanych próbach.” |
| REQ-LOG-05 | Na konto zablokowane nie można się zalogować, także poprawnym hasłem. Wyświetlany jest komunikat „Konto jest zablokowane. Skontaktuj się z administratorem.” |
| REQ-LOG-06 | Próba logowania na nieistniejący login wyświetla komunikat „Błędny login lub hasło.” (bez informacji o liczbie prób). |
| REQ-LOG-07 | Odnośnik „Wyloguj” kończy sesję, przenosi na stronę główną i wyświetla komunikat „Wylogowano.” |
| REQ-LOG-08 | Panel „Administracja” wyświetla tabelę użytkowników z kolumnami: Login, E-mail, Wiek, Nieudane próby, Status (ACTIVE lub LOCKED). (Uproszczenie aplikacji szkoleniowej: panel nie wymaga logowania.) |
| REQ-LOG-09 | Dla konta zablokowanego (i tylko dla niego) panel wyświetla przycisk „Odblokuj”. Jego użycie zmienia status na ACTIVE, zeruje licznik nieudanych prób i wyświetla komunikat „Odblokowano konto &lt;login&gt;.” |

## 4. Kalkulator zamówienia (KAL) – strona „Kalkulator”

| ID | Wymaganie |
|---|---|
| REQ-KAL-01 | Formularz zawiera: pole „Wartość koszyka (zł)”, pole wyboru „Mam kartę stałego klienta”, pole „Kod rabatowy”, wybór sposobu dostawy (Kurier – domyślnie, Paczkomat, Odbiór osobisty) oraz przycisk „Oblicz”. Po obliczeniu wprowadzone dane pozostają w formularzu. |
| REQ-KAL-02 | Wartość koszyka to liczba z zakresu 0,01–10000,00 zł z co najwyżej dwoma miejscami po przecinku; separatorem dziesiętnym może być przecinek lub kropka. Dla innej wartości wyświetlany jest komunikat „Wartość zamówienia musi być liczbą z zakresu 0,01–10000,00.” i wynik nie jest obliczany. |
| REQ-KAL-03 | Rabat: 15%, gdy klient ma kartę stałego klienta **i** podał poprawny kod rabatowy; 10%, gdy spełniony jest tylko jeden z tych warunków; 0%, gdy żaden. Rabaty nie sumują się. |
| REQ-KAL-04 | Poprawny kod rabatowy to „STUDENT10”; wielkość liter nie ma znaczenia, a spacje na początku i końcu są pomijane. Gdy podano niepusty, niepoprawny kod, wyświetlany jest komunikat „Nieprawidłowy kod rabatowy – został pominięty.”, a rabat liczony jest tak, jakby kodu nie podano. |
| REQ-KAL-05 | Dostawa jest bezpłatna, gdy klient ma kartę stałego klienta **lub** wartość koszyka (przed rabatem) wynosi co najmniej 200,00 zł. |
| REQ-KAL-06 | Koszt płatnej dostawy: kurier 15,00 zł, paczkomat 10,00 zł, odbiór osobisty 0,00 zł. |
| REQ-KAL-07 | Kwota do zapłaty = wartość koszyka × (100 − rabat) / 100 (zaokrąglona do pełnego grosza) + koszt dostawy. |
| REQ-KAL-08 | Wynik zawiera: rabat w procentach (np. „15%”), koszt dostawy i kwotę do zapłaty w formacie „127.50 zł” (dwa miejsca po kropce). Przykład: 150,00 zł, karta, kod „student10”, kurier → rabat 15%, dostawa 0.00 zł, do zapłaty 127.50 zł. |

## 5. Zamówienia (ZAM) – strona „Zamówienia”

| ID | Wymaganie |
|---|---|
| REQ-ZAM-01 | Strona jest dostępna tylko dla zalogowanych. Niezalogowany użytkownik jest przenoszony na stronę logowania z komunikatem „Zaloguj się, aby zobaczyć zamówienia.” |
| REQ-ZAM-02 | Strona wyświetla tabelę **własnych** zamówień użytkownika z kolumnami: Nr, Kwota (np. „100.00 zł”), Status (Nowe, Opłacone, Wysłane, Dostarczone, Anulowane), Akcje. |
| REQ-ZAM-03 | Użytkownik może utworzyć nowe zamówienie, podając dodatnią kwotę (przecinek lub kropka); zamówienie otrzymuje status „Nowe” i wyświetlany jest komunikat „Utworzono zamówienie #&lt;nr&gt;.” Dla kwoty niedodatniej lub nieliczbowej: „Kwota zamówienia musi być dodatnią liczbą.” |
| REQ-ZAM-04 | Dozwolone są wyłącznie przejścia: Nowe –opłać→ Opłacone; Nowe –anuluj→ Anulowane; Opłacone –wyślij→ Wysłane; Opłacone –anuluj→ Anulowane; Wysłane –potwierdź dostawę→ Dostarczone. Stany Dostarczone i Anulowane są końcowe. |
| REQ-ZAM-05 | Przy każdym zamówieniu wyświetlane są przyciski wyłącznie tych akcji, które są dozwolone w jego bieżącym stanie („Opłać”, „Wyślij”, „Potwierdź dostawę”, „Anuluj”). Zamówienia w stanie końcowym nie mają przycisków. |
| REQ-ZAM-06 | Po wykonaniu dozwolonej akcji status zmienia się zgodnie z REQ-ZAM-04 i wyświetlany jest komunikat „Zamówienie #&lt;nr&gt;: &lt;stary status&gt; → &lt;nowy status&gt;.”, np. „Zamówienie #1: Nowe → Opłacone.” Każda zmiana jest zapisywana w historii zamówienia. |
| REQ-ZAM-07 | Próba wykonania niedozwolonej akcji (np. przez bezpośrednie wywołanie adresu `akcja.php?id=4&akcja=anuluj`) nie zmienia statusu i wyświetla komunikat „Niedozwolone przejście: &lt;status&gt; → &lt;akcja&gt;.”, np. „Niedozwolone przejście: Dostarczone → anuluj.” |
| REQ-ZAM-08 | Próba wykonania akcji na zamówieniu nieistniejącym lub należącym do innego użytkownika wyświetla komunikat „Nie znaleziono zamówienia.” i niczego nie zmienia. |

## 6. Formularz kontaktowy (KON) – strona „Kontakt”

| ID | Wymaganie |
|---|---|
| REQ-KON-01 | Formularz zawiera pola: Imię (maks. 60 znaków), E-mail (maks. 120 znaków), Wiadomość oraz przycisk „Wyślij”. Strona spełnia wymagania REQ-UI-02 – REQ-UI-05. |
| REQ-KON-02 | Imię nie może być puste – w przeciwnym razie komunikat „Podaj imię.” |
| REQ-KON-03 | E-mail musi być poprawnym adresem – w przeciwnym razie komunikat „Niepoprawny adres e-mail.” |
| REQ-KON-04 | Wiadomość nie może być pusta – w przeciwnym razie komunikat „Wpisz treść wiadomości.” |
| REQ-KON-05 | Po wysłaniu poprawnego formularza wiadomość jest zapisywana i wyświetlany jest komunikat „Dziękujemy za wiadomość.” |

---

## Załącznik A. Dane początkowe (po `install.php?reset=1`)

| Login | Hasło | E-mail | Wiek | Status | Nieudane próby |
|---|---|---|---|---|---|
| jan | Haslo123 | jan@example.com | 30 | ACTIVE | 0 |
| anna | Tajne456 | anna@example.com | 25 | ACTIVE | 0 |
| zablokowany | Haslo123 | zb@example.com | 40 | LOCKED | 3 |

| Nr | Właściciel | Kwota | Status |
|---|---|---|---|
| 1 | jan | 100.00 zł | Nowe |
| 2 | jan | 250.00 zł | Opłacone |
| 3 | jan | 80.00 zł | Wysłane |
| 4 | jan | 40.00 zł | Dostarczone |
| 5 | jan | 60.00 zł | Anulowane |
| 6 | anna | 120.00 zł | Nowe |

## Załącznik B. Słownik

- **Konto aktywne / zablokowane** – status konta ACTIVE / LOCKED widoczny w panelu administracyjnym.
- **Kolejne nieudane próby** – nieudane próby logowania na to samo konto, między którymi nie było poprawnego logowania ani odblokowania konta.
- **Wartość koszyka** – kwota wpisana w kalkulatorze przed naliczeniem rabatu i kosztu dostawy.

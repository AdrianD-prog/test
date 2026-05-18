# AMAZONIX — Sklep internetowy

## 1) O projekcie

Projekt **Amazonix** to sklep internetowy podobny do np. Amazona, Allegro. Zawiera:
- własny **Router** (mapowanie URL → kontroler/metoda),
- prostą strukturę **MVC** (Controller + View + szablony),
- moduł **logowania / rejestracji / wylogowania** oparty o sesje,
- konfigurację przez plik **`.env`** (z użyciem `vlucas/phpdotenv`),

---

## 2) Wymagania

### Oprogramowanie
- **PHP 8.4** (zalecane zgodnie z konfiguracją projektu)
- **Apache** z modułem `mod_rewrite` (XAMPP/WAMP/Laragon)
- **MySQL/MariaDB**

---

## 3) Instalacja i uruchomienie (Windows + XAMPP)

### Krok 1 — pobranie projektu
Umieść projekt w katalogu serwera, np.:
- `C:\xampp\htdocs\amazonix` - Windows
- `/var/www/html/amazonix` - Linux

### Krok 2 — composer
W katalogu projektu uruchom:
- php composer.phar install

### Krok 3 — baza danych
1. Utwórz bazę danych.
2. Zaimportuj baze danych `amazonix.sql`.

### Krok 4 — konfiguracja `.env`
Utwórz plik `.env` w katalogu głównym projektu (jeśli go nie ma) i ustaw zmienne:
- ROOT_DIR
- DB_HOST
- DB_NAME
- DB_USER
- DB_PASS

### Krok 5 — uruchom Apache i wejdź w aplikację
- Włącz Apache + MySQL w XAMPP.
- Otwórz w przeglądarce stronę.

---

## 4) Architektura projektu (jak to działa)

### Punkt wejścia aplikacji
- `public/index.php`
    - uruchamia sesję,
    - ładuje autoloader Composera,
    - inicjalizuje aplikację,
    - pobiera aktualny URL i przekazuje go do routera.

### Bootstrap aplikacji
- `bootstrap/app.php`
    - ładuje `.env` przez Dotenv,
    - tworzy obiekty usług (Router, View, Auth, PDO),
    - ładuje definicje tras.

### Routing

#### Gdzie definiuje się trasy?
- Plik: `config/routes.php`

Trasy są zgrupowane pod prefiksem `ROOT_DIR` (z `.env`), czyli w praktyce wszystkie adresy aplikacji startują od np.:
- `/amazonix`

To pozwala uruchamiać aplikację w podkatalogu serwera (typowe w XAMPP `htdocs`).

> Jeśli nie ma dopasowania do żadnej trasy, aplikacja zwraca `404 Not Found`.


### Widoki i szablony
- `src/View/View.php` — renderowanie plików `.php` z `templates/`
- `templates/layout/` — wspólne elementy (nagłówek/stopka)
- `templates/views/` — konkretne strony (home/login/register/404)

---

## 5) Struktura katalogów (co gdzie jest)
- `bootstrap/` - start i konfiguracja aplikacji
- `config/` - trasy i konfiguracja bazy
- `public/` - katalog publiczny (index.php, assets, .htaccess)
- `src/` - logika aplikacji (MVC + serwisy)
- `templates/` - szablony widoków
- `vendor/` - biblioteki z Composera
- `.env` - konfiguracja środowiska
- `composer.json` - zależności i autoload
- `README.md` - dokumentacja projektu

## 10) Technologie użyte w projekcie

- PHP 8.4
- Apache
- MySQL/MariaDB
- Composer (autoload PSR-4)
- vlucas/phpdotenv (konfiguracja `.env`)
- Bootstrap (frontend)

## 12) Autor i informacje dodatkowe
- Kacper Nowak
- Adrian Dulak
- Wiktor Kozioł

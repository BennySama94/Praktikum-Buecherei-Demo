# Praktikum-Bücherei-Demo

Eine vollständige Bibliotheksverwaltung als Demo-Projekt.  
Nutzer können Bücher ausleihen und zurückgeben; Bibliothekare verwalten den Bestand.

---

## Tech-Stack

| Bereich    | Technologie                            |
|------------|----------------------------------------|
| Backend    | PHP 8.5.0, Laravel 12, Sanctum, SQLite |
| Frontend   | SvelteKit, Tailwind CSS, TypeScript    |
| Tests      | Pest (Feature-Tests)                   |
| Laufzeit   | Node.js 22.17                          |

---

## Schnellstart (Windows)

### Voraussetzungen
- PHP 8.5.0 + Composer
- Node.js 22.17 + npm

### Setup

```powershell
# Abhängigkeiten installieren & Entwicklungsserver starten
.\Startup.ps1
```

Das Skript führt folgende Schritte interaktiv durch:

1. **`composer install`** - Fragt, ob PHP-Abhängigkeiten installiert werden sollen.
2. **`.env`-Datei erstellen** - Kopiert `.env.example` nach `.env` und führt `php artisan key:generate` aus (wird übersprungen, wenn `.env` bereits existiert).
3. **`npm install`** - Fragt, ob Node-Abhängigkeiten installiert werden sollen.
4. **Server starten** - Öffnet zwei separate `cmd`-Fenster: eines für `php artisan serve`, eines für `npm run dev`.

Die App läuft dann unter:
- **Backend:** `http://localhost:8000`
- **Frontend:** `http://localhost:5173`

> **Hinweis:** Datenbank-Migration und Seeding müssen einmalig manuell ausgeführt werden:
> ```bash
> cd Backend
> php artisan migrate --seed
> ```

---

## Architektur-Entscheidungen

- **Form Requests** übernehmen die Validierung - Controller sind reine Routing-Handler
- **LoanService** kapselt die Logik: Ausleihe, Rückgabe, Überfälligkeit
- **`available_copies`** ist ein berechneter Accessor (kein gespeicherter Wert) - verhindert Datendrift
- **Policies** setzen RBAC durch: `BookPolicy` + `LoanPolicy` mit einer dedizierten `return`-Berechtigung
- **Artisan-Kommando** `loans:mark-overdue` für geplante Überfälligkeitserkennung
- **Sanctum Token-Auth** Bearer-Tokens statt Cookie-Session-Flow

---

## Tests

```bash
cd Backend
php artisan test
```

16 Pest-Feature-Tests decken ab:

- Authentifizierung (Register, Login, Logout, Duplikat-E-Mail)
- Buchverwaltung (Auflisten, Erstellen, Löschen, RBAC)
- Ausleihprozess (Ausleihe, Rückgabe, Überfälligkeit, Zugriffskontrolle)

Testdatenbank: SQLite In-Memory - schnell und isoliert vom Entwicklungsstand.

---

## Bekannte Einschränkungen

- Keine E-Mail-Verifizierung
- Nur Token-Authentifizierung (kein stateful SPA-Cookie-Flow)
- Als reines Testprojekt wird nur SQLite verwendet

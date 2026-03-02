$BackendPath  = Join-Path $PSScriptRoot "Backend"
$FrontendPath = Join-Path $PSScriptRoot "Frontend"






# 1. Backend 
$antwort = Read-Host "[Backend] Composer install ausfuehren? (j/N)"
if ($antwort -match '^[JjYy]$') {
    Write-Host "[Backend] Fuehre composer install aus..." -ForegroundColor Cyan
    Push-Location $BackendPath
    composer install
    Pop-Location
} else {
    Write-Host "[Backend] Composer install wird uebersprungen." -ForegroundColor Yellow
}

#2. .env Datei erstellen und Application Key generieren
$antwort = Read-Host "[Backend] .env Datei erstellen? (j/N)"
if ($antwort -match '^[JjYy]$') {
    Write-Host "[Backend] Erstelle .env Datei..." -ForegroundColor Cyan
    Push-Location $BackendPath
    if (-not (Test-Path ".env")) {
        Copy-Item ".env.example" ".env"
        php artisan key:generate
        Write-Host "[Backend] .env Datei wurde erstellt und Application Key generiert." -ForegroundColor Green
    } else {
        Write-Host "[Backend] .env Datei existiert bereits. Ueberspringe Erstellung." -ForegroundColor Yellow
    }
    Pop-Location
} else {
    Write-Host "[Backend] Erstellung der .env Datei wird uebersprungen." -ForegroundColor Yellow
}

# 3 Datenbankmigrationen und Seeder ausführen
$antwort = Read-Host "[Backend] Datenbankmigrationen und Seeder ausfuehren? (j/N)"

if ($antwort -match '^[JjYy]$') {
    Write-Host "[Backend] Bereite Datenbank vor..." -ForegroundColor Cyan
    
    Push-Location $BackendPath
    $dbRelativePath = "database\database.sqlite"
    
    if (-not (Test-Path "database")) {
        New-Item -Path "database" -ItemType Directory | Out-Null
    }

    if (-not (Test-Path $dbRelativePath)) {
        New-Item -Path $dbRelativePath -ItemType File -Force | Out-Null
        Write-Host "  -> Neue SQLite-Datenbank wurde erstellt." -ForegroundColor Gray
    }
    Write-Host "[Backend] Starte Migrationen und Seeding..." -ForegroundColor Cyan

    php artisan migrate:fresh --seed --force
    Pop-Location
    Write-Host "[Backend] Datenbank erfolgreich eingerichtet!" -ForegroundColor Green
} else {
    Write-Host "[Backend] Datenbankmigrationen und Seeder werden uebersprungen." -ForegroundColor Yellow
}

# 4. Frontend 
$antwort = Read-Host "[Frontend] npm install ausfuehren? (j/N)"
if ($antwort -match '^[JjYy]$') {
    Write-Host "[Frontend] Fuehre npm install aus..." -ForegroundColor Cyan
    Push-Location $FrontendPath
    npm install
    Pop-Location
} else {
    Write-Host "[Frontend] npm install wird übersprungen." -ForegroundColor Yellow
}

# 5. Entwicklungsfenster starten 
Write-Host "[Backend] Starte php artisan serve..." -ForegroundColor Cyan
Start-Process "cmd.exe" -ArgumentList "/k", "cd /d `"$BackendPath`" && php artisan serve"

Write-Host "[Frontend] Starte npm run dev..." -ForegroundColor Cyan
Start-Process "cmd.exe" -ArgumentList "/k", "cd /d `"$FrontendPath`" && npm run dev"

Write-Host "Beide Server aktiv. Schliesse die cmd-Fenster, um sie zu beenden." -ForegroundColor Green

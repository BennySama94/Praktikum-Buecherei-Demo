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

# 3. Frontend 
$antwort = Read-Host "[Frontend] npm install ausfuehren? (j/N)"
if ($antwort -match '^[JjYy]$') {
    Write-Host "[Frontend] Fuehre npm install aus..." -ForegroundColor Cyan
    Push-Location $FrontendPath
    npm install
    Pop-Location
} else {
    Write-Host "[Frontend] npm install wird übersprungen." -ForegroundColor Yellow
}

# 4. Entwicklungsfenster starten 
Write-Host "[Backend] Starte php artisan serve..." -ForegroundColor Cyan
Start-Process "cmd.exe" -ArgumentList "/k", "cd /d `"$BackendPath`" && php artisan serve"

Write-Host "[Frontend] Starte npm run dev..." -ForegroundColor Cyan
Start-Process "cmd.exe" -ArgumentList "/k", "cd /d `"$FrontendPath`" && npm run dev"

Write-Host "Beide Server aktiv. Schliesse die cmd-Fenster, um sie zu beenden." -ForegroundColor Green

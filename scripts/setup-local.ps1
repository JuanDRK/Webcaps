Write-Host "== Webcaps local setup =="

$root = Split-Path -Parent $PSScriptRoot
$envExample = Join-Path $root ".env.example"
$envFile = Join-Path $root ".env"
$configExample = Join-Path $root "config/config.example.ini"
$configFile = Join-Path $root "config/config.ini"

if (-Not (Test-Path $envFile)) {
    Copy-Item $envExample $envFile
    Write-Host "Created .env from .env.example"
} else {
    Write-Host ".env already exists"
}

if (-Not (Test-Path $configFile)) {
    Copy-Item $configExample $configFile
    Write-Host "Created config/config.ini from config example"
} else {
    Write-Host "config/config.ini already exists"
}

Write-Host "Done. Update credentials in .env and config/config.ini"

param(
    [string]$Version = "1.0.2",
    [string]$OutputDir = "release\dist"
)

$ErrorActionPreference = "Stop"

$projectRoot = (Resolve-Path "$PSScriptRoot\..").Path
$distRoot = Join-Path $projectRoot $OutputDir
$stagingRoot = Join-Path $distRoot "liquid-glass-admin-$Version"
$zipPath = Join-Path $distRoot "liquid-glass-admin-$Version.zip"

if (Test-Path $stagingRoot) {
    Remove-Item -Path $stagingRoot -Recurse -Force
}

if (Test-Path $zipPath) {
    Remove-Item -Path $zipPath -Force
}

New-Item -Path $stagingRoot -ItemType Directory -Force | Out-Null
New-Item -Path (Join-Path $stagingRoot "assets\css") -ItemType Directory -Force | Out-Null
New-Item -Path (Join-Path $stagingRoot "assets\js") -ItemType Directory -Force | Out-Null
New-Item -Path (Join-Path $stagingRoot "assets\images") -ItemType Directory -Force | Out-Null

Get-ChildItem -Path $projectRoot -Filter *.html -File | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination (Join-Path $stagingRoot $_.Name) -Force
}

Copy-Item -Path (Join-Path $projectRoot "assets\css\style.css") -Destination (Join-Path $stagingRoot "assets\css\style.css") -Force

Get-ChildItem -Path (Join-Path $projectRoot "assets\js") -Filter *.js -File | Where-Object {
    $_.Name -ne "demo-guard.js"
} | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination (Join-Path $stagingRoot ("assets\js\" + $_.Name)) -Force
}

if (Test-Path (Join-Path $projectRoot "assets\images")) {
    Get-ChildItem -Path (Join-Path $projectRoot "assets\images") -File | ForEach-Object {
        Copy-Item -Path $_.FullName -Destination (Join-Path $stagingRoot ("assets\images\" + $_.Name)) -Force
    }
}

if (Test-Path (Join-Path $projectRoot "README_BUYER.md")) {
    Copy-Item -Path (Join-Path $projectRoot "README_BUYER.md") -Destination (Join-Path $stagingRoot "README.md") -Force
}

Compress-Archive -Path (Join-Path $stagingRoot "*") -DestinationPath $zipPath -Force
Write-Host "Buyer package created: $zipPath"


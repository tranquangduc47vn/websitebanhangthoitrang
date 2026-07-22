[CmdletBinding()]
param(
    [string]$Root = (Split-Path -Parent $PSScriptRoot),
    [string]$CsvPath
)

$ErrorActionPreference = 'Stop'
$assetRoots = @(
    (Join-Path $Root 'assets'),
    (Join-Path $Root 'public')
) | Where-Object { Test-Path $_ }

$sourceRoots = @(
    (Join-Path $Root 'application'),
    (Join-Path $Root 'assets')
) | Where-Object { Test-Path $_ }

$sourceFiles = foreach ($sourceRoot in $sourceRoots) {
    Get-ChildItem -Path $sourceRoot -Recurse -File |
        Where-Object { $_.Extension -in '.php', '.css', '.js', '.html' }
}

$results = foreach ($assetRoot in $assetRoots) {
    Get-ChildItem -Path $assetRoot -Recurse -File |
        Where-Object { $_.Extension -in '.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp', '.woff', '.woff2', '.ttf' } |
        ForEach-Object {
            $asset = $_
            $relative = $asset.FullName.Substring($Root.Length + 1).Replace('\', '/')
            $fileName = [Regex]::Escape($asset.Name)
            $matches = $sourceFiles | Select-String -Pattern $fileName -SimpleMatch:$false -List

            [PSCustomObject]@{
                Path       = $relative
                Status     = if ($matches) { 'REFERENCED' } else { 'REVIEW' }
                References = ($matches.Path | ForEach-Object {
                    $_.Substring($Root.Length + 1).Replace('\', '/')
                } | Sort-Object -Unique) -join '; '
            }
        }
}

$results = $results | Sort-Object Status, Path
$results | Format-Table -AutoSize

if ($CsvPath) {
    $results | Export-Csv -Path $CsvPath -NoTypeInformation -Encoding UTF8
    Write-Host "Report written to $CsvPath"
}

Write-Host 'REVIEW means no literal filename reference was found; verify dynamic paths before archiving.'

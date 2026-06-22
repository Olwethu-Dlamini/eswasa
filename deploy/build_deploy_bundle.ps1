<#
  build_deploy_bundle.ps1
  Produces a clean, production-ready copy of the ESWASA site containing ONLY
  the files the live site needs. NON-DESTRUCTIVE: your working repo is never
  modified — everything is copied into a sibling folder.

  Output:  ..\eswasa_deploy_bundle\   (next to the repo, NOT inside it)

  What it drops (verified against the live database — none are referenced):
    - Dev/build dirs:  .git .claude .audit-screenshots .verify node_modules
                       scripts docs deploy
    - Orphaned root mockups, screenshots, presentations, brochures, text
      notes, dev js, and npm manifest (see $deadRootFiles below).

  What it keeps:
    - All page *.php, admin/, assets/, includes/, rs-plugin/, and the 18
      root assets the code/DB actually reference.

  Usage (from the repo root):
    powershell -ExecutionPolicy Bypass -File deploy\build_deploy_bundle.ps1
#>

$ErrorActionPreference = 'Stop'
$src  = Split-Path -Parent $PSScriptRoot          # repo root (parent of deploy\)
$dest = Join-Path (Split-Path -Parent $src) 'eswasa_deploy_bundle'

Write-Host "Source : $src"
Write-Host "Bundle : $dest"

if (Test-Path $dest) {
    Write-Host "Removing previous bundle..."
    Remove-Item -Recurse -Force $dest
}
New-Item -ItemType Directory -Force -Path $dest | Out-Null

# Directories never shipped to a web server.
$excludeDirs = @('.git','.claude','.audit-screenshots','.verify',
                 'node_modules','scripts','docs','deploy') |
               ForEach-Object { Join-Path $src $_ }

# Mirror the tree, excluding the dev directories. robocopy exit codes 0-7 = success.
$xd = @(); foreach ($d in $excludeDirs) { $xd += '/XD'; $xd += $d }
robocopy $src $dest /E /NFL /NDL /NJH /NJS /NP @xd | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy failed (exit $LASTEXITCODE)" }

# Orphaned root files — dead weight, removed from the bundle root only
# (subfolder files of the same name, e.g. assets\img\bg\Ingelo.png, are untouched).
$deadRootFiles = @(
  '.gitignore','package.json','package-lock.json',
  'inspect.js','measure.js','prompt for cms.txt',
  'booklet_text.txt','brochure_text.txt','calibration_text.txt','standards_text.txt',
  'benefitsofcert.PNG','certication.PNG','certification breadcrumb.PNG','contact.PNG',
  'COre Values.PNG','cta.PNG','documents secition.PNG','eswacert land.PNG',
  'facebook logo.PNG','ingelo land.PNG','Ingelo.PNG','new section.PNG',
  'process prod.PNG','prod land.PNG','prod3.PNG','produ2.PNG','product certification.PNG',
  'steps to certification.PNG',
  'image7.jpg','image18.jpg','image26.jpg','produt pro.jpg',
  'screenshot_cards_closeup.png','screenshot_discover.png','screenshot_discover2.png',
  'screenshot_discover3.png','screenshot_discover4.png','screenshot_discover5.png',
  'screenshot_discover6.png','screenshot_discover7.png','screenshot_discover8.png',
  'screenshot_original.png',
  'Booklet final.pdf','CALIBRATION TAB.pdf',
  'Eswatini Standards Authority (ESWASA) Brochure.pdf','standards catalogue price.pdf',
  'CER_FO_002_IPC  Ingelo Certification Application Form.pdf','TD-TR-FM002Applicationform.pdf',
  'ESWASA CLIENT PRESENTATION - ingelo.pptx','ESWASA CLIENT PRESENTATION - WEBSITE2.pptx',
  'INGELO FLYERS.pub'
)
$removed = 0
foreach ($f in $deadRootFiles) {
    $p = Join-Path $dest $f
    if (Test-Path -LiteralPath $p) { Remove-Item -LiteralPath $p -Force; $removed++ }
}

$size = (Get-ChildItem -Recurse -File $dest | Measure-Object Length -Sum).Sum
Write-Host ""
Write-Host "Done. Removed $removed orphaned root files."
Write-Host ("Bundle size: {0:N1} MB" -f ($size / 1MB))
Write-Host "Upload the CONTENTS of '$dest' to your web root."

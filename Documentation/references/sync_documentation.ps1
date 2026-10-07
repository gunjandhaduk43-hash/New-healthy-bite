$docDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$src = Join-Path $docDir "Healthy_Bite_Documentation.docx"
$dst1 = Join-Path $docDir "documentation.docx"
$dst2 = Join-Path $docDir "Healthy_Bite_Project_Documentation.docx"

Write-Host "Copying $src to $dst1 and $dst2..." -ForegroundColor Cyan
try {
    Copy-Item -Path $src -Destination $dst1 -Force
    Copy-Item -Path $src -Destination $dst2 -Force
    Write-Host "✓ Successfully updated documentation.docx and Healthy_Bite_Project_Documentation.docx!" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Please close Microsoft Word first before running this script, as Word locks the file while open." -ForegroundColor Yellow
}

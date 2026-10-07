@echo off
echo Syncing Healthy Bite Documentation to documentation.docx...
copy /Y "%~dp0Healthy_Bite_Documentation.docx" "%~dp0documentation.docx"
copy /Y "%~dp0Healthy_Bite_Documentation.docx" "%~dp0Healthy_Bite_Project_Documentation.docx"
echo Done! Healthy Bite Documentation successfully updated in all target files.
pause

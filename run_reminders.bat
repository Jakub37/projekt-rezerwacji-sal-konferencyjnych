@echo off
set PHP_EXE=C:\xampp\php\php.exe
set APP_DIR=C:\xampp\htdocs\praktykant\projekt1\projekt-rezerwacji-sal-konferencyjnych

cd /d "%APP_DIR%"
"%PHP_EXE%" -f "%APP_DIR%\send_reminders.php" >> "%APP_DIR%\send_reminders.log" 2>&1


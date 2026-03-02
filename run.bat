@echo off
set PORT=8888
echo Starting Bookingsmarts Server on http://localhost:%PORT%...
echo Press Ctrl+C to stop the server.

:: Open the browser in a new window
start http://localhost:%PORT%

:: Start the PHP built-in server
:: We use index.php as the router to handle CI3 routing
php -S localhost:%PORT%

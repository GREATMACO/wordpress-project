<?php
// Datenbank-Verbindungsdetails
$host = '192.168.178.195';
$user = 'Mo';
$password = '1234';
$database = 'wordpress';

// Verbindung zur Datenbank herstellen
$mysqli = new mysqli($host, $user, $password, $database);

// Fehlerbehandlung
if ($mysqli->connect_error) {
    die('Verbindungsfehler: ' . $mysqli->connect_error);
}

// Zeichensatz setzen
$mysqli->set_charset("utf8mb4");

// SQL-Datei lesen
$sqlFile = __DIR__ . '/db/wordpress.sql';

if (!file_exists($sqlFile)) {
    die('SQL-Datei nicht gefunden: ' . $sqlFile);
}

$sqlContent = file_get_contents($sqlFile);

// SQL-Statements ausführen
$statements = array_filter(array_map('trim', explode(';', $sqlContent)));

$successCount = 0;
$errorCount = 0;

foreach ($statements as $statement) {
    if (empty($statement)) {
        continue;
    }
    
    if ($mysqli->query($statement)) {
        $successCount++;
    } else {
        echo "Fehler bei Statement: " . $mysqli->error . "\n";
        $errorCount++;
    }
}

echo "Import abgeschlossen!\n";
echo "Erfolgreiche Statements: " . $successCount . "\n";
echo "Fehlerhafte Statements: " . $errorCount . "\n";

$mysqli->close();
?>

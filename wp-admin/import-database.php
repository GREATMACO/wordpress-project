<?php
/**
 * WordPress Datenbank Import Script
 * Nur lokal zugreifbar!
 */

// Sicherheit: Nur lokale IP zulassen
$allowed_ips = array('127.0.0.1', '::1', '192.168.178.');
$client_ip = $_SERVER['REMOTE_ADDR'];
$is_allowed = false;

foreach ($allowed_ips as $ip) {
    if (strpos($client_ip, $ip) === 0) {
        $is_allowed = true;
        break;
    }
}

if (!$is_allowed) {
    die('Zugriff verweigert!');
}

// WordPress laden
require_once(__DIR__ . '/wp-load.php');

global $wpdb;

// SQL-Datei Pfad (relativ zu diesem Script)
$sql_file = __DIR__ . '/../../db/wordpress.sql';

if (!file_exists($sql_file)) {
    die('SQL-Datei nicht gefunden: ' . $sql_file);
}

echo '<h1>WordPress Datenbank Import</h1>';
echo '<p>Lese SQL-Datei: ' . $sql_file . '</p>';

$sql_content = file_get_contents($sql_file);

// SQL-Statements trennen und ausführen
$statements = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;
$errors = array();

foreach ($statements as $statement) {
    if (empty($statement)) {
        continue;
    }
    
    // Kommentare ignorieren
    if (strpos(trim($statement), '--') === 0) {
        continue;
    }
    
    // SQL ausführen
    $result = $wpdb->query($statement);
    
    if ($result === false) {
        $error_count++;
        $errors[] = $wpdb->last_error;
    } else {
        $success_count++;
    }
}

echo '<div style="background: #e8f5e9; padding: 10px; margin: 10px 0; border-radius: 4px;">';
echo '<strong>Import abgeschlossen!</strong><br>';
echo 'Erfolgreiche Statements: ' . $success_count . '<br>';
echo 'Fehlerhafte Statements: ' . $error_count;
echo '</div>';

if (!empty($errors)) {
    echo '<div style="background: #ffebee; padding: 10px; margin: 10px 0; border-radius: 4px;">';
    echo '<strong>Fehler:</strong><br>';
    foreach ($errors as $error) {
        echo '- ' . htmlspecialchars($error) . '<br>';
    }
    echo '</div>';
}
?>

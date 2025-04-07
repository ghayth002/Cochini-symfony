<?php
// Use default credentials
$user = 'root';
$pass = '';
$host = '127.0.0.1';
$port = '3306';
$dbname = 'pi_dev';

try {
    // Connect to MySQL and select the database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to MySQL database $dbname.\n";
    
    // Check structure of reclamation table
    $stmt = $pdo->query("SHOW COLUMNS FROM reclamation");
    echo "Table structure for reclamation:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . ": " . $row['Type'] . " " . 
             ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
             ($row['Default'] !== null ? " DEFAULT '" . $row['Default'] . "'" : '') . "\n";
    }

    // Try to select data with statut column
    echo "\nAttempting to query reclamation table with statut column:\n";
    $stmt = $pdo->query("SELECT IdReclamation, statut FROM reclamation LIMIT 5");
    $reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($reclamations) > 0) {
        foreach ($reclamations as $reclamation) {
            echo "- Record ID: " . $reclamation['IdReclamation'] . ", Statut: " . 
                 ($reclamation['statut'] ? 'treated' : 'not treated') . "\n";
        }
    } else {
        echo "No records found in the reclamation table.\n";
    }
    
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage() . "\n";
} 
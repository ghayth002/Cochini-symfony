<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pi_dev', 'root', '');
    echo "Connected to database\n";
    
    $stmt = $pdo->query('SHOW COLUMNS FROM reponse');
    echo "Columns in reponse table:\n";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
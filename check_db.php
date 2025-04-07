<?php
try {
    // Database credentials - adjust as needed
    $host = 'localhost';
    $dbname = 'pi_dev';
    $user = 'root';
    $password = '';
    
    // Create a connection with PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Successfully connected to database\n";
    
    // Check reclamation table structure
    $stmt = $pdo->query("DESCRIBE reclamation");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Reclamation table structure:\n";
    echo "==========================\n";
    foreach ($columns as $column) {
        echo sprintf("Field: %s, Type: %s, Null: %s, Key: %s, Default: %s, Extra: %s\n",
            $column['Field'],
            $column['Type'],
            $column['Null'],
            $column['Key'],
            $column['Default'] ?? 'NULL',
            $column['Extra']
        );
    }
    
    // Check if any records exist in the reclamation table
    $stmt = $pdo->query("SELECT COUNT(*) FROM reclamation");
    $count = $stmt->fetchColumn();
    
    echo "\nReclamation count: " . $count . "\n";
    
    if ($count > 0) {
        // Show some sample records
        $stmt = $pdo->query("SELECT * FROM reclamation LIMIT 3");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nSample records:\n";
        echo "==============\n";
        foreach ($records as $record) {
            echo "ID: " . $record['IdReclamation'] . "\n";
            echo "Description: " . substr($record['description'] ?? '', 0, 50) . (strlen($record['description'] ?? '') > 50 ? '...' : '') . "\n";
            echo "Type: " . ($record['typeR'] ?? 'NULL') . "\n";
            echo "Date: " . ($record['date'] ?? 'NULL') . "\n";
            echo "Status: " . (isset($record['statut']) ? ($record['statut'] ? 'Treated' : 'Not treated') : 'NULL') . "\n";
            echo "Adherent ID: " . ($record['Id_adherent'] ?? 'NULL') . "\n";
            echo "Coach ID: " . ($record['Id_coach'] ?? 'NULL') . "\n";
            echo "---\n";
        }
    }
    
    // Check user table structure
    $stmt = $pdo->query("DESCRIBE user");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nUser table structure:\n";
    echo "====================\n";
    foreach ($columns as $column) {
        echo sprintf("Field: %s, Type: %s, Null: %s, Key: %s, Default: %s, Extra: %s\n",
            $column['Field'],
            $column['Type'],
            $column['Null'],
            $column['Key'],
            $column['Default'] ?? 'NULL',
            $column['Extra']
        );
    }
    
    // Check if users exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM user");
    $count = $stmt->fetchColumn();
    
    echo "\nUser count: " . $count . "\n";
    
    if ($count > 0) {
        // Show some sample users
        $stmt = $pdo->query("SELECT * FROM user LIMIT 3");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nSample users:\n";
        echo "=============\n";
        foreach ($users as $user) {
            echo "ID: " . $user['id'] . "\n";
            echo "Name: " . ($user['Nom'] ?? 'NULL') . " " . ($user['Prenom'] ?? 'NULL') . "\n";
            echo "Email: " . ($user['Email'] ?? 'NULL') . "\n";
            echo "---\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?> 
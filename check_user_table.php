<?php
$user = 'root';
$pass = '';
$host = '127.0.0.1';
$port = '3306';
$dbname = 'pi_dev';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get structure of user table
    $stmt = $pdo->query("SHOW COLUMNS FROM user");
    echo "Columns in user table:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    // Check specifically for phoneNumber/phone_number
    echo "\nChecking for phoneNumber or phone_number column:\n";
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns 
                       WHERE table_schema = 'pi_dev' 
                       AND table_name = 'user' 
                       AND (column_name LIKE '%phone%' OR column_name LIKE '%Phone%')");
    $phoneColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($phoneColumns) > 0) {
        foreach ($phoneColumns as $column) {
            echo "- Found column: " . $column . "\n";
        }
    } else {
        echo "- No phone-related columns found!\n";
        
        // Check if there are any migrations for adding phoneNumber column
        echo "\nChecking for migrations related to phoneNumber:\n";
        $stmt = $pdo->query("SELECT version FROM doctrine_migration_versions WHERE version LIKE '%Version20240501%'");
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($migrations) > 0) {
            echo "- Found migrations that might be related to phoneNumber:\n";
            foreach ($migrations as $migration) {
                echo "  - " . $migration . "\n";
            }
        } else {
            echo "- No phoneNumber-related migrations found.\n";
        }
    }
    
    // Try to query with phoneNumber
    echo "\nTrying to query the user table:\n";
    try {
        $stmt = $pdo->query("SELECT * FROM user LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "- Sample user record: " . print_r($user, true) . "\n";
    } catch (PDOException $e) {
        echo "- Query failed: " . $e->getMessage() . "\n";
    }
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage() . "\n";
} 
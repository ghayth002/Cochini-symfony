<?php
try {
    // Connect to MySQL without specifying a database
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    echo "Connected successfully to MySQL.\n";
    
    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS pi_dev");
    echo "Database 'pi_dev' created or already exists.\n";
    
    // Select the database
    $pdo->exec("USE pi_dev");
    
    // Check if the user table exists, create it if it doesn't
    $tableExists = $pdo->query("SHOW TABLES LIKE 'user'")->rowCount() > 0;
    
    if (!$tableExists) {
        $pdo->exec("CREATE TABLE user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(255) DEFAULT NULL,
            Prenom VARCHAR(255) DEFAULT NULL,
            Email VARCHAR(255) DEFAULT NULL,
            MDP VARCHAR(255) DEFAULT NULL,
            Image VARCHAR(255) DEFAULT NULL
        )");
        echo "Created 'user' table.\n";
    }
    
    // Check if the phoneNumber column already exists
    $columnExists = false;
    $columns = $pdo->query("SHOW COLUMNS FROM user")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('phoneNumber', $columns)) {
        $columnExists = true;
        echo "The 'phoneNumber' column already exists in the 'user' table.\n";
    }
    
    // Apply the migration (add phoneNumber to user table) if the column doesn't exist
    if (!$columnExists) {
        $stmt = $pdo->prepare('ALTER TABLE user ADD phoneNumber VARCHAR(20) DEFAULT NULL');
        $result = $stmt->execute();
        
        if ($result) {
            echo "Migration executed successfully: Added 'phoneNumber' column to 'user' table.\n";
        } else {
            echo "Error executing migration.\n";
        }
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
} 
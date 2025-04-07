<?php
try {
    // Connect to MySQL and select the database
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pi_dev', 'root', '');
    echo "Connected successfully to MySQL database pi_dev.\n";
    
    // Check if the status column exists in the reponse table
    $columnExists = false;
    $stmt = $pdo->query("SHOW COLUMNS FROM reponse LIKE 'status'");
    $columnExists = $stmt->rowCount() > 0;
    
    // If the column doesn't exist, add it
    if (!$columnExists) {
        $stmt = $pdo->prepare("ALTER TABLE reponse ADD status VARCHAR(255) NOT NULL DEFAULT 'valider'");
        $result = $stmt->execute();
        
        if ($result) {
            echo "Successfully added 'status' column to the 'reponse' table.\n";
            
            // Update all existing records to have 'valider' status
            $stmt = $pdo->prepare("UPDATE reponse SET status = 'valider'");
            $updateResult = $stmt->execute();
            
            if ($updateResult) {
                echo "Updated all existing responses to have 'valider' status.\n";
            } else {
                echo "Error updating existing responses.\n";
            }
        } else {
            echo "Error adding 'status' column to the 'reponse' table.\n";
        }
    } else {
        echo "The 'status' column already exists in the 'reponse' table.\n";
    }
    
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
} 
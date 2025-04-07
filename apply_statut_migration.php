<?php
try {
    // Connect to MySQL and select the database
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pi_dev', 'root', '');
    echo "Connected successfully to MySQL database pi_dev.\n";
    
    // Check if the statut column already exists in the reclamation table
    $columnExists = false;
    $columns = $pdo->query("SHOW COLUMNS FROM reclamation")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('statut', $columns)) {
        $columnExists = true;
        echo "The 'statut' column already exists in the 'reclamation' table.\n";
    }
    
    // Add the statut column if it doesn't exist
    if (!$columnExists) {
        $stmt = $pdo->prepare('ALTER TABLE reclamation ADD statut TINYINT(1) NOT NULL DEFAULT 0');
        $result = $stmt->execute();
        
        if ($result) {
            echo "Migration executed successfully: Added 'statut' column to 'reclamation' table.\n";
            
            // Update existing reclamations with responses to have statut=1
            $stmt = $pdo->prepare('
                UPDATE reclamation r 
                SET r.statut = 1 
                WHERE EXISTS (
                    SELECT 1 
                    FROM reponse rp 
                    WHERE rp.Id_Reclamation = r.IdReclamation
                )
            ');
            $updateResult = $stmt->execute();
            
            if ($updateResult) {
                echo "Updated existing reclamations with responses to have statut=1.\n";
            } else {
                echo "Error updating existing reclamations.\n";
            }
        } else {
            echo "Error executing migration.\n";
        }
    }
    
    echo "Migration process completed.\n";
    
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
} 
<?php
try {
    // Connect to MySQL without specifying a database
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    echo "Connected successfully to MySQL.\n";
    
    // Select the database
    $pdo->exec("USE pi_dev");
    echo "Using 'pi_dev' database.\n";
    
    // Check if the reclamation table exists
    $reclamationTableExists = $pdo->query("SHOW TABLES LIKE 'reclamation'")->rowCount() > 0;
    
    if (!$reclamationTableExists) {
        // Create reclamation table
        $pdo->exec("CREATE TABLE reclamation (
            IdReclamation INT AUTO_INCREMENT PRIMARY KEY,
            description TEXT DEFAULT NULL,
            typeR VARCHAR(255) DEFAULT NULL,
            Id_coach INT DEFAULT NULL,
            date DATE NOT NULL,
            Id_adherent INT DEFAULT NULL,
            INDEX IDX_CE60640428AA4EAA (Id_coach),
            INDEX IDX_CE60640446949322 (Id_adherent),
            CONSTRAINT FK_RECLAMATION_COACH FOREIGN KEY (Id_coach) REFERENCES user (id),
            CONSTRAINT FK_RECLAMATION_ADHERENT FOREIGN KEY (Id_adherent) REFERENCES user (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`");
        echo "Created 'reclamation' table.\n";
    } else {
        echo "The 'reclamation' table already exists.\n";
    }
    
    // Check if the reponse table exists
    $reponseTableExists = $pdo->query("SHOW TABLES LIKE 'reponse'")->rowCount() > 0;
    
    if (!$reponseTableExists) {
        // Create reponse table
        $pdo->exec("CREATE TABLE reponse (
            id INT AUTO_INCREMENT PRIMARY KEY,
            Id_Reclamation INT DEFAULT NULL,
            Date_reponse DATE DEFAULT NULL,
            Contenu TEXT DEFAULT NULL,
            status VARCHAR(255) NOT NULL,
            INDEX IDX_5FB6DEC79D8A5EFC (Id_Reclamation),
            CONSTRAINT FK_REPONSE_RECLAMATION FOREIGN KEY (Id_Reclamation) REFERENCES reclamation (IdReclamation) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`");
        echo "Created 'reponse' table.\n";
    } else {
        echo "The 'reponse' table already exists.\n";
    }
    
    echo "Migration completed successfully.\n";
    
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
} 
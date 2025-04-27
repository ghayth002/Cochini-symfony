<?php

// Script to update .env.local with correct MAILER_DSN format

$envPath = __DIR__ . '/.env.local';

// Read current content
if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    echo "Current .env.local contents:\n";
    echo $content . "\n\n";
    
    // Check if we need to update the DSN
    if (strpos($content, 'mailtrap+api://') !== false) {
        echo "Found 'mailtrap+api://' in .env.local - updating to smtp:// format\n";
        
        // Replace mailtrap+api:// with smtp://
        $updatedContent = str_replace(
            'mailtrap+api://', 
            'smtp://', 
            $content
        );
        
        // Write updated content back to file
        file_put_contents($envPath, $updatedContent);
        
        echo "Updated .env.local contents:\n";
        echo file_get_contents($envPath) . "\n";
        echo "The .env.local file has been updated successfully!\n";
    } else {
        echo "No 'mailtrap+api://' found in .env.local.\n";
        echo "Current MAILER_DSN format in .env.local:\n";
        
        // Extract current MAILER_DSN
        if (preg_match('/MAILER_DSN=(.+)/', $content, $matches)) {
            echo $matches[0] . "\n";
            
            if (strpos($matches[0], 'smtp://') !== false) {
                echo "The MAILER_DSN is already using the correct 'smtp://' format.\n";
            } else {
                echo "The MAILER_DSN is not using 'smtp://' format. Please update it manually to:\n";
                echo "MAILER_DSN=smtp://username:password@sandbox.smtp.mailtrap.io:2525\n";
            }
        } else {
            echo "No MAILER_DSN found in .env.local\n";
            echo "Please add the following line to .env.local:\n";
            echo "MAILER_DSN=smtp://username:password@sandbox.smtp.mailtrap.io:2525\n";
        }
    }
} else {
    echo ".env.local file not found.\n";
    echo "Creating new .env.local file with correct Mailtrap settings...\n";
    
    $newContent = <<<EOT
# Mailtrap configuration for Symfony Mailer
MAILER_DSN=smtp://58758cbf58c694:310f4b9ed48247@sandbox.smtp.mailtrap.io:2525

# Email sender address
MAILER_FROM=reclamation@yourdomain.com

# Debug mode for email testing
MAILER_DEBUG=true
EOT;
    
    file_put_contents($envPath, $newContent);
    echo "Created new .env.local file with proper SMTP configuration.\n";
}

echo "\nWhat to do next:\n";
echo "1. Make sure .env.local contains the SMTP configuration (smtp://...)\n";
echo "2. Clear the cache: php bin/console cache:clear\n";
echo "3. Try again to respond to a reclamation.\n"; 
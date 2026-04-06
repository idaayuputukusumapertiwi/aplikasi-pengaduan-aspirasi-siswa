<?php
/**
 * Password Hash Generator
 * Use this file to generate password hashes for your users
 */

// Function to generate password hash
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Example usage - uncomment and run to generate hashes
// echo "Password: admin123\n";
// echo "Hash: " . generatePasswordHash('admin123') . "\n\n";

// echo "Password: siswa123\n";
// echo "Hash: " . generatePasswordHash('siswa123') . "\n\n";

// Or use this interactive way:
if (php_sapi_name() === 'cli') {
    echo "=================================\n";
    echo "Password Hash Generator\n";
    echo "=================================\n\n";
    
    echo "Enter password to hash: ";
    $password = trim(fgets(STDIN));
    
    if (!empty($password)) {
        $hash = generatePasswordHash($password);
        echo "\nGenerated Hash:\n";
        echo $hash . "\n\n";
        echo "Copy this hash and paste it in your database.\n";
    } else {
        echo "Password cannot be empty!\n";
    }
} else {
    // Web interface
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Password Hash Generator</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .container { background: #f5f5f5; padding: 30px; border-radius: 8px; }
            h2 { color: #333; }
            input[type="text"] { width: 100%; padding: 10px; margin: 10px 0; font-size: 14px; }
            button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 14px; }
            button:hover { background: #0056b3; }
            .result { background: #fff; padding: 15px; margin-top: 20px; border: 1px solid #ddd; word-break: break-all; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Password Hash Generator</h2>
            <form method="POST">
                <label>Enter Password:</label>
                <input type="text" name="password" placeholder="Enter password to hash" required>
                <button type="submit">Generate Hash</button>
            </form>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
                $password = $_POST['password'];
                $hash = generatePasswordHash($password);
                echo '<div class="result">';
                echo '<strong>Password:</strong> ' . htmlspecialchars($password) . '<br><br>';
                echo '<strong>Generated Hash:</strong><br>' . $hash;
                echo '</div>';
            }
            ?>
        </div>
    </body>
    </html>
    <?php
}
?>
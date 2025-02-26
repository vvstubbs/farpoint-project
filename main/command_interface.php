<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Configuration
$blacklisted_commands = array(
    'rm',
    'shutdown',
    'reboot',
    'halt',
    'init',
    'mkfs',
    'fdisk',
    'dd'
);

$output = "";
$error = "";

// Additional validation function
function is_valid_command($command) {
    if (strlen($command) > 100) return false;
    if (preg_match('/[|;&$`()<>]/', $command)) return false;
    if (!preg_match('/^[a-zA-Z0-9]/', $command)) return false;
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['command'])) {
    $command = trim($_POST['command']);
    
    if (empty($command)) {
        $error = "Error: No command entered";
    } elseif (!is_valid_command($command)) {
        $error = "Error: Invalid command format or contains prohibited characters";
    } else {
        $command_parts = explode(" ", $command);
        $base_command = strtolower($command_parts[0]);
        
        if (in_array($base_command, $blacklisted_commands)) {
            $error = "Error: Command '$base_command' is not allowed.";
        } else {
            $command = escapeshellcmd($command);
            
            // Log the command with user ID
            $log_entry = date('Y-m-d H:i:s') . " - User: " . $_SESSION['user_id'] . " - Command: $command\n";
            file_put_contents('/var/log/web_commands.log', $log_entry, FILE_APPEND);
            
            $safe_command = "timeout 5 $command 2>&1";
            exec($safe_command, $output_array, $return_var);
            
            if ($return_var === 0) {
                $output = implode("\n", $output_array);
            } else {
                $error = "Command execution failed. Return code: $return_var";
                $output = implode("\n", $output_array);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Interface</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .command-container {
            height: 80vh;
            display: flex;
            flex-direction: column;
            margin: 20px;
        }
        .output-frame {
            flex: 7;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
            white-space: pre-wrap;
            margin-bottom: 10px;
        }
        .input-frame {
            flex: 3;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .error { color: #ff4444; }
        input[type="text"] { 
            width: 70%; 
            padding: 5px; 
            margin-right: 10px;
        }
        input[type="submit"] { padding: 5px 15px; }
        
        .dark-mode .output-frame,
        .dark-mode .input-frame {
            border-color: #555;
            background-color: #2a2a2a;
        }
    </style>
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'true') {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</head>
<body>
    <button class="toggle-button" onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <h1>Command Interface</h1>
    
    <div class="command-container">
        <div class="output-frame">
            <?php
            if ($error) {
                echo "<div class='error'>" . htmlspecialchars($error) . "</div>\n";
            }
            if ($output) {
                echo htmlspecialchars($output);
            }
            if (!$error && !$output) {
                echo "Command output will appear here...";
            }
            ?>
        </div>
        
        <div class="input-frame">
            <form method="POST">
                <input type="text" name="command" placeholder="Enter Linux command">
                <input type="submit" value="Execute">
            </form>
        </div>
    </div>
    
    <a href="menu.php">Back to Menu</a>
</body>
</html>

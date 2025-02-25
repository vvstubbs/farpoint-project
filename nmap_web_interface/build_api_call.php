<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_ip = htmlspecialchars($_POST['target_ip']);
    $scan_type = htmlspecialchars($_POST['scan_type']);
    $output_format = htmlspecialchars($_POST['output_format']);

    // Build the command to run the Python script
    $command = escapeshellcmd("python3 nmap_script.py $target_ip $scan_type $output_format");

    // Execute the command
    $output = shell_exec($command);

    // Output the result
    echo $output;
}
?>

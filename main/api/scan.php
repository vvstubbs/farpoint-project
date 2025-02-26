<?php
// Start session and verify authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Authentication required']);
    exit();
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['target'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Target is required']);
    exit();
}

$target = $data['target'];
$scanType = isset($data['scanType']) ? $data['scanType'] : 'basic';

// Validate target to prevent command injection
if (!preg_match('/^[a-zA-Z0-9\.\-\_\/\:]+$/', $target)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid target format']);
    exit();
}

// Define scan parameters based on scan type
$scan_args = '';
switch ($scanType) {
    case 'basic':
        $scan_args = '-sV -F';
        break;
    case 'full':
        $scan_args = '-sV -p-';
        break;
    case 'udp':
        $scan_args = '-sU -F';
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid scan type']);
        exit();
}

// Execute nmap command
$command = "nmap {$scan_args} {$target} -oX -";
$descriptorspec = [
    0 => ["pipe", "r"],  // stdin
    1 => ["pipe", "w"],  // stdout
    2 => ["pipe", "w"]   // stderr
];

$process = proc_open($command, $descriptorspec, $pipes);

if (is_resource($process)) {
    // Close stdin
    fclose($pipes[0]);
    
    // Get output
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    
    // Close pipes
    fclose($pipes[1]);
    fclose($pipes[2]);
    
    // Close process
    $return_value = proc_close($process);
    
    if ($return_value !== 0) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $error]);
        exit();
    }
    
    // Return XML output
    header('Content-Type: application/xml');
    echo $output;
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to execute command']);
}
?>

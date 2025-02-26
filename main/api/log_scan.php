<?php
// Start session and verify authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Authentication required']);
    exit();
}

// Include database connection if needed for logging
require_once('../db.php');

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['target']) || !isset($data['scanType'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$target = $data['target'];
$scanType = $data['scanType'];
$username = $_SESSION['username'];
$timestamp = date('Y-m-d H:i:s');

// Log the scan - this could be to a database or a log file
// For example with a database:
// $stmt = $conn->prepare("INSERT INTO scan_logs (username, target, scan_type, timestamp) VALUES (?, ?, ?, ?)");
// $stmt->bind_param("ssss", $username, $target, $scanType, $timestamp);
// $stmt->execute();

// For simplicity, log to a file
$logs_dir = '../logs';
if (!file_exists($logs_dir)) {
    mkdir($logs_dir, 0755, true);
}
$log_entry = "[{$timestamp}] User: {$username}, Target: {$target}, Scan Type: {$scanType}\n";
file_put_contents($logs_dir . '/scan_logs.txt', $log_entry, FILE_APPEND);

// Return success
header('Content-Type: application/json');
echo json_encode(['status' => 'logged']);
?>

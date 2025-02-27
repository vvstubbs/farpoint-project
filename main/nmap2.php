<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Scanner</title>
    
    <!-- Include the same CSS as your other pages -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
	    
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
    
    <!-- Get user's theme preference -->
    <?php
    $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
    echo '<script>document.documentElement.setAttribute("data-theme", "' . $theme . '");</script>';
    ?>
    
</head>
<body>
    <button class="toggle-button" onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <div class="container mt-4">
        <h2>Network Scanner</h2>
        <div class="card">
            <div class="card-body">
                <form id="scanForm">
                    <div class="form-group mb-3">
                        <label for="target">Target IP/Hostname/Network:</label>
                        <input type="text" class="form-control" id="target" placeholder="e.g., 192.168.1.1, example.com, 10.0.0.0/24" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Scan Type:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scanType" id="basicScan" value="basic" checked>
                            <label class="form-check-label" for="basicScan">
                                Basic Scan (Fast, common ports)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scanType" id="fullScan" value="full">
                            <label class="form-check-label" for="fullScan">
                                Full Scan (All ports, slower)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="scanType" id="udpScan" value="udp">
                            <label class="form-check-label" for="udpScan">
                                UDP Scan (Common UDP ports)
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Start Scan</button>
                </form>
            </div>
        </div>

        <div id="statusContainer" class="mt-3 d-none">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span id="scanStatus">Scan in progress...</span>
                </div>
            </div>
        </div>

        <div id="resultContainer" class="mt-3 d-none">
            <div class="card">
                <div class="card-header">
                    <h5>Scan Results</h5>
                </div>
                <div class="card-body">
                    <div id="scanResults"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const scanForm = document.getElementById('scanForm');
        const statusContainer = document.getElementById('statusContainer');
        const resultContainer = document.getElementById('resultContainer');
        const scanStatus = document.getElementById('scanStatus');
        const scanResults = document.getElementById('scanResults');

        scanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const target = document.getElementById('target').value;
            const scanType = document.querySelector('input[name="scanType"]:checked').value;
            
            // Show status
            statusContainer.classList.remove('d-none');
            resultContainer.classList.add('d-none');
            scanStatus.textContent = 'Scan in progress...';
            
            // Perform the scan
            fetch('api/scan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    target: target,
                    scanType: scanType
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Network scan failed');
                    });
                }
                return response.text();
            })
            .then(xmlData => {
                // Log the scan for auditing
                return fetch('api/log_scan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        target: target,
                        scanType: scanType
                    })
                }).then(() => xmlData);
            })
            .then(xmlData => {
                // Parse and display results
                displayResults(xmlData);
                statusContainer.classList.add('d-none');
                resultContainer.classList.remove('d-none');
            })
            .catch(error => {
                scanStatus.textContent = `Error: ${error.message}`;
                statusContainer.classList.remove('d-none');
                resultContainer.classList.add('d-none');
            });
        });

        function displayResults(xmlData) {
            // Parse XML
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlData, "text/xml");
            
            let resultsHtml = '<div class="scan-summary mb-3">';
            
            // Add scan info
            const scanInfo = xmlDoc.querySelector('nmaprun');
            if (scanInfo) {
                const startTime = new Date(scanInfo.getAttribute('start') * 1000).toLocaleString();
                resultsHtml += `<p><strong>Scan started:</strong> ${startTime}</p>`;
                resultsHtml += `<p><strong>Nmap version:</strong> ${scanInfo.getAttribute('version')}</p>`;
                resultsHtml += `<p><strong>Command:</strong> ${scanInfo.getAttribute('args')}</p>`;
            }
            
            resultsHtml += '</div>';
            
            // Process each host
            const hosts = xmlDoc.querySelectorAll('host');
            if (hosts.length === 0) {
                resultsHtml += '<div class="alert alert-warning">No hosts found.</div>';
            } else {
                hosts.forEach(host => {
                    const address = host.querySelector('address').getAttribute('addr');
                    const status = host.querySelector('status').getAttribute('state');
                    
                    resultsHtml += `<div class="host-entry mb-4">`;
                    resultsHtml += `<h4>Host: ${address} (${status})</h4>`;
                    
                    // Get hostname if available
                    const hostname = host.querySelector('hostname');
                    if (hostname) {
                        resultsHtml += `<p><strong>Hostname:</strong> ${hostname.getAttribute('name')}</p>`;
                    }
                    
                    // Get ports
                    const ports = host.querySelectorAll('port');
                    if (ports.length > 0) {
                        resultsHtml += `<table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Port</th>
                                    <th>Protocol</th>
                                    <th>State</th>
                                    <th>Service</th>
                                    <th>Version</th>
                                </tr>
                            </thead>
                            <tbody>`;
                        
                        ports.forEach(port => {
                            const portId = port.getAttribute('portid');
                            const protocol = port.getAttribute('protocol');
                            const state = port.querySelector('state').getAttribute('state');
                            
                            let service = 'N/A';
                            let version = 'N/A';
                            
                            const serviceElem = port.querySelector('service');
                            if (serviceElem) {
                                service = serviceElem.getAttribute('name') || 'N/A';
                                
                                // Try to get version info
                                const product = serviceElem.getAttribute('product') || '';
                                const versionAttr = serviceElem.getAttribute('version') || '';
                                const extraInfo = serviceElem.getAttribute('extrainfo') || '';
                                
                                version = [product, versionAttr, extraInfo].filter(Boolean).join(' ');
                                if (!version) version = 'N/A';
                            }
                            
                            resultsHtml += `<tr>
                                <td>${portId}</td>
                                <td>${protocol}</td>
                                <td>${state}</td>
                                <td>${service}</td>
                                <td>${version}</td>
                            </tr>`;
                        });
                        
                        resultsHtml += `</tbody></table>`;
                    } else {
                        resultsHtml += `<p>No open ports found.</p>`;
                    }
                    
                    resultsHtml += `</div>`;
                });
            }
            
            scanResults.innerHTML = resultsHtml;
        }
    });
    
    // Theme toggle functionality will be inherited from your existing system
    // as it's typically handled by JavaScript included via menu.php
    </script>
    <a href="menu.php">Back to Menu</a>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NMAP Web Interface</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>NMAP API Call Builder</h1>
        <form action="build_api_call.php" method="POST">
            <label for="target_ip">Target IP Address:</label>
            <input type="text" id="target_ip" name="target_ip" required>

            <label for="scan_type">Scan Type:</label>
            <select id="scan_type" name="scan_type">
                <option value="ping">Ping Scan</option>
                <option value="syn">SYN Scan</option>
                <option value="connect">Connect Scan</option>
                <option value="versions">Version Detection</option>
                <option value="os">OS Detection</option>
            </select>

            <label for="output_format">Output Format:</label>
            <select id="output_format" name="output_format">
                <option value="xml">XML</option>
                <option value="nmap">Nmap</option>
                <option value="gnmap">Grepable</option>
            </select>

            <button type="submit">Build API Call</button>
        </form>
    </div>
</body>
</html>

Creating a web page to interact with an NMAP application to build an API call requires a few steps. 
This will involve serving HTML through a PHP server, handling form submissions, and then using these 
parameters to generate an NMAP command using Python. Following is a basic example to illustrate these steps.

### 1. Setting up a PHP Server

You need a server to run PHP. If you don't have one, you can use XAMPP, WAMP, or MAMP.

### 2. HTML Form (index.php)

This is where the user will submit their parameters for the NMAP scan.

```html
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
```

### 3. CSS Styling (styles.css)

Make the form look better.

```css
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input, select, button {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background-color: #5cb85c;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #4cae4c;
}
```

### 4. PHP Script (build_api_call.php)

This script processes the form submission and passes the parameters to a Python script.

```php
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_ip = htmlspecialchars($_POST['target_ip']);
    $scan_type = htmlspecialchars($_POST['scan_type']);
    $output_format = htmlspecialchars($_POST['output_format']);

    // Build the command to run the Python script
    $command = escapeshellcmd("python3 nmap_script.py $target_ip $scan_type $output_format");

    // Execute the command
    $output = shell_exec($command);

    // Display the output
    echo "<pre>$output</pre>";
}
?>
```

### 5. Python Script (nmap_script.py)

This script receives the parameters and executes an NMAP scan.

```python
import sys
import subprocess

def main():
    # Retrieve parameters from command line arguments
    target_ip = sys.argv[1]
    scan_type = sys.argv[2]
    output_format = sys.argv[3]

    # Map scan types to nmap command-line options
    scan_options = {
        'ping': '-sn',
        'syn': '-sS',
        'connect': '-sT',
        'versions': '-sV',
        'os': '-O'
    }

    output_options = {
        'xml': '-oX',
        'nmap': '-oN',
        'gnmap': '-oG'
    }

    # Construct the nmap command
    nmap_command = f"nmap {scan_options[scan_type]} {output_options[output_format]} output.{output_format} {target_ip}"

    try:
        # Run the nmap command
        result = subprocess.run(nmap_command, shell=True, capture_output=True, text=True, check=True)

        # Print the command used (for debugging)
        print(f"Command: {result.args}")

        # Print the output
        print(result.stdout)
    except subprocess.CalledProcessError as e:
        print(f"An error occurred: {e.stderr}")

if __name__ == "__main__":
    main()
```

### Notes:
1. **Security:** Be very careful with executing shell commands from user input. Ensure that the parameters are properly 
   handled and sanitized to prevent command injection vulnerabilities.
2. **Permissions:** The web server process running the PHP script must have the necessary permissions to execute the 
   NMAP command. Consider running the server as a user with reduced privileges and granting NMAP execution access.
3. **Environment:** Ensure NMAP is installed and properly configured on your server.
4. **Dependencies:** Both PHP and Python must be installed and configured correctly on your server.

By following these steps, you'll create a basic web interface to build and execute an NMAP API call. 
You can extend this by adding more features, such as additional scan options, logging, or error handling.
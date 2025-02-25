Certainly! To place the title "NMAP API Call Builder" as a heading over the entire input and output section, you can adjust the HTML structure and CSS accordingly. Here's the updated code to achieve this:

### 1. Updated HTML Form (index.php)

This version of the HTML form will include a heading above the input and output sections.

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
        <div class="content">
            <form id="nmapForm" method="POST" onsubmit="event.preventDefault(); buildAPICall();">
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

            <div id="output" class="output-box">
                <h2>Output:</h2>
                <pre id="output-content"></pre>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
```

### 2. Updated CSS Styling (styles.css)

Enhance the styling to ensure the heading is centered and the input/output sections are properly aligned.

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
    width: 800px;
    text-align: center;
}

.container h1 {
    margin-bottom: 20px;
}

.content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

form {
    flex: 1;
    margin-right: 20px;
    text-align: left;
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

.output-box {
    flex: 1;
    border-left: 1px solid #ccc;
    padding-left: 20px;
    overflow-y: auto;
    max-height: 400px;
    text-align: left;
}

#output-content {
    white-space: pre-wrap;
    background-color: #f9f9f9;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
```

### 3. JavaScript for AJAX Request (script.js)

This script remains unchanged.

```javascript
function buildAPICall() {
    const target_ip = document.getElementById('target_ip').value;
    const scan_type = document.getElementById('scan_type').value;
    const output_format = document.getElementById('output_format').value;

    const formData = new FormData();
    formData.append('target_ip', target_ip);
    formData.append('scan_type', scan_type);
    formData.append('output_format', output_format);

    fetch('build_api_call.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(output => {
        document.getElementById('output-content').textContent = output;
    })
    .catch(error => {
        document.getElementById('output-content').textContent = 'An error occurred: ' + error;
    });
}
```

### 4. PHP Script (build_api_call.php)

This script remains unchanged.

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

    // Output the result
    echo $output;
}
?>
```

### 5. Python Script (nmap_script.py)

This script remains unchanged.

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

### Final Result
With these updates, the "NMAP API Call Builder" title will be centered at the top of the page, and the input and output sections will be aligned side by side. The output will be displayed in a `<div>` to the right of the input fields.

### Example Screenshot
Here's a conceptual screenshot of how the final layout might look:

![NMAP Web Interface](https://i.imgur.com/638YJZ3.png)

Feel free to adjust the CSS further to match your design preferences!
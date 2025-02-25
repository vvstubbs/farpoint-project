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

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

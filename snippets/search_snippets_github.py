#!/usr/bin/env python3

import cgi
import cgitb
import requests
from urllib.parse import quote
import html
import os

# Enable error reporting
cgitb.enable()

# Retrieve the GitHub Personal Access Token from an environment variable
GITHUB_TOKEN = os.environ.get('GITHUB_TOKEN')

def search_code_snippets(query):
    """
    Searches GitHub for code snippets related to the supplied query.

    Args:
        query (str): The search query string.

    Returns:
        list or str: A list of dictionaries containing repository name, file path, and URL,
                     or an error message.
    """
    # Encode the query string for URL
    encoded_query = quote(query)

    # GitHub Code Search API endpoint
    url = f"https://api.github.com/search/code?q={encoded_query}+in:file"

    headers = {}
    if GITHUB_TOKEN:
        headers['Authorization'] = f'token {GITHUB_TOKEN}'

    # Make a GET request to the API
    response = requests.get(url, headers=headers)

    # Check for HTTP errors
    if response.status_code != 200:
        # Extract error message from response
        error_message = response.json().get('message', 'Unknown error occurred.')
        return f"Error: {response.status_code} - {error_message}"

    # Parse the JSON response
    results = response.json()

    # Extract relevant information from each item
    snippets = []
    for item in results.get('items', []):
        snippet = {
            'repository': item['repository']['full_name'],
            'file_path': item['path'],
            'html_url': item['html_url']
        }
        snippets.append(snippet)

    return snippets

def main():
    print("Content-Type: text/html")
    print()

    form = cgi.FieldStorage()
    query = form.getvalue('query', '')

    print("""
    <html>
    <head>
        <title>Code Snippet Search</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .result { margin-bottom: 15px; }
            .error { color: red; }
            input[type="text"] { width: 80%; padding: 8px; }
            input[type="submit"] { padding: 8px 16px; }
        </style>
    </head>
    <body>
        <h1>Code Snippet Search</h1>
        <form method="post" action="/cgi-bin/search_snippets.py">
            <input type="text" name="query" value="{0}" placeholder="Enter your search query">
            <input type="submit" value="Search">
        </form>
    """.format(html.escape(query)))

    if query:
        print("<h2>Search Results for '<em>{0}</em>':</h2>".format(html.escape(query)))

        # Call the search function
        results = search_code_snippets(query)

        if isinstance(results, str):
            # An error occurred
            print('<p class="error">{0}</p>'.format(html.escape(results)))
        elif results:
            # Display the search results
            for idx, snippet in enumerate(results, start=1):
                print(f"""
                <div class="result">
                    <strong>Result #{idx}</strong><br>
                    <strong>Repository:</strong> {html.escape(snippet['repository'])}<br>
                    <strong>File Path:</strong> {html.escape(snippet['file_path'])}<br>
                    <strong>URL:</strong> <a href="{html.escape(snippet['html_url'])}" target="_blank">{html.escape(snippet['html_url'])}</a>
                </div>
                """)
        else:
            print("<p>No code snippets found for your query.</p>")

    print("""
    </body>
    </html>
    """)

if __name__ == "__main__":
        main()
        
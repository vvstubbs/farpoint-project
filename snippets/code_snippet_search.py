import requests
from urllib.parse import quote

def search_code_snippets(query):
    """
    Searches GitHub for code snippets related to the supplied query.

    Args:
        query (str): The search query string.

    Returns:
        list: A list of dictionaries containing repository name, file path, and URL.
    """
    # Encode the query string for URL
    encoded_query = quote(query)

    # GitHub Code Search API endpoint
    url = f"https://api.github.com/search/code?q={encoded_query}+in:file"

    # Make a GET request to the API
    response = requests.get(url)

    # Check for HTTP errors
    if response.status_code != 200:
        print(f"Error: {response.status_code} - {response.reason}")
        return []

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
    print("=== Code Snippet Search ===")
    query = input("Enter your search query (e.g., 'binary search in python'): ")

    print("\nSearching for code snippets related to your query...\n")
    snippets = search_code_snippets(query)

    if not snippets:
        print("No code snippets found for your query.")
        return

    # Display the search results
    for idx, snippet in enumerate(snippets, start=1):
        print(f"Result #{idx}")
        print(f"Repository: {snippet['repository']}")
        print(f"File Path: {snippet['file_path']}")
        print(f"URL: {snippet['html_url']}\n")

if __name__ == "__main__":
    main()
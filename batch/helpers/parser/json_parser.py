import json

def get_data_from_content(response_data):
    # Parse the HTML content using BeautifulSoup
    try:
        json_data = json.loads(response_data.get('content', '{}'))
    except json.JSONDecodeError:
        print(f'JSON decode error. {response_data}')
    return json_data

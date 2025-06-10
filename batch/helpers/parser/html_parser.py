from bs4 import BeautifulSoup


def get_data_from_content(response_data):
    # Parse the HTML content using BeautifulSoup
    soup = BeautifulSoup(response_data.get('content', {}), 'html.parser')
    # Find script tags with JSON-LD data
    json_ld_scripts = soup.find_all('script', type="application/ld+json")
    
    return json_ld_scripts

def get_data_from_content_tag(response_data, tag_name, attrs):
    # Parse the HTML content using BeautifulSoup
    soup = BeautifulSoup(response_data.get('content', {}), 'html.parser')
    # Find script tags with JSON-LD data
    json_ld_scripts = soup.find(tag_name, {attrs['name']: attrs['value']})

    return json_ld_scripts
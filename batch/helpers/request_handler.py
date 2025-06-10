import requests
import pdb

DEFAULT_TIMEOUT = 10  # Default timeout value in seconds


def get_headers():
    return {}


def resource_locator_handler(url, request_type="GET", data={}, headers={}, timeout=DEFAULT_TIMEOUT, form_data={}):
    # Send an HTTP GET request to retrieve the HTML content of the webpage
    #pdb.set_trace()
    try:
        if request_type == "GET":
            response = requests.get(url, headers=headers, timeout=timeout)
        elif request_type == "POST":
            if form_data:
                response = requests.post(url, data=form_data, headers=headers, timeout=timeout)
            else:
                response = requests.post(url, json=data, headers=headers, timeout=timeout)
        # Check for successful response
        if response.status_code == 200:
            return {
                'status_code': response.status_code,
                'content': response.content,
                'is_error': False
            }
        else:
            return {
                'status_code': response.status_code,
                'error_msg': response,
                'is_error': True
            }
    except Exception as e:
        return {
            'status_code': 'NA',
            'error_msg': str(e),
            'is_error': True
        }
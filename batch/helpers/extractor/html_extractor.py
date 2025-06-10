import json


def extract_json_from_html_script(json_scripts):
    # Extract JSON data from each script
    json_data = []
    for script in json_scripts:
        # Check if script has content
        if script.string:
            try:
                json_data.append(json.loads(script.string.strip()))
            except json.JSONDecodeError:
                pass  # Skip scripts with non-JSON content
    return json_data

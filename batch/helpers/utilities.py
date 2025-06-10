import json
from helpers.parser.json_parser import get_data_from_content
from helpers.request_handler import resource_locator_handler
import pdb
import re    
import json
import shutil
import csv
import os
import math

def is_duplicate(consolidated_list_of_ids, check_id):
    #pdb.set_trace()
    duplicate = False
    if(len(consolidated_list_of_ids) > 0):
        for list_product_ids in consolidated_list_of_ids:
            if(list_product_ids == check_id):
                duplicate = True
    
    return duplicate


def remove_duplicate_keywords(keywords):
  """Removes duplicate keywords from a list.

  Args:
    keywords: A list of strings representing keywords.

  Returns:
    A new list containing only the unique keywords, preserving the original order as much as possible.
  """
  seen = set()
  unique_keywords = []
  for keyword in keywords:
    if keyword not in seen:
      seen.add(keyword)
      unique_keywords.append(keyword)
  return unique_keywords

def remove_html_tags(text):
  """Removes HTML tags from a string.

  Args:
    text: The input string containing HTML tags.

  Returns:
    A new string with all HTML tags removed.
  """
  clean = re.compile('<.*?>')
  return re.sub(clean, '', text)

def remove_leading_comma_space(text):
  """Removes the first two characters (", ") from a string if it starts with them.

  Args:
    text: The input string.

  Returns:
    The string with the leading ", " removed if present, otherwise the original string.
  """
  if text.startswith(", "):
    return text[2:]  # Slice the string starting from the 3rd character (index 2)
  else:
    return text

def append_unique_words(str1, str2):
    """
    Compares two comma-separated words strings and appends words from the
    second string to the first if they are not already present.

    Args:
        str1: The first comma-separated words string.
        str2: The second comma-separated words string.

    Returns:
        A new string containing the words from the first string with any unique
        words from the second string appended, separated by commas.
    """
    words1 = [word.strip() for word in str1.split(',')] if str1 else []
    words2 = [word.strip() for word in str2.split(',')] if str2 else []

    unique_words2 = [word for word in words2 if word not in words1 and word]

    combined_words = words1 + unique_words2
    return ", ".join(combined_words)

def get_json(url, header):
    
    #if(url == 'https://ds.fashiongo.net/api/products/7a0fcddd-d733-4d4c-9065-ee6e10d3320c'):
        #pdb.set_trace()
    try:
        response = resource_locator_handler(url, headers=header, timeout=100)
        content = response['content']
        json_data = json.loads(content)
        return json_data
    except Exception as e:
        print(f'Error from get variant detail json: {str(e)}, url = {url}')


def old_duplicate_csv(filepath):
    """
    Duplicates a CSV file with "_updated" appended to its name.

    Args:
        filepath (str): The path to the CSV file.
    """
    new_filepath = ""
    if not filepath.lower().endswith(".csv"):
        print("Error: File is not a CSV file.")
        return new_filepath

    if not os.path.exists(filepath):
        print(f"Error: File '{filepath}' not found.")
        return new_filepath

    try:
        base, ext = os.path.splitext(filepath)
        new_filepath = base + "_updated" + ext
        shutil.copy2(filepath, new_filepath)  # copy2 preserves metadata
        print(f"File '{filepath}' duplicated to '{new_filepath}'.")
        return new_filepath
    except Exception as e:
        print(f"An error occurred during duplicating csv file: {e}")   

def compare_string_numbers(str1, str2):
    """
    Compares the number value of two strings, handling edge cases.

    Args:
        str1: The first string.
        str2: The second string.

    Returns:
        True if the number values of the strings are equal, False otherwise.
    """
    # Handle empty strings and '0' cases
    if not str1:
        str1_val = 0
    else:
        try:
            str1_val = float(str1)
        except ValueError:
            return False  # Return False if str1 is not a valid number

    if not str2:
        str2_val = 0
    else:
        try:
            str2_val = float(str2)
        except ValueError:
            return False  # Return False if str2 is not a valid number

    return str1_val == str2_val

def convert_all_caps_to_title(text):
    """
    Checks if a string contains words that are entirely in uppercase.
    If so, it converts those all-caps words to title case.
    Otherwise, it returns the original string.

    Args:
        text: The input string.

    Returns:
        The string with all-caps words converted to title case, or the original string
        if no all-caps words are found.
    """
    words = text.split()
    modified_words = []
    has_all_caps = False

    for word in words:
        # Check if the word consists entirely of uppercase letters
        if re.fullmatch(r'[A-Z]+', word):
            modified_words.append(word.title())
            has_all_caps = True
        else:
            modified_words.append(word)

    if has_all_caps:
        return " ".join(modified_words)
    else:
        return text



def calculate_selling_price(unit_price, multiplication_factor, shipping_rate):
    """
    Calculates the selling price based on unit cost, multiplication factor, and shipping rate.

    Args:
    unit_price: The cost of a single unit.
    multiplication_factor: The factor by which the base price is multiplied.
    shipping_rate: The cost of shipping per unit.

    Returns:
    The calculated selling price, rounded up to end in 0, 2, 5, or 8 with 0 cents.
    """
    try:
        #intermediate_price = (unit_price + shipping_rate) * multiplication_factor
        intermediate_price = (unit_price * multiplication_factor) + shipping_rate
        rounded_price = math.ceil(intermediate_price)
    except Exception as e:
        print(f"An error occurred during calculation of selling price: {e}")  

    
    remainder = rounded_price % 10
    if remainder <= 2 and remainder != 0:
        rounded_price += (2 - remainder)
    elif remainder <= 5 and remainder > 2:
        rounded_price += (5 - remainder)
    elif remainder <= 8 and remainder > 5:
        rounded_price += (8 - remainder)
    elif remainder > 8:
        rounded_price += (10 - remainder)

    return int(rounded_price)

def round_up_selling_price(price):
    rounded_price = price
    remainder = rounded_price % 10
    if remainder <= 2 and remainder != 0:
        rounded_price += (2 - remainder)
    elif remainder <= 5 and remainder > 2:
        rounded_price += (5 - remainder)
    elif remainder <= 8 and remainder > 5:
        rounded_price += (8 - remainder)
    elif remainder > 8:
        rounded_price += (10 - remainder)

    return int(rounded_price)

def remove_duplicate_keywords_from_csv(comma_separated_string):
  """
  Converts a comma-separated string to a string of unique values
  separated by commas.

  Args:
    comma_separated_string: The string to convert.

  Returns:
    A string of unique values, separated by commas. Returns an
    empty string if the input string is empty or contains only
    whitespace.  Duplicate values are removed, and the order of the
    first occurrence of each unique value is preserved.
  """
  if not comma_separated_string.strip():
    return ""  # Handle empty or whitespace-only strings

  values = comma_separated_string.split(',')
  # Remove leading/trailing whitespace from each value
  values = [value.strip() for value in values]
  unique_values = []
  seen = set()
  for value in values:
    if value not in seen:
      seen.add(value)
      unique_values.append(value)
  return ','.join(unique_values)


def duplicate_csv(filepath, test_run = False):
    """
    Duplicates a CSV file with "_updated" appended to its name.

    Args:
        filepath (str): The path to the CSV file.
    """
    new_filepath = ""
    if not filepath.lower().endswith(".csv"):
        print("Error: File is not a CSV file.")
        return new_filepath

    if not os.path.exists(filepath):
        print(f"Error: File '{filepath}' not found.")
        return new_filepath

    try:
        base, ext = os.path.splitext(filepath)
        new_filepath = base + "_updated" + ext
        if(test_run):
            new_filepath = "TEST_" + base + "_updated" + ext
        shutil.copy2(filepath, new_filepath)  # copy2 preserves metadata
        print(f"File '{filepath}' duplicated to '{new_filepath}'.")
        return new_filepath
    except Exception as e:
        print(f"An error occurred during duplicating csv file: {e}")      


def check_data_integrity(filtered_products):
    # Perform data integrity checks.
    
    # Check for orphan variants.
    orphan_variants = []
    product_titles_in_output = set()
    variant_handles_in_output = set()
    
    for row in filtered_products:
        if row['Title']:
            product_titles_in_output.add(row['Handle'])
        else:
            variant_handles_in_output.add(row['Handle'])

    for row in filtered_products:
        if not row['Title']:  # Variant row
            found_product = False
            for product_title in product_titles_in_output:
                if product_title in row['Handle']:
                    found_product = True
                    break
            if not found_product:
                orphan_variants.append(row['Handle'])
    
    if orphan_variants:
        print(f"Error: Orphan variant(s) found: {orphan_variants}\n")
    else:
        print("Integrity check passed: No orphan variants found.")
    """
    # Check for prices below $25.
    below_25_prices = []
    for row in filtered_products:
        try:
            price = float(row['Price'])
            if price < 25:
                below_25_prices.append(f"{row['Title'] if row['Title'] else row['Handle']}: {price}")
        except ValueError:
            print(f"Warning: Invalid price found: {row['Price']} for product/variant: {row['Title'] if row['Title'] else row['Handle']}")

    if below_25_prices:
        print(f"Error: Prices below $25 found: {below_25_prices}")
    else:
        print("Integrity check passed: All prices are $25 or above.")
"""

def prepare_post_message(record, payload_structure):
    """
    Parses a CSV record into a dictionary suitable for a POST request payload,
    based on the provided payload_structure, including hardcoded values.
    """
    #pdb.set_trace()
    post_payload = {}
    for field_def in payload_structure:
        api_field = field_def["api_field"]
        target_type = field_def.get("type", str)
        
        # --- Check for hardcoded 'value' first ---
        if "value" in field_def:
            # Use the hardcoded value directly, applying type conversion if specified
            try:
                if target_type == int:
                    post_payload[api_field] = int(field_def["value"])
                elif target_type == float:
                    post_payload[api_field] = float(field_def["value"])
                elif target_type == bool:
                    post_payload[api_field] = str(field_def["value"]).strip().lower() in ['true', '1', 'yes']
                else: # Default to string for any other type
                    post_payload[api_field] = str(field_def["value"]).strip()
            except (ValueError, TypeError) as e:
                # This should ideally not happen if hardcoded values are correct
                print(f"Error: Hardcoded value '{field_def['value']}' for field '{api_field}' "
                      f"could not be converted to type {target_type.__name__}: {e}")
                # Decide how to handle: skip field, set to None, or raise
                raise # Re-raise if a hardcoded value is fundamentally malformed
            continue # Move to the next field in the structure
            
        # --- If no hardcoded 'value', proceed with CSV data ---
        csv_field = field_def["csv_field"] # Must be present if no 'value'
        is_required = field_def.get("required", False)
        default_value = field_def.get("default")

        csv_value = record.get(csv_field)

        if csv_value is None or str(csv_value).strip() == '':
            if is_required:
                raise ValueError(
                    f"Required CSV field '{csv_field}' is missing or empty in record: {record}. "
                    f"Cannot prepare payload."
                )
            elif default_value is not None:
                post_payload[api_field] = default_value
            else:
                continue # Skip this field if not required and no default
        else:
            try:
                if target_type == int:
                    post_payload[api_field] = int(csv_value)
                elif target_type == float:
                    post_payload[api_field] = float(csv_value)
                elif target_type == bool:
                    post_payload[api_field] = str(csv_value).strip().lower() in ['true', '1', 'yes']
                else:
                    post_payload[api_field] = str(csv_value).strip()
            except (ValueError, TypeError) as e:
                if is_required:
                     raise ValueError(
                        f"Failed to convert value '{csv_value}' for required field '{csv_field}' "
                        f"to type {target_type.__name__}: {e}. Record: {record}"
                    )
                else:
                    print(f"Warning: Could not convert value '{csv_value}' for field '{csv_field}' "
                          f"to type {target_type.__name__}. Skipping this field. Error: {e}")
                continue

    return post_payload
PRINT_CARD_API_URL = 'http://localhost/kdms/api/upsertdevotee.php'
REMOVE_CARD_API_URL = 'http://localhost/kdms/api/upsertdevotee.php'
UPSERT_DEVOTEE_API_URL = 'http://localhost/kdms/api/upsertdevotee.php'

DEVOTEE_REC_FILE_NAME = r'Runner20250609.csv'

# PC Variables
# DESC_FILE_PATH = r'C:\Users\gupta\OneDrive\Documents\Anekay\Apparel Site\description_wkg_folder'
DEVOTEE_REC_FILE_PATH = r'C:\Users\gupta\OneDrive\Documents\kdms\api_working_folder'

#Mac variables
DEVOTEE_REC_FILE_PATH_MAC = '/Users/anil/Documents/kdms/api_working_folder'

PRINT_CARD_API_DATA = [
    {"csv_field": "devotee_key", "api_field": "devotee_key", "type": str, "required": True},
    # --- New: Hardcoded field example ---
    # This field 'source_system' will always be 'CSV_Uploader' regardless of CSV content
    {"api_field": "eventId", "type": str, "value": "2025JB"},
    # Another example: A fixed status for new users
    {"api_field": "requestType", "type": str, "value": "addToPrintQueue"}
]

REMOVE_FROM_PRINT_API_DATA = [
    {"csv_field": '"devotee_key"', "api_field": "devotee_key", "type": str, "required": True},
    # --- New: Hardcoded field example ---
    # This field 'source_system' will always be 'CSV_Uploader' regardless of CSV content
    {"api_field": "eventId", "type": str, "value": "2025JB"},
    # Another example: A fixed status for new users
    {"api_field": "requestType", "type": str, "value": "removeFromPrintQueue"}
]

REGISTER_DEVOTEE_API_DATA = [
    {"csv_field": "devotee_key", "api_field": "devotee_key", "type": str, "required": True},
    {"csv_field": "2024 Duties", "api_field": "devotee_seva_id", "type": str},
    {"csv_field": "Proposed accommodation for 2025", "api_field": "devotee_accommodation_id", "type": str, "required": True},
    # --- New: Hardcoded field example ---
    # This field 'source_system' will always be 'CSV_Uploader' regardless of CSV content
    {"api_field": "eventId", "type": str, "value": "2025JB"},
    # Another example: A fixed status for new users
    {"api_field": "requestType", "type": str, "value": "registerDevotee"}
]
UNREGISTER_DEVOTEE_API_DATA = [
    {"csv_field": "devotee_key", "api_field": "devotee_key", "type": str, "required": True},
    # --- New: Hardcoded field example ---
    # This field 'source_system' will always be 'CSV_Uploader' regardless of CSV content
    {"api_field": "eventId", "type": str, "value": "2025JB"},
    # Another example: A fixed status for new users
    {"api_field": "requestType", "type": str, "value": "deleteDevotee"},
    {"api_field": "registrationOnly", "type": str, "value": "true"}
]

ADD_DEVOTEE_API_DATA = [
    {"csv_field": "devotee_first_name", "api_field": "devotee_first_name", "type": str, "required": True},
    {"csv_field": "devotee_last_name", "api_field": "devotee_last_name", "type": str, "required": True},
    {"csv_field": "devotee_gender", "api_field": "devotee_gender", "type": str, "required": True},
    {"csv_field": "devotee_dob", "api_field": "devotee_dob", "type": str, "required": True},
    {"csv_field": "devotee_id_type", "api_field": "devotee_id_type", "type": str, "required": True},
    {"csv_field": "devotee_id_number", "api_field": "devotee_id_number", "type": str, "required": True},
    {"csv_field": "devotee_cell_phone_number", "api_field": "devotee_cell_phone_number", "type": str},
    {"csv_field": "devotee_type", "api_field": "devotee_type", "type": str},
    {"csv_field": "devotee_status", "api_field": "devotee_status", "type": str},
    {"csv_field": "devotee_referral", "api_field": "devotee_referral", "type": str},
    {"csv_field": "devotee_seva_id", "api_field": "devotee_seva_id", "type": str},
    {"csv_field": "devotee_accommodation_id", "api_field": "devotee_accommodation_id", "type": str},
    {"csv_field": "devotee_station", "api_field": "devotee_station", "type": str},
    {"csv_field": "devotee_country", "api_field": "devotee_country", "type": str},
    
    # --- New: Hardcoded field example ---
    # This field 'source_system' will always be 'CSV_Uploader' regardless of CSV content
    {"api_field": "eventId", "type": str, "value": "2025JB"},
    # Another example: A fixed status for new users
    {"api_field": "requestType", "type": str, "value": "upsertDevotee"}
]
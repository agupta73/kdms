PRINT_CARD_API_URL = 'http://localhost/kdms/api/upsertdevotee.php'

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




from helpers import utilities
from batch_config import DEVOTEE_REC_FILE_NAME, DEVOTEE_REC_FILE_PATH, DEVOTEE_REC_FILE_PATH_MAC, PRINT_CARD_API_URL,PRINT_CARD_API_DATA
from helpers.parser.json_parser import get_data_from_content
from helpers.request_handler import resource_locator_handler
import pdb
import os
import shutil
import csv
import urllib.parse
from helpers.data_reader import read_from_mysql 

from bs4 import BeautifulSoup
import json

chatty = True
mac = True
updated_handles = []
updated_rows = []
header = 'Content-Type:application/x-www-form-urlencoded'

def update_data():    
    duplicate_file = ""
    print("Starting the process to process Devotee data!!")
    
    if(mac):
        #duplicate_file =  utilities.duplicate_csv(DEVOTEE_REC_FILE_PATH_MAC + "/" + DEVOTEE_REC_FILE_NAME)
        duplicate_file =  DEVOTEE_REC_FILE_PATH_MAC + "/" + DEVOTEE_REC_FILE_NAME
    else:
        #duplicate_file =  utilities.duplicate_csv(DEVOTEE_REC_FILE_PATH + "\\" + DEVOTEE_REC_FILE_NAME)
        duplicate_file =  DEVOTEE_REC_FILE_PATH + "\\" + DEVOTEE_REC_FILE_NAME

    
    if(duplicate_file != ""):
        try:
            with open(duplicate_file, 'r', newline='', encoding='utf-8') as infile:
                reader = csv.DictReader(infile)
                fieldnames = reader.fieldnames
                if(chatty):
                    print(f'number of fieldnames read from duplicate file: {len(fieldnames)}')
                devotee_recs = list(reader) #read all rows into memory
        except Exception as e:
            print(f"An error occurred reading the duplicated file: {e}")
        
        modified = False
        url = PRINT_CARD_API_URL
        response = ""
        update_counter = 0
        
        for row_index, row in enumerate(devotee_recs):                                                         
            #print(updates)
            response = call_api(url, row )
       

def call_api(url, row):
    print("reaching to call API")
    get_json(url=url, header="", data=row)

def get_json(url, header, data):
    
    #if(url == 'https://ds.fashiongo.net/api/products/7a0fcddd-d733-4d4c-9065-ee6e10d3320c'):
    pdb.set_trace()
    payload_structure = PRINT_CARD_API_DATA
    data = utilities.prepare_post_message(record=data, payload_structure=payload_structure)
    #pdb.set_trace()
    try:
        response = resource_locator_handler(url=url, request_type="POST",data=data, headers=header, timeout=100)
        content = response['content']
        json_data = json.loads(content)
        return json_data
    except Exception as e:
        print(f'Error from get variant detail json: {str(e)}, url = {url}')

if __name__ == "__main__":
    update_data()



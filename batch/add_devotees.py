

from helpers import utilities
from batch_config import DEVOTEE_REC_FILE_NAME, DEVOTEE_REC_FILE_PATH, DEVOTEE_REC_FILE_PATH_MAC, PRINT_CARD_API_URL,PRINT_CARD_API_DATA, REMOVE_FROM_PRINT_API_DATA, REMOVE_CARD_API_URL, UPSERT_DEVOTEE_API_URL, REGISTER_DEVOTEE_API_DATA, UNREGISTER_DEVOTEE_API_DATA, ADD_DEVOTEE_API_DATA
from helpers.parser.json_parser import get_data_from_content
from helpers.request_handler import resource_locator_handler
import pdb
import os
import shutil
import csv
import urllib.parse
from helpers.data_reader import read_from_mysql 
import sys

from bs4 import BeautifulSoup
import json

chatty = True
mac = True
updated_handles = []
updated_rows = []
header = 'Content-Type:application/x-www-form-urlencoded'

def update_data(option=1):    
    duplicate_file = ""
    print("Starting the process to process Devotee data!!")
    #pdb.set_trace()
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
        
        payload_structure = []
        modified = False
        url = ""
        
        #Add to print queue
        if(option == '1'):
            url = PRINT_CARD_API_URL
            payload_structure = PRINT_CARD_API_DATA

        #Remove from print queue
        elif(option == '2'):
            url = REMOVE_CARD_API_URL
            payload_structure = REMOVE_FROM_PRINT_API_DATA

        #Register Existing Devotees for the event
        elif(option == '3'):
            url = UPSERT_DEVOTEE_API_URL
            payload_structure = REGISTER_DEVOTEE_API_DATA

        #Un-Register Existing Devotees from the event
        elif(option == '4'):
            url = UPSERT_DEVOTEE_API_URL
            payload_structure = UNREGISTER_DEVOTEE_API_DATA

        #Add new devotee record
        elif(option == '5'):
            url = UPSERT_DEVOTEE_API_URL
            payload_structure = ADD_DEVOTEE_API_DATA

        
        response = ""
        update_counter = 0

        for row_index, row in enumerate(devotee_recs):                                                         
            #print(updates)
            response = call_api(url, row, payload_structure )
       

def call_api(url, row, payload_structure):
    get_json(url=url, header="", data=row, payload_structure=payload_structure)

def get_json(url, header, data, payload_structure):   
    data = utilities.prepare_post_message(record=data, payload_structure=payload_structure)
    
    try:
        pdb.set_trace()
        response = resource_locator_handler(url=url, request_type="POST", headers=header, timeout=100, form_data=data)
        content = response['content']
        json_data = json.loads(content)
        return json_data
    except Exception as e:
        print(f'Error from get detail json: {str(e)}, url = {url}')

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # sys.argv[1] would be the first argument after the script name
        option = sys.argv[1]
    update_data(option)



import json
import datetime
import csv
#import mysql.connector
import pymysql
import pymysql.cursors
from config.settings import TIMESTAMP_FORMAT
import pdb
from config import settings

def get_timestamp():
	"""Generates a datetime string in TIMESTAMP_FORMAT specified in settings."""
	now = datetime.datetime.now()
	return now.strftime(TIMESTAMP_FORMAT)

def connect_to_mysql():
    try:
        conn = pymysql.connect(
            host= settings.HOST,
            user=settings.USER,
            password=settings.PASSWORD,
            database=settings.DATABASE
        )
        return conn
    except pymysql.Error as err:
        print(f"Error connecting to MySQL: {err}")
        return None

def load_csv_data(file_path):
    data = []
    with open(file_path, 'r') as csvfile:
        csv_reader = csv.DictReader(csvfile)
        for row in csv_reader:
            data.append(row)
    return data

def load_json_data(file_path):
    with open(file_path, 'r') as jsonfile:
        data = json.load(jsonfile)
    return data

def read_from_mysql(custom_sql = ''):
 

    conn = connect_to_mysql()
    if not conn:
        exit(1)

    # If needed, Load CSV data from a file
    # csv_data = load_csv_data("your_csv_file.csv")
    # Load JSON data
    # json_data = load_json_data("your_json_file.json")
    # Convert JSON data to a suitable format for insertion (e.g., list of dictionaries)
    # insert_data_into_mysql(conn, converted_json_data, table_name)
    
    result = []
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = 'select * from product_master'
    if(custom_sql):
        sql = custom_sql
    
    try:
        cursor.execute(sql)
        result = cursor.fetchall()
    except Exception as e:        
        print (f'>>> unexpected error: {e}')
        print(sql)
    
    #pdb.set_trace()    
    cursor.close()
    conn.close()                        
    return result
    


    


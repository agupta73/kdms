import json
import datetime
import csv
#import mysql.connector
import pymysql
from config.settings import TIMESTAMP_FORMAT
import pdb
from config import settings

def get_timestamp():
	"""Generates a datetime string in TIMESTAMP_FORMAT specified in settings."""
	now = datetime.datetime.now()
	return now.strftime(TIMESTAMP_FORMAT)

def write_to_csv(data, filename):
    """Writes JSON data to a CSV file"""
    with open(filename, 'w', newline='') as csvfile:
        # Determine fieldnames based on data type
        if isinstance(data, list):
            # Get all unique field names from the list of dictionaries
            fieldnames = set()
            for item in data:
                fieldnames.update(item.keys())
            fieldnames = list(fieldnames)
        elif isinstance(data, dict):
            # Get fieldnames from the single dictionary
            fieldnames = list(data.keys())
            # Wrap the dictionary in a list for uniform processing
            data = [data]
        else:
            raise ValueError("Data should be a list or a dictionary")

        # Initialize the CSV writer with the fieldnames
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        writer.writeheader()

        # Write each row of data
        for item in data:
            writer.writerow(item)

def connect_to_mysql_old(host, user, password, database):
    try:
        
        conn = pymysql.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )
        
        return conn
    except pymysql.Error as err:
        print(f"Error connecting to MySQL: {err}")
        return None

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

def write_to_mysql(db_name, company, data,  sql_mapping = '', data_type = 'product'):
    """"
    #old, depricated code
    if db_name == "mktlens":
        # Replace with your actual database credentials
        host = "anil_dell"
        user = "mktlens_user"
        password = "mktlens_user"
        database = "mktlens_v20240803"
        # table_name = "product_master"

    conn = connect_to_mysql_old(host, user, password, database)
    """
    
    conn = connect_to_mysql()
    if not conn:
        exit(1)

    # If needed, Load CSV data from a file
    # csv_data = load_csv_data("your_csv_file.csv")
    # Load JSON data
    # json_data = load_json_data("your_json_file.json")
    # Convert JSON data to a suitable format for insertion (e.g., list of dictionaries)
    # insert_data_into_mysql(conn, converted_json_data, table_name)
    #pdb.set_trace()
    cursor = conn.cursor()
    sql = sql_mapping
    """"
    if(data_type ==  'product' or data_type == 'inventory' or data_type == 'reviews'):
        # Construct SQL INSERT statement based on data structure and table schema    
        # sql = "INSERT INTO {} (column1, column2, ...) VALUES (%s, %s, ...)".format('product_master')
        sql = ''
        if(sql_mapping == ''):
            sql = "INSERT INTO `product_master` \
                (`product_key`,`company`,`product_id`,`variant_id`,`product_url`, \
                    `variant_url`,`product_primary_image_url`,`variant_primary_image_urll`, \
                        `variant_size`,`variant_color`,`variant_description`,`variant_fabric`, \
                            `variant_name`,`variant_price_reg`,`variant_price_sale`,`variant_offer`, \
                                `variant_availability`,`variant_sku_id`,`variant_keywords`,`comments`) \
                VALUES \
                (%s, %s, %s, %s, %s, \
                    %s, %s, %s, \
                        %s, %s, %s, %s, \
                            %s, %d, %d, %s, \
                                %s, %s, %s, %s ).format(`product_master`)"
        else:
            sql = sql_mapping
    """
    counter = 0
    error_counter = 0
    for row in data:
        # Prepare data for insertion (e.g., handle missing values, data type conversions)
        
        counter += 1

        try:
            cursor.execute(sql, tuple(row.values()))
        except Exception as e:
            error_counter += 1
            print (f'{error_counter}": >>> unexpected error:" {e}')
            print(tuple(row.values()))
            #pdb.set_trace()
                              
    conn.commit()
    cursor.close()
    conn.close()
    print(f'{counter} rows written to DB. {error_counter} rows couldn''t be saved' )


def run_mysql_sp(sp_name):
    sp_param = ''
    conn = connect_to_mysql()
    if not conn:
        exit(1)

    if(sp_name == 'sp_calculate_product_sold'):
        #sp_param = {'input_date':datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")}
        sp_param = {'input_date_as_str':'','company_as_str':''}
        
    #pdb.set_trace()
    try:
        with conn.cursor() as cursor:
            cursor.callproc(sp_name, sp_param)
            results = cursor.fetchall()
        return results
    except Exception as e:
        print(f"Error calling stored procedure: {e}")
        return None
    finally:
        conn.close()
        cursor.close()
    
    

    


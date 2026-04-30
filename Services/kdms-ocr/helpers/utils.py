import os
import time
import base64
import pytesseract

from PIL import Image
from io import BytesIO

TEMP_FOLDER_PATH = './temp/'

def get_image_text_data_from_image_path(path):
    """get image text data using image path."""
    img = Image.open(path)
    return get_image_text(img)

def get_image_text_data_from_base64_data(base64imagedata):
    """get image text data using image path."""
    img = Image.open(BytesIO(base64.b64decode(base64imagedata)))
    return get_image_text(img)

def get_image_text(img):
    """read text lines from image."""
    ts = time.time()
    temp_image_file_name = f'temp-bw-image-{int(ts)}'
    # This directory store the BW image of uploaded image file
    temp_image_name = f'{TEMP_FOLDER_PATH}{temp_image_file_name}.png'
    img.save(temp_image_name)
    text = pytesseract.image_to_string(Image.open(temp_image_name))
    os.remove(temp_image_name)
    return text

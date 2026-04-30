""" This file has view of OCR reader."""

from helpers.aadhaar_ocr_parser import AadhaarCardParser
from helpers.constants import IMAGE_TYPES
from helpers.utils import get_image_text_data_from_base64_data, get_image_text_data_from_image_path

class OCRReaderView:
    """ OCR reader view."""

    def __init__(self, params={}):
        self.params = params
       
    def run_processor(self):
        """ 
            params: { 
                image_data_type: BASE64
                image_data: base64 data.
            }
        """
        response = {
            'error': False,
            'data': {}
        }
        # intialize it to 200 (OK) response.
        error_code = 200
        image_data_type = self.params.get('image_data_type')
        
        if image_data_type in IMAGE_TYPES:
            image_data = self.params.get('image_data')
            card_type = self.params.get('card_type')
            if image_data_type == 'BASE64':
                # Todo: to be automatically picks depending upong type.
                text_from_image = get_image_text_data_from_base64_data(image_data)
                if card_type == 'AADHAAR':
                    aadhaar_parser_obj = AadhaarCardParser(text_from_image)
                    data = aadhaar_parser_obj.run_parser()
                    response.update({
                        'data': data
                    })
        else:
            # Todo: update this with meaningful error message.
            response.update({
                'error': True,
                'error_msg': 'Error occurred'
            })
            error_code = 500
        return response, error_code
        # call OCR reader main function here.



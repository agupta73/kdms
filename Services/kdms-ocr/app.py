# using flask_restful
from flask import Flask, jsonify, request
from flask_restful import Resource, Api
import logging
import time

from views.ocr_reader import OCRReaderView

# creating the flask app
app = Flask(__name__)
# creating an API object
api = Api(app)
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s %(levelname)s %(message)s'
)
logger = logging.getLogger("kdms-ocr")


@app.before_request
def _log_request_start():
    request._start_time = time.time()
    logger.info(
        "OCR request started method=%s path=%s remote=%s",
        request.method,
        request.path,
        request.remote_addr
    )


@app.after_request
def _log_request_end(response):
    start = getattr(request, "_start_time", None)
    duration_ms = int((time.time() - start) * 1000) if start else -1
    logger.info(
        "OCR request completed method=%s path=%s status=%s duration_ms=%s",
        request.method,
        request.path,
        response.status_code,
        duration_ms
    )
    return response


# @cross_origin()
# class KDMSOCRReader(Resource):

#     # Corresponds to POST request
#     def post(self):
#         data = request.get_json() # status code
#         ocr_reader_obj = OCRReaderView(data)
#         response, error_code = ocr_reader_obj.run_processor()
#         return response, error_code

# # adding the defined resources along with their corresponding urls
# api.add_resource(KDMSOCRReader, '/api/v1/kdms-ocr/')

@app.route('/api/v1/kdms-ocr/', methods=['POST'])
def api():
    try:
        request_data = dict(request.values)
        ocr_reader_obj = OCRReaderView(request_data)
        response, status_code = ocr_reader_obj.run_processor()
        response = jsonify({'data': response, 'status_code': status_code})
        response.headers.add('Access-Control-Allow-Origin', '*')
        response.headers.add('Access-Control-Allow-Headers', 'Content-Type,Authorization')
        response.headers.add('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE')
        return response
    except Exception:
        logger.exception("OCR processing failed")
        return jsonify({'data': {'error': 'OCR processing failed'}, 'status_code': 500}), 500
    # return response


# driver function
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug = True)

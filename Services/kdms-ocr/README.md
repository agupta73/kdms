# KDMS OCR Setup

## Windows:

### Step 1: Install Python

Download and install the Python distribution based on your machine. You can find the download link [here](https://www.python.org/downloads/windows/). We recommend using version 3.10.X. Make sure to check the "Install pip" option during installation.

Add Python to the PATH environment variables by adding the scripts folder path to the PATH environment variables. You can find the reference link explaining how to install and use pip3 [here](https://www.activestate.com/resources/quick-reads/how-to-install-and-use-pip3/).

More links:

https://www.digitalocean.com/community/tutorials/install-python-windows-10
https://www.activestate.com/resources/quick-reads/how-to-install-and-use-pip3/


### Install Python dependencies

Go to the kdms-ocr directory and run the below command:

```
pip3 install -r requirements.txt
```

### Download Tesseract for Windows

Download Tesseract for Windows [here](https://tesseract-ocr.github.io/tessdoc/Downloads.html). We recommend using version 5.3.0. Make sure you add Tesseract to the PATH environment variables.

More links:
https://github.com/tesseract-ocr/tesseract
https://tesseract-ocr.github.io/tessdoc/Downloads.html
https://github.com/UB-Mannheim/tesseract/wiki
https://ironsoftware.com/csharp/ocr/blog/ocr-tools/tesseract-ocr-windows/

### CORS Error

If you see a CORS error while making a call from an application to this app, please download this [extension](https://chrome.google.com/webstore/detail/cors-unblock/lfhmikememgdcahcdlaciloancbhjino/related?hl=en) for the Google Chrome browser.
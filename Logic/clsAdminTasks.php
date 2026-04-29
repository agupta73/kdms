<?php

class clsAdminTasks {


    private $url;
    private $request = array();
    public $debug = false;

    public function __construct($requestObject) {
        $this->request = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['api_dir_server'] . 'manageAdmin.php';
    }

    public function processAdminTasks() {
        $response = $this->curl_rest($this->url, true, $this->request);

        if($this->debug) {
            echo "from API call - Response", "<br>";
            var_dump($response);
        }
        return $response;
    }

    /*
    private function get_records_from_API($requestData) {
        if($this->debug) {
            echo "from API call", "<br>";
            var_dump($requestData);
        }
        $ch = curl_init();
        $url =$this->url . urlencode($requestData) ;
        if($this->debug) {
            echo "from API call - URL", "<br>";
            var_dump($url);

        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }
    */
    private function curl_rest($url, $is_post = true, $post_fields = null, $request_type = null) {

        if($this->debug) {
            echo "from API call", "<br>";
            var_dump($post_fields);
        }
        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, true);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_type);
        }
        // application/x-www-form-urlencoded (consistent with PHP $_POST in manageAdmin.php)
        if (is_array($post_fields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);

        if($this->debug) {
            echo "after API call", "<br>";
            var_dump($response);
        }
        curl_close($ch);

        if ($response === false || $curlErrno !== 0) {
            error_log(
                '[KDMS] login API curl failed: ' . json_encode([
                    'errno' => $curlErrno,
                    'error' => $curlError,
                    'url' => $url,
                ], JSON_UNESCAPED_SLASHES)
            );

            return [
                'status'  => false,
                'message' => 'Unable to reach the login API from the server. Check Apache logs, KDMS_INTERNAL_ORIGIN, or database/API errors.',
            ];
        }

        $decoded = $this->decodeJsonResponse((string) $response);

        if (! is_array($decoded)) {
            error_log('[KDMS] login API returned non-JSON or empty response: ' . substr((string) $response, 0, 500));

            return [
                'status'  => false,
                'message' => 'Invalid response from login service.',
            ];
        }

        return $decoded;
    }

    /**
     * Strips leading PHP notices/BOM and decodes the first JSON object or array in the body.
     */
    private function decodeJsonResponse(string $raw): ?array
    {
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $trim = trim($raw);
        if ($trim === '') {
            return null;
        }
        $decoded = json_decode($trim, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        $pos = strpos($trim, '[');
        if ($pos === false) {
            $pos = strpos($trim, '{');
        }
        if ($pos !== false && $pos > 0) {
            $decoded = json_decode(substr($trim, $pos), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clsOptionHandler
 *
 * @author agupta
 */
class clsServicesManager
{

    private $debug = false;

    private $config_data;
    private $optionType = "";

    private $eventId = "";
    private $helperUrl = "";

    private $apiURL = "";


    private $request = "";
    private $loggerReady = false;

    public function __construct($requestObject)
    {
        $this->request = $requestObject;
        $this->config_data = include(dirname(__DIR__) . "/site_config.php");
        $this->apiURL = $this->config_data['api_dir'];
        $this->initLogger();
    }

    private function initLogger(): void
    {
        if ($this->loggerReady) {
            return;
        }
        $this->loggerReady = true;
        $loggerPath = dirname(__DIR__) . '/kmreports_log.php';
        if (is_readable($loggerPath)) {
            require_once $loggerPath;
            if (function_exists('kmreports_log_bootstrap')) {
                kmreports_log_bootstrap();
            }
        }
    }

    public function setOptionType($optionType)
    {
        $this->optionType = $optionType;
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Safely fetch a field when request payload is array-based.
     */
    private function requestField(string $key, string $default = ""): string
    {
        if (is_array($this->request) && isset($this->request[$key])) {
            return (string) $this->request[$key];
        }

        return $default;
    }

    public function getRecords()
    {
        switch ($this->optionType) {
            case "config":
                $this->helperUrl = $this->config_data['service_dir'] . 'helper.php';
                $helperConfig = $this->getServiceFromHelper($this->optionType);
                return $helperConfig;

                break;

            case "login":
                $this->apiURL = $this->apiURL . 'manageAdmin.php';

                $response = $this->curl_rest($this->apiURL, true, $this->request);

                if ($this->debug) {
                    echo "from API call - Response", "<br>";
                    //var_dump($response);
                }

                return $response;

                break;

            case "favorites":

                $this->apiURL = $this->apiURL . "manageAdmin.php"; //?type=favorites&user_key=" . $this->request['user_key'] . "&fav_type=" . $this->request['fav_type'] ;
                $response = $this->curl_rest($this->apiURL, true, $this->request);

                if ($this->debug) {
                    echo "from API call - Response", "<br>";
                    var_dump($this->apiURL);
                    var_dump($response);
                }

                return $response;

                break;
    
            case "event":
                $this->apiURL = $this->apiURL . "loadOptions.php?option_type=EventDetail" . "&key=" . $this->request . "&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "", $this->request);

                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }

                return $response;

                break;

            case "ADS": //Devotee for Seva
                // mode = option type (ADS); key = Seva ID or All
                $this->apiURL = $this->apiURL . "searchDevotee.php?mode=ADS&key=" . $this->requestField('key') . "&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "", $this->request);
                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;

                case "DSA": //Devotee Seva Attendance
                    // mode = option type (ADS); key = Seva ID or All
                    $this->apiURL = $this->apiURL . "searchDevotee.php?mode=DSA&key=" . $this->requestField('key') . "&eventId=" . $this->eventId;
                    $response = $this->curl_rest($this->apiURL, "", $this->request);
                    if ($this->debug) {
                        echo "from API call - rquest URL/Response", "<br>";
                        //var_dump($this->apiURL);
                        //var_dump($response);
                    }
                    return $response;
                    break;

            case "AOD": //Devotee for Accommodation
                // mode = option type (ADS); key = Accommodation ID or All
                $this->apiURL = $this->apiURL . "searchDevotee.php?mode=AOD&key=" . $this->request . "&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "", $this->request);
                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;
            
            case "DPR": //Devotee for Accommodation
                    // mode = option type (ADS); key = Accommodation ID or All
                    $this->apiURL = $this->apiURL . "searchDevotee.php?mode=DPR&key=" . $this->request;
                    $response = $this->curl_rest($this->apiURL, "", $this->request);
                    if ($this->debug) {
                        echo "from API call - rquest URL/Response", "<br>";
                        //var_dump($this->apiURL);
                        //var_dump($response);
                    }
                    return $response;
                    break;

            case "accoCounts": //Devotee Accomodation counts for the dashboard

                $this->apiURL = $this->apiURL . "getReport.php?type=DevoteeCount&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "");

                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;

            case "accoCount": //Accomodation names/counts for the dashboard dropdown

                $this->apiURL = $this->apiURL . "getReport.php?type=AccoCount&accoType=" . $this->request . "&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "");

                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;

            case "accoAvailability":
                $this->apiURL = $this->apiURL . "getReport.php?type=AccoAvailability&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "");
                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                }
                return $response;
                break;


            case "DutyReport": //Devotee Accomodation counts for the dashboard

                $dutyKey = is_array($this->request) ? $this->requestField("key") : (string) $this->request;
                $photoRequired = is_array($this->request) ? strtoupper($this->requestField("photo_required", "N")) : "N";
                $this->apiURL = $this->apiURL . "getReport.php?type=DutyReport&key=" . $dutyKey . "&eventId=" . $this->eventId . "&photo_required=" . $photoRequired;
                $response = $this->curl_rest($this->apiURL, "");

                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;

            case "sevaCounts": //Devotee Seva counts for the dashboard

                $this->apiURL = $this->apiURL . "loadOptions.php?option_type=Seva&key=" . $this->request . "&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "");

                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;

            case "dutyLocations": //Devotee Seva counts for the dashboard

                $this->apiURL = $this->apiURL . "loadOptions.php?option_type=dutyLocations&key=" . $this->request . "&eventId=" . $this->eventId;
                $response = $this->curl_rest($this->apiURL, "");

                if ($this->debug) {
                    echo "from API call - rquest URL/Response", "<br>";
                    //var_dump($this->apiURL);
                    //var_dump($response);
                }
                return $response;
                break;

            default:

                break;
        }
    }

    public function upsertRecords()
    {
        $response = array();
        $url = "";     
        
        if (!empty($this->request)) {
            if($this->requestField("requestType") !== ""){
                $this->optionType = $this->requestField("requestType");
            }
            else {
                $this->optionType = $this->requestField("type");
            }               
        }

        switch ($this->optionType) {
            
            case "upsertRemark":

                $url = $this->apiURL . "upsertDevotee.php";
                $response = json_encode( $this->curl_rest($url, true, $this->request));

                if ($this->debug) {
                    echo "clsServiceManager:upsertRemark: Request => <br>";
                    var_dump($this->request);
                    echo "<br> Response => ";
                    //var_dump($response);
                }

                break;

            case "upsertFav":

                $url = $this->apiURL . "manageAdmin.php";
                $response = json_encode( $this->curl_rest($url, true, $this->request));

                if ($this->debug) {
                    //echo "clsServiceManager:upsertFAv: Request => <br>";
                    //var_dump($this->request);
                    echo "<br> Response => ";
                    var_dump($response);
                }

                break;
    
            case "upsertAttendance":

                $url = $this->apiURL . "upsertDevotee.php";
                $response = json_encode( $this->curl_rest($url, true, $this->request));

                if ($this->debug) {
                    echo "clsServiceManager:upsertRemark: Request => <br>";
                    var_dump($this->request);
                    echo "<br> Response => ";
                    //var_dump($response);
                }

                break;

            
            default:
            echo "request type not specified";
                break;
            
        }


        return $response;
    }


    private function getServiceFromHelper($optionType)
    {

        $ch = curl_init();
        $this->helperUrl = $this->helperUrl . "?option_type=" . $optionType;
        curl_setopt($ch, CURLOPT_URL, $this->helperUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === false && function_exists('kmreports_log')) {
            kmreports_log('ERROR', 'KMReports helper call failed', [
                'url'        => $this->helperUrl,
                'curl_error' => curl_error($ch),
            ]);
        }
        curl_close($ch);
        return $response;
    }

    private function curl_rest($url, $is_post = true, $post_fields = null, $request_type = null)
    {

        if ($this->debug) {
            //echo "from API call", "<br>";
            //var_dump($post_fields);
        }
        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, count($post_fields));
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_type);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        // curl_setopt($ch, CURLOPT_USERPWD, "kdms_admin:oxwkV-3]S&{t");
        $serviceKey = getenv('KMREPORTS_SERVICE_KEY');
        if (!is_string($serviceKey) || $serviceKey === '') {
            $serviceKey = getenv('KDMS_SERVICE_KEY');
        }
        if (is_string($serviceKey) && $serviceKey !== '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-KDMS-SERVICE-KEY: ' . $serviceKey]);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $rawResponse = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = $rawResponse;
        if ($response === false) {
            $curlError = curl_error($ch);
            if (function_exists('kmreports_log')) {
                kmreports_log('ERROR', 'KMReports API call failed', [
                    'url'        => $url,
                    'optionType' => $this->optionType,
                    'curl_error' => $curlError,
                ]);
            }
            curl_close($ch);
            return [
                'status'  => false,
                'message' => 'API connection failed.',
                'info'    => $curlError,
            ];
        }

        if ($this->debug) {
            //echo "after API call", "<br>";
            //var_dump($response);
        }
        try {
            $response = json_decode((string) $rawResponse, true);
            if ($response === null && $httpCode >= 400) {
                if (function_exists('kmreports_log')) {
                    $snippet = is_string($rawResponse) ? substr(preg_replace('/\s+/', ' ', $rawResponse), 0, 400) : '';
                    kmreports_log('ERROR', 'KMReports API non-JSON error response', [
                        'url'        => $url,
                        'optionType' => $this->optionType,
                        'http_code'  => $httpCode,
                        'body_snip'  => $snippet,
                    ]);
                }
                $response = [
                    'status'  => false,
                    'message' => 'API returned error response.',
                    'info'    => 'HTTP ' . $httpCode,
                ];
            } elseif ($response === null) {
                if (function_exists('kmreports_log')) {
                    $snippet = is_string($rawResponse) ? substr(preg_replace('/\s+/', ' ', $rawResponse), 0, 400) : '';
                    kmreports_log('ERROR', 'KMReports API invalid JSON', [
                        'url'        => $url,
                        'optionType' => $this->optionType,
                        'http_code'  => $httpCode,
                        'body_snip'  => $snippet,
                    ]);
                }
                $response = [
                    'status'  => false,
                    'message' => 'API returned invalid JSON.',
                    'info'    => 'HTTP ' . $httpCode,
                ];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }
        //$response = json_decode($response, true);

        curl_close($ch);
        return $response;
    }
}
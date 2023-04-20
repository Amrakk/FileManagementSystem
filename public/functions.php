<?php
    function callApi($url, $data=null, $method) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return json_encode(array('code' => 10, 'error' => 'Invalid URL'));
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        switch($method) {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                if ($data != null) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PUT':
                if ($data != null) {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                return json_encode(array('code' => 10, 'error' => 'Unsupported method'));
        }

        $response = curl_exec($curl);
        
        if (curl_errno($curl)) 
            return json_encode(array('code' => 10,'error' => curl_error($curl)));
        
        curl_close($curl);
        
        $data = json_decode($response, true);
        return $data;
    }

    function get($key, $default = null)
    {
        $app_config = require_once('../config.php');

        return !empty($app_config[$key]) ? $app_config[$key] : $default;
    }

?>


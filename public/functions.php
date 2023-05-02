<?php
    function callApi($url, $data=null, $method) 
    {

        $curl = curl_init();
        $method = strtoupper($method);

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
                return array('code' => 20, 'message' => 'Unsupported method');
        }

        $response = curl_exec($curl);
        
        if (curl_errno($curl)) 
            return array('code' => 10,'message' => curl_error($curl));
        
        curl_close($curl);
        
        $data = json_decode($response, true);
        return $data;
    }

?>
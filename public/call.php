<!-- This is an example on how to communicate with api -->

<?php
// http://localhost/FMS/public/call.php


    $apiUrl = 'http://example.com/api/products';

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Send the request and store the response
    $response = curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        echo 'Error: ' . curl_error($curl);
        exit;
    }

    // Close cURL session
    curl_close($curl);

    // Decode the response JSON string into a PHP array
    $data = json_decode($response, true);

    // Display the response
    foreach ($data as $product) {
        echo $product['name'] . ' - ' . $product['price'] . '<br>';
    }


    echo "this is a test";
?>


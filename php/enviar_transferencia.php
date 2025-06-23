<?php
header("Content-Type: application/json");

function sendStockTransfer($jsonPayload) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://192.168.2.12:8086/api/StockTransfer',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jsonPayload,
        CURLOPT_HTTPHEADER => array(
            'User-Agent: Apidog/1.0.0 (https://apidog.com)',
            'Content-Type: application/json',
            'Accept: */*',
            'Host: 192.168.2.12:8086',
            'Connection: keep-alive'
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error = curl_error($curl);
        curl_close($curl);
        http_response_code(500);
        echo json_encode(["error" => $error]);
        return;
    }

    curl_close($curl);
    echo $response;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents("php://input");
    sendStockTransfer($json);
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>

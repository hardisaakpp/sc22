<?php

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
   CURLOPT_POSTFIELDS =>'{
  "cardCode": "",
  "comments": "sc22",
  "fromWarehouse": "DES-MZ",
  "toWarehouse": "TOUT-PSC",
  "priceList": -2,
  "stockTransferLines": [
    {
      "itemCode": "IT00317",
      "quantity": 24,
      "warehouseCode": "TOUT-PSC",
      "baseEntry": 311410,
      "baseLine": 1,
      "baseType": 1250000001
    },
    {
      "itemCode": "IT00647",
      "quantity": 12,
      "warehouseCode": "TOUT-PSC",
      "baseEntry": 311410,
      "baseLine": 12,
      "baseType": 1250000001
    }
  ]
}',
   CURLOPT_HTTPHEADER => array(
      'User-Agent: Apidog/1.0.0 (https://apidog.com)',
      'Content-Type: application/json',
      'Accept: /',
      'Host: 192.168.2.12:8086',
      'Connection: keep-alive'
   ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
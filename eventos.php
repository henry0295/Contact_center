<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://dashboard.360nrs.com/api/rest/sendings/1/reports/keys',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic YOUR_AUTH_TOKEN'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
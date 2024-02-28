<?php

function api_call($url, $headers, $payload=false) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  if ($payload) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response_raw = curl_exec($ch);
  curl_close($ch);
  return json_decode($response_raw, true);
}

?>
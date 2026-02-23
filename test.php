<?php
$user_id = '26022022907419439';
$page_access_token = 'EAAK5ePDtHgcBQlKZCRX5OZAiHArDkijN10cxiDM8z6fAxXQEkq6pCzCRVa8x4Xyv7gZAZARZCtQkSAzinBpwhcVGxMTjyy4KCw1FmikONpud32VZCZBfOZB8bq7tZCDmGoDkQerZCAgIl07o9QgD1rB5gSXCkWXpmwKKUpNz8SAY0Bcij9HWkAJDHG366qqSCxdZAXYArM3NOrp9QZDZD';
// ===============================

$url = "https://graph.facebook.com/{$user_id}?fields=name,first_name,last_name,profile_pic&access_token={$page_access_token}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo '<pre>';
print_r($data);
echo '</pre>';
?>

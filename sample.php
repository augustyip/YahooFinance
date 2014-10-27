<?php

require_once('YahooFinance.php');

echo '<pre>';

$stocks = array("0700.HK","0386.HK");

// YQL Query Parameters. Ref: https://developer.yahoo.com/yql/guide/yql_url.html
$options = array(
  'format' => 'json',
);

$yf = new YahooFinance($options);
$quote = $yf->quote($stocks);
print_r(json_decode($quote));

echo '</pre>';
?>

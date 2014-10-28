<?php

require_once('YahooFinance.php');

echo '<pre>';

$symbols = array("0700.HK","0386.HK");

$data = array(
  'table' => 'quote',
  'conditions' => array(
    array(
      'field' => 'symbol',
      'value' => $symbols,
    ),
  ),
);

$result = YahooFinance::query($data);

print_r(json_decode($result));

$result = YahooFinance::query(array('parameters' => array('q' => 'show tables')));

print_r(json_decode($result));

echo '</pre>';
?>

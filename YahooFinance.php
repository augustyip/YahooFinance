<?php

class YahooFinance {
  private $yql_url  = "http://query.yahooapis.com/v1/public/yql";

  // YQL Query Parameters. Ref: https://developer.yahoo.com/yql/guide/yql_url.html
  private $options = array(
    'format' => 'json',
    'env' => 'http://datatables.org/alltables.env',
  );

  private $format;

  public function __construct($options = array()) {
    
  }
}

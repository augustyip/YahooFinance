<?php

class YahooFinance {
  private $yql_url  = "http://query.yahooapis.com/v1/public/yql";

  // YQL Query Parameters. Ref: https://developer.yahoo.com/yql/guide/yql_url.html
  private $options = array(
    'env' => 'http://datatables.org/alltables.env',
  );

  public function __construct($options = array()) {
    $this->options = array_merge($this->options, $options);
  }

  public function quote($symbols) {
    if (is_string($symbols)) {
      $symbols = array($symbols);
    }

    $this->options['q'] = 'SELECT * FROM yahoo.finance.quote WHERE symbol IN ("' . implode('", "', $symbols) . '")';
    return $this->query();
  }

  private function query() {
    if (empty($this->options['q']))
      return;

    $url = $this->yql_url . '?' . $this->http_build_query($this->options);
    $curl = curl_init($url);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
  }

  private function http_build_query(array $query, $parent = '') {
    $params = array();

    foreach ($query as $key => $value) {
      $key = ($parent ? $parent . '[' . rawurlencode($key) . ']' : rawurlencode($key));

      // Recurse into children.
      if (is_array($value)) {
        $params[] = $this->http_build_query($value, $key);
      }
      // If a query parameter value is NULL, only append its key.
      elseif (!isset($value)) {
        $params[] = $key;
      }
      else {
        // For better readability of paths in query strings, we decode slashes.
        $params[] = $key . '=' . str_replace('%2F', '/', rawurlencode($value));
      }
    }

    return implode('&', $params);
  }
}

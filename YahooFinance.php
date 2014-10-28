<?php

class YahooFinance {
  private $yql_url  = "http://query.yahooapis.com/v1/public/yql";

  /**
   * YQL Query Parameters.
   * Ref: https://developer.yahoo.com/yql/guide/yql_url.html
   * @var array
   */
  private $options = array(
    'env'         => 'store://datatables.org/alltableswithkeys',
    'diagnostics' => FALSE,
  );

  private $table;
  private $conditions = array();

  public function __construct($options = array()) {
    $this->options = array_merge($this->options, $options);
  }

  public function quote($symbols) {
    $this->table = 'quote';
    $this->conditions = array(
      array(
        'field' => 'symbol',
        'value' => $symbols,
      ),
    );
    return $this->query();
  }

  public function quotes($symbols) {
    $this->table = 'quotes';
    $this->conditions = array(
      array(
        'field' => 'symbol',
        'value' => $symbols,
      ),
    );
    return $this->query();
  }

  private function prepare_yql() {
    if (empty($this->table)) {
      return FALSE;
    }
    else {
      $yql = 'SELECT * FROM yahoo.finance.' . $this->table;
    }

    if (!empty($this->conditions)) {
      foreach ($this->conditions as $key => $condition) {
        if ($key == 0){
          $yql .= ' WHERE ';
        }
        else {
          $yql .= ' AND ';
        }

        if (is_array($condition['value'])) {
          $yql .= $condition['field'] . ' IN ("' . implode('", "', $condition['value']) . '")';
        }
        else {
          $yql .= $condition['field'] . ' = "' . $condition['value'] . '"';
        }
      }
    }

    return $yql;
  }

  private function query() {
    $yql = $this->prepare_yql();

    if ($yql) {
      $this->options['q'] = $yql;
    }
    else {
      return FALSE;
    }

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

<?php

class YahooFinance {
  static $endpoint  = "http://query.yahooapis.com/v1/public/yql";

  // YQL Query Parameters, ref: https://developer.yahoo.com/yql/guide/yql_url.html
  static $parameters = array(
    'env'         => 'store://datatables.org/alltableswithkeys',
    'diagnostics' => FALSE,
    'format'      => 'json',
  );

  static function build_yql($table, $conditions = array()) {
    $yql = 'SELECT * FROM yahoo.finance.' . $table;

    if (!empty($conditions)) {
      foreach ($conditions as $key => $condition) {
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

  static function query($data) {

    if (!empty($data['parameters']['q'])) {
      return self::execute($data['parameters']);
    }

    if (empty($data['table']))
      return FALSE;

    $yql = self::build_yql($data['table'], $data['conditions']);

    $data['parameters']['q'] = $yql;
    return self::execute($data['parameters']);
  }

  static function execute($parameters) {
    $parameters = array_merge(self::$parameters, $parameters);
    $url = self::$endpoint . '?' . http_build_query($parameters);
    $curl = curl_init($url);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
  }
}

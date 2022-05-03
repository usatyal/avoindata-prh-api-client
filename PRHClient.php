<?php

/**
 *
 */
class PRHClient {
  static $API_ENDPOINT = "https://avoindata.prh.fi/bis/v1/";
  static $PATTERN = '/^\d{7}-\d{1}$/';
  public $businessID;
  static $serverError = 'Internal server error';

  public function isValidBusinessId($businessID) {
     return preg_match(self::$PATTERN, $businessID);
  }

  public function fetchCompanyInformation ($businessId) {
    return  $this -> getResponse($businessId);
  }

  private function constructFullPath ($businessID) {
    return  self::$API_ENDPOINT . $businessID;
  }

  private function getResponse ($businessID) {
    $ch = curl_init();
    $fullPath =  $this -> constructFullPath($businessID);
    curl_setopt($ch, CURLOPT_URL, $fullPath);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curlResponse = json_decode(curl_exec($ch), true);
    if (json_last_error() === JSON_ERROR_NONE) {
      $response = $curlResponse;
    } else {
      $response = self::$serverError;
    }
    curl_close($ch);

    return $response;
  }
}

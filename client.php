<?php

require("PRHClient.php");

// isolta businessid: $businessID = "1854047-8";

// get the id from the command line argument
$businessID = getopt("f:")["f"];

$client = new PRHClient();

// Exit if invalid business id
if (!($client -> isValidBusinessId($businessID))) {
  exit("Invalid business id \n");
} else {
  // set businessid for the client
  $client->businessID = $businessID;
}

$responseData = $client -> fetchCompanyInformation($businessID);

// check if the response is valid and not empty
if ($responseData === $client::$serverError) {
  exit($client::$serverError);
} elseif(empty($responseData['results'])) {
  exit('No result found for this ID');
} else {
  $results = $responseData['results'];
}

// so that they do not end up as null
$name = '';
$street = '';
$postCode = '';
$city = '';
$businessLines = '';
$businessLinesCode = '';
$webAddress = '';

foreach ($results as $key => $value) {
  $name = $value['name'];
  $street = end($value['addresses'])['street'];
  $postCode = end($value['addresses'])['postCode'];
  $city = end($value['addresses'])['city'];

  // if not Finnish then look for others
  foreach ($value['businessLines'] as $business => $line) {
    if ($line['language'] === 'FI' && !empty($line['name'])) {
      $businessLines = $line['name'];
      $businessLinesCode = $line['code'];
      break;
    } elseif ($line['language'] === 'EN' && !empty($line['name'])) {
      $businessLines = $line['name'];
      $businessLinesCode = $line['code'];
      break;
    } elseif ($line['language'] === 'SE' && !empty($line['name'])) {
      $businessLines = $line['name'];
      $businessLinesCode = $line['code'];
    }
  }

  foreach ($value['contactDetails'] as $contact => $detail) {
    if ($detail['type'] === 'Kotisivun www-osoite' && !empty($detail['value'])) {
      $webAddress = $detail['value'];
      break;
    } elseif ($detail['type'] === 'www-adress' && !empty($detail['value'])) {
      $webAddress = $detail['value'];
      break;
    } elseif ($detail['type'] === 'Website address' && !empty($detail['value'])) {
      $webAddress = $detail['value'];
    }
  }

  // values to print
  $requiredFields[] = array(
    'name' => !is_null($name) && !empty($name) ? $name : "Name not found",
    'street' => !is_null($street) && !empty($street) ? $street : "Steeet address not found",
    'postCode' => !is_null($postCode) && !empty($postCode) ? $postCode : "Postal code not found",
    'city' => !is_null($city) && !empty($city) ? $city: "City not found",
    'businessLine' => !is_null($businessLines) && !empty($businessLines) ? $businessLines : "Business line not found",
    'businessLinesCode' => !is_null($businessLinesCode) && !empty($businessLinesCode) ? $businessLinesCode : "Business line code not found",
    'webAddress' => !is_null($webAddress) && !empty($webAddress) ? $webAddress : "Web address not found",
  );
}

var_dump(json_encode($requiredFields, JSON_FORCE_OBJECT));

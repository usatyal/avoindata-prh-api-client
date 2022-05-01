<?php

require("PRHClient.php");

// $businessID = "1854047-8";

$businessID = getopt("f:")["f"];

$client = new PRHClient();
$client->businessID = $businessID;

if (!($client -> isValidBusinessId($businessID))) {
  exit("Invalid business id \n");
}


$responseData = $client -> fetchCompanyInformation($businessID);
$results = $responseData['results'];

if(empty($results)) {
  exit("No results found \n");
}

foreach ($results as $key => $value) {
  $name = $value['name'];
  $street = end($value['addresses'])['street'];
  $postCode = end($value['addresses'])['postCode'];
  $city = end($value['addresses'])['city'];
  $businessLines = end($value['businessLines'])['name'];
  $webAddress = end($value['contactDetails'])['value'];

  $requiredFields[] = array(
    'name' => $name,
    'street' => $street,
    'postCode' => $postCode,
    'city' => $city,
    'businessLine' => $businessLines,
    'webAddress' => $webAddress
  );
}

var_dump(json_encode($requiredFields, JSON_FORCE_OBJECT));

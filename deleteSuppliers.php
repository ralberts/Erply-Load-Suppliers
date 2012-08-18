<?php
ini_set('max_execution_time', 180);

session_start();


// include ERPLY API class
include('EAPI.class.php');

// Initialise class
$api = new EAPI();

// Configuration settings
$api->url = "https://s3.erply.com/api/";
$api->clientCode = "49102";
$api->username = "ryan.alberts@heartlineministries.org";
$api->password = "heliftsyouup";

$result = $api->sendRequest("getSuppliers", array("recordsOnPage" => 100));
$suppliers = json_decode($result, true);

echo '<pre>';

foreach ($suppliers["records"] as $key => $value) {
	
	if(strcmp($value['supplierType'],"PERSON") == 0) {
		print "Deleting... " . $value["firstName"] . " " . $value["lastName"] . " | " . $value["supplierID"] . "<br />";
		
		$deleteResponse = $api->sendRequest("deleteSupplier", array( "supplierID" => $value["supplierID"]));
		$deleteOutput = json_decode($deleteResponse, true);
		
		print_r($deleteOutput);		
	}
}

echo '</pre>';
?>
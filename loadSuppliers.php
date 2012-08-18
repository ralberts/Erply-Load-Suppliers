<?php
/**
 * Used for importing Suppliers into Erply from a CSV spreadsheet.
 *
 * The format should look something like this:
 *
 * Code | First & Last Name | GroupID number | Notes
 *
 * Example:
 * 100	Dieula Etienne	7	ED
 *
 */

ini_set('max_execution_time', 180);

session_start();

// include ERPLY API class
include ('EAPI.class.php');

// Initialise class
$api = new EAPI();

// Configuration settings
$api -> url = "https://s3.erply.com/api/";
$api -> clientCode = "<INSERT CLIENT CODE>";
$api -> username = "<INSERT USERNAME>";
$api -> password = "<INSERT PASSWORD>";

////////////////////////////////////
// Supplier Groups
////////////////////////////////////
function getSupplierGroups() {
	global $api;
	$supplierGroups = $api -> sendRequest("getSupplierGroups", array());
	$supplierGroupsOutput = json_decode($supplierGroups, true);

	print "<pre>";
	print_r($supplierGroupsOutput);
	print "</pre>";
}

////////////////////////////////////
// Load CSV
////////////////////////////////////
function loadCSV() {
	$csv = array();
	$lines = file('suppliers.csv', FILE_IGNORE_NEW_LINES);

	foreach ($lines as $key => $value) {
		$csv[$key] = str_getcsv($value);
	}

	echo '<pre>';
	print_r($csv);
	echo '</pre>';

	return $csv;
}

////////////////////////////////////
// Get Suppliers
////////////////////////////////////

function loadSuppliers() {
	global $api;
	// Get client groups from API
	// No input parameters are needed
	$result = $api -> sendRequest("getSuppliers", array("recordsOnPage" => 100));

	// Default output format is JSON, so we'll decode it into a PHP array
	$suppliers = json_decode($result, true);
	//print_r($suppliers);

	print "<pre>";
	foreach (loadCSV() as $key => $row) {
		$newCode = $row[0];
		list($newFirst, $newLast) = explode(" ", $row[1]);
		$newGroupID = $row[2];
		$newNote = $row[3];
		print "$newFirst | $newLast | $newCode | $newGroupID | $newNote <br />";

		$isFound = FALSE;
		$supplierId;

		foreach ($suppliers["records"] as $key => $value) {
			//print "Value code: " . strcmp($value["code"],$newCode) . " | " . $value["code"] . " | " . $newCode . " | <br />";

			if (isset($value["code"]) && strcmp($value["code"], $newCode) == 0) {
				$isFound = TRUE;
				$supplierId = $value["supplierID"];
				print "Found Code: $newCode for $newFirst with supplier id of $supplierId <br />";
				break;
			}
		}

		$saveSupplier;

		if ($isFound) {
			//Update supplier
			$saveSupplier = array("supplierID" => $supplierId, "firstName" => $newFirst, "lastName" => $newLast, "groupID" => $newGroupID, "notes" => $newNote, "code" => $newCode);
			print "Supplier will be *updated* with code: $newCode for $newFirst <br />\n";

		} else {
			//New Supplier
			$saveSupplier = array("firstName" => $newFirst, "lastName" => $newLast, "groupID" => $newGroupID, "notes" => $newNote, "code" => $newCode);
			print "New Supplier will be added with code: $newCode for $newFirst <br />\n";

		}

		$result = $api -> sendRequest("saveSupplier", $saveSupplier);
		$suppliers = json_decode($result, true);
		print_r($suppliers);

	}

	print "</pre>";

}

//getSupplierGroups(); //Need to use to figure out the groupId number

loadSuppliers();


?>
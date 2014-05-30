<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/common_3/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/bioflyer-editor2/bde-BioFlyers.php');

$BioFlyers = new BioFlyers();
// initialize our response array that will be returned as JSON upon script completion
$response_array = array();
// initialize our array for search results for when the user is looking for a specific bioflyer
$search_results = array();

// open connection with database
$con = new mysqli($db, $username, $password, 'sandboxuif');
// error out if connection cannot be established
if ($con->connect_errno) {
    $response_array['status'] = 'connect_err';
} else {
	###################################
	# SETUP DASHBOARD                                               
	###################################
	// intialize our associative array which will be structured like so: area_title => array(bf_titles)
	$abf_assoc = array();

	// get all area ids and set up keys in associative array
	if ($result = $con->query("SELECT id FROM bf_areas ORDER BY title ASC")) {
	    while ($row = $result->fetch_array()) {
        	$abf_assoc[$row[0]] = array();
    	}
	    // free result set
	    $result->close();
	}

	// get all bioflyer titles and build arrays of bioflyers that share the same areas.
	// then, associate the arrays with their respective areas in the associative array built above.
	if ($result = $con->query("SELECT area_id, title FROM bioflyers ORDER BY area_id ASC")) {
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			array_push($abf_assoc[$row['area_id']], $row['title']);
		}
		// free result set
	    $result->close();
	}

	// initialize our array of area names for duplicate checking later
	$areas = array();
	if ($result = $con->query("SELECT id, title FROM bf_areas ORDER BY title ASC")) {
		$areas = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$areas[$row['id']] = $row['title'];
		}
	} else { 
		$areas = array("-1" => "No areas found."); 
	}

	// initialize our array of bioflyer names for duplicate checking later
	$bioflyers = array();
	if ($result = $con->query("SELECT id, title FROM bioflyers ORDER BY title ASC")) {
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$bioflyers[$row[id]] = $row[title];
		}
	} else { 
		$bioflyers = array("-1" => "No bioflyers found."); 
	} 

	################################
	# CHECK FORM RESULTS 
    ###############################
	// return bioflyer data on select menu option click
	if ($_POST['form'] === 'getbf') {
		$response_array = $BioFlyers->getBioFlyer($_POST['bf_id']);
	// narrow down select menu options as search term is entered
	} elseif ($_POST['form'] == 'dbsearch') { 
		// query based on whether search term is provided
		if ($_POST['search_query'] === '') {
			// build query depending on whether area_id is provided
			if ($_POST['area_id'] != "-1") {
				$response_array = $BioFlyers->searchTitle(NULL, $_POST['area_id']);
			} else {
				$response_array = $BioFlyers->searchTitle(NULL);
			}
		} else {
			// build query depending on whether area_id is provided
			if ($_POST['area_id'] != "-1") {
				$response_array = $BioFlyers->searchTitle($_POST['search_query'], $_POST['area_id']);
			} else {
				$response_array = $BioFlyers->searchTitle($_POST['search_query']);
			}			
		} // end search query if/else 
	// add bioflyer to database
	} elseif ($_POST['form'] === 'addbioflyer') {
		if (in_array($_POST['title'], $abf_assoc[$_POST['area_id']])) {
			$response_array['status'] = 'bf_exists_err';
		} else {
			$response_array = $BioFlyers->addBioFlyer($_POST['area_id'], $_POST['title'], $_POST['body'], $_POST['file_under']);
		}
	// add area to database
	} elseif ($_POST['form'] === 'addarea') {
		if (in_array($_POST['title'], $areas)) {
			$response_array['status'] = 'area_exists_err';
		} else {
			$response_array = $BioFlyers->addArea($_POST['title'], $_POST['dir_url']);
		}

	// edit existing bioflyer
	} elseif ($_POST['form'] === 'editbioflyer') {
		if (in_array($_POST['title'], $abf_assoc[$_POST['area_id']]) && array_search($_POST['title'], $bioflyers) != $_POST['bf_id']) {
			$response_array['status'] = 'bf_exists_err';
		} else {
			$response_array = $BioFlyers->editBioflyer($_POST['area_id'], $_POST['title'], $_POST['body'], $_POST['file_under'], $_POST['bf_id']);
		}

	// edit existing area
	} elseif ($_POST['form'] === 'editarea') {
		if (in_array($_POST['title'], $areas)) {
			$response_array['status'] = 'area_exists_err';
		} else {
			$response_array = $BioFlyers->editArea($_POST['title'], $_POST['dir_url'], $_POST['area_id']);
		}

	// delete existing bioflyer
	} elseif ($_POST['form'] === 'deletebioflyer') {
		$response_array = $BioFlyers->deleteBioFlyer($_POST['bf_id']);
	// delete existing area
	} elseif ($_POST['form'] === 'deletearea') {
		$response_array = $BioFlyers->deleteArea($_POST['area_id']);
	} else {
		$response_array['status'] = 'else_err';
	} // END FORM IF/ELSE

} // END CHECK DB IF/ELSE


header('Content-type: application/json');
echo json_encode($response_array);

?>
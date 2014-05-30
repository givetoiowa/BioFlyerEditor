<?php
/*
Plugin Name: Bioflyer Editor
Description: Bioflyer editor for the MySQL illiterate and the lazy MySQL literates.
Version: 2.0
Authors: Sam Blumhardt / Addison Benzshawel
Authors URI: https://twitter.com/SamBlumhardt
             http://addison.im
License: GPL2
*/

// Hook for adding admin menus
add_action('admin_menu', 'add_bde_page');

// Action function for above hook
function add_bde_page() {
    // Add a new submenu under Settings:
    add_menu_page( 'Bioflyer Editor', 'Bioflyer Editor', 'delete_others_pages', 'bioflyers', 'bde_edit_page', '', 29);
}


// * ======= Start User Defined Helper Functions ======= *

// populate dropdown menu with area names from associative array
function dropdown_populate($arr, $arr_name) {
	if ($arr_name == "areas") {
		printf("<option value='-1'></option>");
		foreach($arr as $id=>$title) {
			printf("<option value='%s'>%s</option>", $id, ucwords($title));
		}
	} else {
		foreach($arr as $id=>$title) {
			printf("<option value='%s'>%s</option>", $id, $title);
		}
	}
}

function populate_alpha() {
    foreach (range('A', 'Z') as $l) {
        printf("<option value='%s'>%s</option>", $l, $l);
    }
}



// * ======= End User Defined Helper Functions ======= *

// bde_edit_page() displays the page content for the Staff Directory Editor settings submenu
function bde_edit_page() {
	
    // Must check that the user has the required capability
    if (!current_user_can('delete_others_pages'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // Get CSS and JS
    wp_register_style('bde', '/wp-content/plugins/bioflyer-editor2/bde-css.css');
    wp_enqueue_style('bde');
    wp_register_script('bde', '/wp-content/plugins/bioflyer-editor2/bde-js.js');
	wp_enqueue_script('bde');

	
	include($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/common_3/db_connect.php');	

	$con = new mysqli($db, $username, $password, 'sandboxuif');

	if ($con->connect_errno):
    	printf("Connect failed: %s\n", $con->connect_error);
    	exit;
	else:
		// Get array of area names
		if ($result = $con->query("SELECT id, title FROM bf_areas ORDER BY title ASC")) {

			$areas = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$areas[$row['id']] = $row['title'];
			}
		} else { $areas = array("-1" => "No areas found."); }
		// Get array of bioflyer names
		if ($result = $con->query("SELECT id, title FROM bioflyers ORDER BY title ASC")) {
			$bioflyers = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$bioflyers[$row['id']] = $row['title'];
			}
		} else { $bioflyers = array("-1" => "No bioflyers found."); }
?>

<div id="bde-menu" class="group">
   <ul>
      <li data-id="bde-add" class="tab"><a href="#">Add</a></li>
      <li data-id="bde-edit" class="tab"><a href="#">Edit</a></li>
      <li data-id="bde-delete" class="tab"><a href="#">Delete</a></li>
      <li class="title">Bioflyer Editor</li>
   </ul>
</div>
<div id="bde-main">
	<div id="bde-add" class="hidden-content">
		<h2>Add a bioflyer to an existing area or add a new area (e.g., Medicine, Engineering, Liberal Arts).</h2>
		<ul class="add-choose">
			<li><input type='radio' name='add-form-toggle' value='1' checked>Add Bioflyer</li>
			<li><input type='radio' name='add-form-toggle' value='2'>Add Area</li>
		</ul>
		<div class="add-form-1">
			<div class="bfa-msg" id="error-message"></div>
		   	<form name="bf-add" enctype="multipart/form-data" method="post" action="">
				<p>
					<label for="bf-area-add">Select an area:</label>
					<select name="bf-area-add" id="bf-area-add">
						<?php dropdown_populate($areas, "areas") ?>
					</select>
					<label for="bf-fu-add" style="margin-left:20px">File under:</label>
					<select name="bf-fu-add" id="bf-fu-add">
						<?php populate_alpha() ?>
					</select>
				</p>
				<p>
					<input type="text" name="bf-title-add" id="bf-title-add" placeholder="Enter title here" size="40" max="150">
				</p>
				<p>
					<?php wp_editor( $content, "bf-body-add" ); ?>
				</p>
				<p class="bf-submit-add">
					<input type="submit" name="Submit" class="button-primary bfa-submit" value="<?php esc_attr_e('Submit') ?>" />
				</p>
			</form>
		</div>
		<div class="add-form-2">
			<div class="aa-msg"></div>
			<form name="area-add" method="post" action="">
				<p>
					<input type="text" name="area-title-add" id="area-title-add" placeholder="Enter area title here" size="40" max="150">
				</p>
				<p class="area-submit-add">
					<input type="submit" name="Submit" class="button-primary aa-submit" value="<?php esc_attr_e('Submit') ?>" />
				</p>
			</form>
		</div>

	</div>
	<div id="bde-edit" class="hidden-content">
	   <h2>Edit a bioflyer in an existing area or edit an area.</h2>
		<ul class="edit-choose">
			<li><input type='radio' name='edit-form-toggle' value='1' checked>Edit Bioflyer</li>
			<li><input type='radio' name='edit-form-toggle' value='2'>Edit Area</li>
		</ul>
		<div class="edit-form-1">
			<div class="bfe-msg" id="error-message"></div>
			<form name="bf-edit" method="post" action="">
				<p style="font-size:1.2em">Find a bioflyer by entering in a name and area in the fields below and selecting a result from the list.</p>
				<p>
					<label for="bf-title-search">Name:</label>
					<input type="text" name="bf-title-search" id="bf-title-search" placeholder="Enter name here" size="40" max="150">
					<label for="bf-area-search">Area:</label>
					<select name="bf-area-search" id="bf-area-search">
						<?php dropdown_populate($areas, "areas") ?>
					</select>
				</p>
				<p>
					<select name="bf-edit-select" id="bf-edit-select" size="5">
						<?php dropdown_populate($bioflyers, "bioflyers") ?>
					</select>
				</p>
				<div id="bf-info-edit">
					<p style="margin-top:40px;">
						<label for="bf-area-edit">Select a new area:</label>
						<select name="bf-area-edit" id="bf-area-edit">
							<?php dropdown_populate($areas, "areas") ?>
						</select>
						<label for="bf-file-under" style="margin-left:20px">File under:</label>
						<select name="bf-file-under" id="bf-file-under">
							<?php populate_alpha() ?>
						</select>
					</p>
					</p>
					<p>
						<input type="text" name="bf-title-edit" id="bf-title-edit" placeholder="Enter title here" size="40" max="150">
					</p>
					<p>
						<?php wp_editor( $content, "bf-body-edit" ); ?>
					</p>
					<p class="bf-submit-edit">
						<input type="submit" name="Submit" class="button-primary bfe-submit" onSubmit="window.location.href='#bde-edit-submit-response'; return false;" value="<?php esc_attr_e('Submit') ?>" />
					</p>
				</div>
			</form>
		</div>
		<div class="edit-form-2">
			<div class="ae-msg" id="error-message"></div>
			<form name="area-edit" method="post" action="">
				<p>
					<label for="area-edit-select">Area:</label>
					<select name="area-edit-select" id="area-edit-select">
						<?php dropdown_populate($areas, "areas") ?>
					</select>
				</p>
				<p>
					<input type="text" name="area-title-edit" id="area-title-edit" placeholder="Enter new title here" size="40" max="150">
				</p>
				<p class="area-submit-edit">
					<input type="submit" name="Submit" class="button-primary ae-submit" value="<?php esc_attr_e('Submit') ?>" />
				</p>
			</form>
		</div>
	</div>
	<?php
		// Check to see if user is admin (only admins can delete areas)
		if( current_user_can('activate_plugins') ):
	?>
		<div id="bde-delete" class="hidden-content">
		   	<h2>Delete a bioflyer in an existing area or delete an area <br>
		   		(<span style="color:red">Warning:</span> this will delete all bioflyers associated with that area).</h2>
			<ul class="delete-choose">
				<li><input type='radio' name='delete-form-toggle' value='1' checked>Delete Bioflyer</li>
				<li><input type='radio' name='delete-form-toggle' value='2'>Delete Area</li>
			</ul>
			<div class="delete-form-1">
				<div class="bfd-msg" id="error-message"></div>
				<form name="bf-delete" method="post" action="">
					<p>
						<label for="bf-title-delete">Name:</label>
						<input type="text" name="bf-title-delete" id="bf-title-delete" placeholder="Enter name here" size="40" max="150">
						<label for="bf-title-delete">Area:</label>
						<select name="bf-area-delete" id="bf-area-delete">
							<?php dropdown_populate($areas, "areas") ?>
						</select>
					</p>
					<p>
						<select name="bf-delete-select" id="bf-delete-select" size="5">
							<?php dropdown_populate($bioflyers, "bioflyers") ?>
						</select>
					</p>
					<p class="bf-submit-delete">
						<input type="submit" name="Submit" class="button-primary bfd-submit" value="<?php esc_attr_e('Submit') ?>" />
					</p>
				</form>
			</div>
			<div class="delete-form-2">
				<div class="da-msg"></div>
				<form name="area-delete" method="post" action="">
					<p>
						<label for="area-title-delete">Area:</label>
						<select name="area-title-delete" id="area-title-delete">
							<?php dropdown_populate($areas, "areas") ?>
						</select>
					</p>
					<p class="area-submit-delete">
						<input type="submit" name="Submit" class="button-primary da-submit" value="<?php esc_attr_e('Submit') ?>" />
					</p>
				</form>
			</div>
		</div>
	</div>
<?php else: ?>
	<div id="bde-delete" class="hidden-content">
	   	<h2>Sorry only admins have this power.</h2>
	</div>
<?php
	endif; // end admin check if/else
endif; // end db connection else statement
} // end bde_edit_page()
?>
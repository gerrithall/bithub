<?
/**
 * Database.php
 * shorthand functions to make coding quicker
 *
 * copyright 2015 zcor
 */

$db = mysqli_connect(DBSERVER, DBUSER, DBPASS, DBPRIMARY) or die(file_get_contents("install.html"));
Gbl::store('db',$db);

if (!$db) {
    die('Could not connect: ' . mysql_error());
}

function query($query) {
	try {	
		$return = mysqli_query(Gbl::get('db'), $query); 
		if(mysqli_error(Gbl::get('db'))) {
			throw new Exception('Query error '.mysqli_error(Gbl::get('db')));
		}
	} catch (Exception $e) {
		Gbl::get('system_log')->write(LOG_LEVEL_ERROR, 'Fatal query error: ' . $e->getMessage());
		if(MODE == 'staging') {
			echo $e->getMessage();
			echo "\n<br>\n" . $query;
		}
		die("404");
	}

	return($return);
}

function q($query) {
	return(query($query));
}

# query_grab($get_query, $field)
# a little shorthand for grabbing a single desired variable from a unique query
# it gets to be a pain to type this stuff out over and over again.

function query_grab($get_query, $field = '') {
	//$assoc_query = mysqli_query(Gbl::get('db'), $query);
	$unique_query = mysqli_query(Gbl::get('db'), $get_query) or die($get_query." ".mysqli_error()."Query failed, please consult the webmaster");
	$unique_result = mysqli_fetch_assoc($unique_query);
	if($field) {
		return($unique_result[$field]);
	} else {
		return(array_shift($unique_result));
	}
}

function query_load($get_query) {
	$q = mysqli_query(Gbl::get('db'), $get_query) or die("Query failed, please consult the webmaster");
	$arr = array();
	while($r = fa($q)) {
		$arr[] = $r;
	}
	return($arr);
}

function query_assoc($query) {
	$assoc_query = mysqli_query(Gbl::get('db'), $query);
	return(mysqli_fetch_assoc($assoc_query));
}

function escape($x) {
	return mysqli_real_escape_string(Gbl::get('db'), $x); 
}
function query_count($count_query) {
	$return = mysqli_query(Gbl::get('db'), $count_query); 
	return(mysqli_num_rows($return));
}

function num_rows($query) {
	return(mysql_num_rows($query));
}

function fa($query) {
	return(mysqli_fetch_assoc($query));
}

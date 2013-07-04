<?php

// ini_set('display_errors','1');
// ini_set('display_startup_errors','1');
// error_reporting (E_ALL);
date_default_timezone_set ("Australia/Sydney");

$json_output = '';
echo '{ ';
$json_output .= ' "REQUEST_URI":"' . $_SERVER['REQUEST_URI'] . '",';

$intLink = mssql_connect("sqlserver-ip","user","password");
mssql_select_db("datbasename",$intLink);
if (!$intLink) {  die(' "errorMessage":"' . mssql_get_last_message() .'"}');} 

$query_table = substr($_SERVER['PATH_INFO'],1);
$json_output .= ' "PATH_INFO":"' . $query_table . '",';

// get all columns name
$query = mssql_query('SELECT name FROM syscolumns WHERE id=OBJECT_ID("'.$query_table.'");'
  ,$intLink);

if (!mssql_num_rows($query)) {
    die(' "errorMessage":"' . mssql_get_last_message() .'"}');
} else {
	$json_output .=  ' "optFields":[';
	
   while ($row = mssql_fetch_array($query, MSSQL_NUM)) {
   		$json_output .=  '"' . $row[0] . '",';
   		
        // ...
    }
    //remove the last commar
    $json_output = substr($json_output,0,-1);

    $json_output .= '],';
}

$reqFields ='*';
if (isset($_GET["reqFields"])){
	$reqFields = $_GET["reqFields"];
	$json_output .= ' "reqFields":"' . $reqFields . '",';
}

$whereClause ='';
if (isset($_GET['whereClause'])){
	$whereClause = $_GET['whereClause'];
	$json_output .= ' "whereClause":"' . $whereClause . '",';
}else{
	$query_string = $_SERVER['QUERY_STRING'];
	parse_str($query_string, $query_array);

	foreach ($query_array as $key => $value) {
		if (($key !='reqFields') && ($key !='whereClause')){
			$whereClause .=  $key . '=' . $value. ' AND ';
		}
	}
	$whereClause = substr($whereClause,0,-4);
	$json_output .= ' "whereClause":"' . $whereClause . '",';
	//$json_output .= ' "var_dump":"' . var_dump($query_array) . '",';
}	

$select_clause = 'SELECT '. $reqFields .' FROM '.$query_table .' ';
if ($whereClause !=''){
	$select_clause .= ' WHERE '. $whereClause;
}
$json_output .= ' "sql":"' . $select_clause . '",';


$query = mssql_query($select_clause );

if (mssql_num_rows($query)) {

	//get select column name
	$columnsName = array ();
	for($i = 0; $i < mssql_num_fields($query); $i++) {
	    $field_info = mssql_fetch_field($query, $i);
	    array_push($columnsName,  $field_info->name );
	}

	$json_output .=  ' "data":[ ';
   while ($row = mssql_fetch_array($query, MSSQL_NUM)) {
   		$json_output .=  '{';
   		foreach ($row as $key => $value) {
    		$json_output .=  '"'. $columnsName[$key] . '":"' . $value. '",';
		}

		$json_output = substr($json_output,0,-1);
		$json_output .=  '},';
    }

    //remove the last commar
    $json_output = substr($json_output,0,-1);

    $json_output .= '],';
}

echo substr($json_output,0,-1);

echo '}';


?>

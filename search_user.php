<?php
include "connect.php";
$term="";
if(isset($_GET['term'])) { $term=$_GET['term']; }
if($term!=""){
	$sql = mysql_query("SELECT  name, email, business FROM wp_mlw_results WHERE name LIKE '%$term%' OR business LIKE '%$term%' OR email LIKE '%$term%' GROUP BY name" );

	$results = array();
	while($row = mysql_fetch_assoc($sql))
	{
	   $results[] = array(
		  'name' => utf8_encode($row['name']),
		  'email' => utf8_encode($row['email']),
		  'business' => utf8_encode($row['business'])
	   );
	}
	echo json_encode($results);
}
?>
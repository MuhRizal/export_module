<?php
function init_report(){
	
}

function utf8ize($mixed) {
	if (is_array($mixed)) {
		foreach ($mixed as $key => $value) {
			$mixed[$key] = utf8ize($value);
		}
	} else if (is_string ($mixed)) {
		return utf8_encode($mixed);
		//return $mixed;
	}
	return $mixed;
}

function mb_unserialize($string) {
    $string = preg_replace_callback('/!s:(\d+):"(.*?)";!se/', function($matches) { return 's:'.strlen($matches[1]).':"'.$matches[1].'";'; }, $string);
    return unserialize($string);
}

function trimPoint($serialized,$fStr){
	$position = strpos($serialized, $fStr);
	$end_p = strpos($serialized, "}", $position);
	$previous_p = strrpos(substr($serialized, 0, $position), "{");
	$start = strrpos(substr($serialized, 0, $previous_p), ";");
	$plus=$previous_p-$start;
	$new_serialized=substr($serialized, $start+1, $end_p-$previous_p+$plus);
	$res=unserialize($new_serialized);
	return $res;
}
?>
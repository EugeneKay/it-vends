<?php
// It-Vends/functions.php
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//

function format($data) {
	global $formats, $text_seps;
	$format = post_get('format','text');
	$format = in_array($format, $formats) ? $format : 'text';
	
	switch($format) {
	case 'php':
		$return = var_export($data);
		break;
	case 'serial':
		$return = serialize($data);
		break;
	case 'json':
		header('Content-type: application/json');
		$return = json_encode($data);
		break;
	case 'text':
	default:
		header('Content-type: text/plain');
		$sep = post_get('sep','lf');
		$sep = array_key_exists($sep, $text_seps) ? $text_seps[$sep] : $text_seps['lf'];
		$return = is_array($data) ? implode( $sep, $data) : $data;
	}
	
	return $return;
}

function post_get($key, $default = null) {
	$search = array($_POST, $_GET);
	foreach( $search as $arr ) {
		if ( array_key_exists($key, $arr)) {
			return $arr[$key];
		}
	}
	return $default;
}

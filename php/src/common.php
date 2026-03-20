<?php
// common.php
// it-vends
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//

//
// Runtime Constants
//

// End-of-line
define('EOL', "\n");

// Max items to vend
define('ITEMLIMIT', 100);

// Request protocol
switch (@$_SERVER['SERVER_PORT']) {
case '443':
	define('PROTOCOL', 'https');
	break;
case '80':
default:
	define('PROTOCOL', 'http');
}


//
// Preloaded Variables
//

// Valid output formats
$formats = array('text', 'title', 'json', 'serial', 'php');

// Text separators
$text_seps = array(
	'cr'	=>	"\r",
	'lf'	=>	"\n",
	'crlf'	=>	"\r\n",
	'comma'	=>	',',
	'tab'	=>	"\t",
	'newline'	=>	"\n",
	'br'	=>	'<br />',
);


//
// Preparatory Actions
//

// Load items
require_once 'vendlist.php';


//
// Function Definitions
//

// arg()
//
// Load an argument from POST or GET
//
// $key: name of argument
// $default: what to return if nothing else is found
// Return value: value being sought out, or $default
function arg($key, $default = null) {
	// Where to look for arguments
	$arg = array($_POST, $_GET);
	
	// Look for the $key in each arg source
	foreach( $arg as $arr ) {
		// Check for the key in this arg source
		if ( array_key_exists($key, $arr)) {
			// We found it!
			return $arr[$key];
		}
	}
	// Send back the default if we've failed
	return $default;
}

// byte_size()
//
// Turn a byte-count into a human-readable quantity, using SI binary prefixes
//
// $b: number of bytes
// Return value: 
function byte_size($b = 0) {
	if ($b < 1024 ) {
		return $b . 'B';
	}
	if (($kb = $b / 1024) < 1024) {
		return round($kb, 0) . 'KiB';
	}
	if (($mb = $kb / 1024) < 1024) {
		return round($mb, 0) . 'MiB';
	}
	if (($gb = $mb / 1024) < 1024) {
		return round($gb, 0) . 'GiB';
	}
	if (($tb = $gb / 1024) < 1024) {
		return round($tb, 0) . 'TiB';
	}
	if (($pb = $tb / 1024) < 1024) {
		return round($pb, 0) . 'PiB';
	}
	if (($eb = $pb / 1024) < 1024) {
		return round($eb, 0) . 'EiB';
	}
	if (($zb = $eb / 1024) < 1024) {
		return round($zb, 0) . 'ZiB';
	}
	return round($zb / 1024, 0) . 'YiB';
}

// format()
//
// Format data for output in various types
//
// $data: Data to be formatted
// $format: Output format to use
// Return value: string containing the input formatted as desired
function format($data, $format = 'text') {
	// Use global variables for performance
	global $formats, $text_seps;
	
	// Ensure the format we're after is supported
	$format = in_array($format, $formats) ? $format : 'text';
	
	// Generate output based on format chosen
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
        case 'title':
                header('Content-type: text/html; Charset=UTF-8');
                $sep = arg('sep', 'lf');
                $sep = array_key_exists($sep, $text_seps) ? $text_seps[$sep] : $text_seps['lf'];
                $vend = is_array($data) ? implode( $sep, $data) : $data;
                $return = "<!DOCTYPE HTML>\n<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html;Charset=UTF-8\">\n<title>$vend</title>\n</head>\n</html>";
                break;
	case 'text':
	default:
		header('Content-type: text/plain');
		$sep = arg('sep', 'lf');
		$sep = array_key_exists($sep, $text_seps) ? $text_seps[$sep] : $text_seps['lf'];
		$return = is_array($data) ? implode( $sep, $data) : $data;
	}
	
	// Send back the generated output
	return $return;
}

// vend()
//
// Vend some items(or a single one)
//
// $qty: Number of items to return. If 0, send 1 item, but as string, not array
// $special: Rate at which to return special items(0=0%, 100=100%)
// Return value: 0-indexed array of items(minimum 1), OR string if $qty == 0
function vend( $qty = 0, $special = 10 ) {
	// Globalize variables for performance
	global $vendlist, $vendspecial;
	
	if ($qty == 0) {
		$c = 1;
	} else {
		$c = min($qty, ITEMLIMIT);
	}
	// Dole out items
	for($i = 0; $i < $c; $i++) {
		// Decide if we're gonna give out a special item
		if( $special && rand(1, 100) <= $special ) {
			// Pick a random item from the "special" list
			$items[] = $vendspecial[array_rand($vendspecial)];
		} else {
			// Pick a random item from the regular list
			$items[] = $vendlist[array_rand($vendlist)];
		}
	}
	// Send items out
	if ($qty == 0) {
		return $items[0];
	} else {
		return $items;
	}
}

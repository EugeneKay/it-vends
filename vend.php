<?php
// It-Vends/vend.php
//
// Copyright 2011 Eugene E. Kashpureff and Jeffrey C. Hoyt
//
// Consult the README file included with this program for License information.
//
require_once( "vendlist.php" );

$formats = array( 'text', 'json', 'serial', 'php' );

function format($data)
{
	global $formats;
	$format = array_key_exists( 'format', $_GET) ? $_GET['format'] : 'text';
	$format = in_array($format, $formats) ? $format : 'text';
	
	switch($format)
	{
		case 'php':
			return var_export($data);
		case 'serial':
			return serialize($data);
		case 'json':
			return json_encode($data);
		case 'text':
		default:
			return $data;
	}
}

if (isset( $_POST["action"] ) ) {
	$action = $_POST["action"];
}
elseif (isset( $_GET["action"] ) ) {
	$action = $_GET["action"];
}
else {
	$action = "vend";
}
switch ($action) {
case "give":
	echo "Item giving is currently not supported. Sorry";
	break;
case "inventory":
	echo implode(", ", $vendlist);
	break;
case "vend":
default:
	echo format($vendlist[array_rand($vendlist, 1)]);
	break;
}
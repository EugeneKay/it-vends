<?php
// It-Vends/vend.php
//
// Copyright 2011 Eugene E. Kashpureff and Jeffrey C. Hoyt
//
// Consult the README file included with this program for License information.
//
require_once( "vendlist.php" );

$formats = array( 'text', 'json', 'serial', 'php' );
$limit = 10;

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

function post_get($key, $default = null)
{
	$search = array($_POST, $_GET);
	foreach( $search as $arr )
	{
		if ( array_key_exists($key, $arr))
		{
			return $arr[$key];
		}
	}
	return $default;
}

$action = post_get('action', 'vend');
$count = post_get('count','1');
if ( is_numeric($count) )
{
	$count = (int)$count;
	$count > $limit and $count = $limit;
}
else
{
	$count = 1;
}
switch ($action) {
case "formats":
	echo format($formats);
	break;
case "give":
	echo "Item giving is currently not supported. Sorry";
	break;
case "inventory":
	echo implode(", ", $vendlist);
	break;
case "vend":
default:
	if ($count==1)
	{
		echo format($vendlist[array_rand($vendlist, 1)]);
	}
	else
	{
		$indicies = array_rand($vendlist, $count);
		$values = array();
		foreach($indicies as $index)
		{
			$values[] = $vendlist[$index];
		}
		echo format($values);
	}
	
	break;
}
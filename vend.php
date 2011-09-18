<?php
// It-Vends/vend.php
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//
require_once("functions.php");
require_once("vendlist.php");

$formats = array( 'text', 'json', 'serial', 'php' );
$limit = 10;

$text_seps = array(
	'cr'	=> 	"\r",
	'lf'	=>	"\n",
	'crlf'	=>	"\r\n",
	'comma'	=>	',',
	'newline'=>	"\n",
	'br'	=>	'<br />',
);

$action = post_get('action', 'vend');
$count = post_get('count','1');
if ( is_numeric($count) ) {
	$count = (int)$count;
	$count > $limit and $count = $limit;
} else {
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
	for($i=0; $i < $count; $i++) {
		$values[]=$vendlist[array_rand($vendlist)];
	}
	echo format($values);
	
	break;
}
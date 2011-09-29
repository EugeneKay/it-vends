<?php
// It-Vends/vend.php
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//

//  Load functions
require_once("functions.php");

// Load items
require_once("vendlist.php");

//
// Definitions
//


// Valid output formats
$formats = array( 'text', 'json', 'serial', 'php' );

// Max items to vend
$limit = 10;

// Text separators
$text_seps = array(
	'cr'	=> 	"\r",
	'lf'	=>	"\n",
	'crlf'	=>	"\r\n",
	'comma'	=>	',',
	'newline'=>	"\n",
	'br'	=>	'<br />',
);

// What are we doing?
$action = post_get('action', 'vend');
// How much would you like?
$count = post_get('count','1');
if ( is_numeric($count) ) {
	// No funny business
	$count = (int)$count;
	$count > $limit and $count = $limit;
} else {
	// Default
	$count = 1;
}

// What are we doing again?
switch ($action) {
case "formats":
	// List valid output formats
	echo format($formats);
	break;
case "give":
	// Load an item into the vendlist
	echo "Item giving is currently not supported. Sorry";
	break;
case "inventory":
	// List items in the machine
	echo implode(", ", $vendlist);
	break;
case "vend":
default:
	// Make with the items!
	for($i=0; $i < $count; $i++) {
		$values[]=$vendlist[array_rand($vendlist)];
	}
	// List 'em
	echo format($values);
	
	break;
}

// That's all, folks!

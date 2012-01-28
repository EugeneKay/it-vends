<?php
// It-Vends/vend.php
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//

//  Load functions
require_once("common.php");

require_once("vendlist.php");
//
// Definitions
//


// What are we doing?
$action = arg("action", "vend");
// How much would you like?
$count = (int) arg("count","1");
// How would you like it?
$format = arg("format", "text");

// What are we doing again?
switch ($action) {
case "formats":
	// List valid output formats
	echo format($formats, $format);
	break;
case "give":
	// Load an item into the vendlist
	echo format(array("Item giving is currently not supported. Sorry"), $format);
	break;
case "inventory":
	// List items in the machine
	echo format($vendlist, $format);
	break;
case "vend":
default:
	// IT VENDS!
	echo format(vend($count), $format);
	break;
}

// That's all, folks!

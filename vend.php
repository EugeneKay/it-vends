<?php
// It-Vends/vend.php

require_once( "vendlist.php" );
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
	echo $vendlist[array_rand($vendlist, 1)];
	break;
}

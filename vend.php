<?php
// It-Vends/vend.php


if (isset( $_POST["action"] ) ) {
	$action = $_GET["action"];
}
elseif (isset( $_GET["action"] ) ) {
	$action = $_POST["action"];
}
else {
	$action = "vend";
}
	{
switch ($action) {
	case "vend":
		echo $vendlist[array_rand($vendlist, 1)];
		break;
	default:
}

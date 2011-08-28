<?php
// It-Vends/vend.php
/*
// Copyright 2011 Eugene E. Kashpureff and Jeffrey C. Hoyt
//
// The full source code is available at http://github.com/eugenekay/it-vends
//
// It Vends is free software: you can redistribute it and/or modify it under the
// terms of the GNU General Public License as published by the Free Software 
// Foundation, either version 3 of the License, or (at your option) any later
// version.
// 
// It Vends is distributed in the hope that it will be useful, but WITHOUT ANY 
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
// A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with It Vends. If not, see <http://www.gnu.org/licenses/>.
*/
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

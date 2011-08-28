<?php
// It-Vends/index.php
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

switch ($_SERVER['SERVER_PORT']) {
case '443':
	define ('PROTOCOL', 'https');
	break;
case '80':
default:
	define('PROTOCOL', 'http');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<title>It Vends!</title>
		<base href="<?php echo PROTOCOL;?>://itvends.com/" />
		<link rel="stylesheet" type="text/css" href="css/itvends.css"/>
		<link rel="stylesheet" type="text/css" href="css/start/jquery-ui-1.8.5.custom.css"/>
		<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.14.js"></script>
		<script type="text/javascript">
		 $(document).ready(function() {
				$( "#vendbutton" ).button();
				$( "#vendbutton" ).click(function() { $('#awesome').submit(); });
		 });
		</script>
	</head>
	<body><center>
		<div id="vendit">
			<form action="/" id="awesome" method="post">
				<input name="action" value="vend" type="hidden"/>
				<a href="#" id="vendbutton"><span style="font-size:100px;">Vend!</span></a>
			</form>
			<center>
<?php
//
// IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS
//
require_once("vendlist.php");
switch (@$_POST["action"]) {
	case "vend":
		echo "<div class=\"itvends-overlay\"><div id=\"itvends\">IT VENDS!<div id=\"venditem\">".$vendlist[array_rand($vendlist, 1)]."</div></div></div>";
		break;
	default:
	}
?>
			</center>
		</div>
		
		</center>
		<div id="footer"><div>Code available on <a href="https://github.com/eugenekay/it-vends">GitHub</a> under the terms of the <a href="https://www.gnu.org/licenses/gpl.html">GPL</a></div></div>
	</body>
</html>

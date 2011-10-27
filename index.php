<?php
// It-Vends/index.php
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//

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
<html xmlns="http://www.w3.org/1999/xhtml"> 
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<title>It Vends!</title>
		<base href="<?php echo PROTOCOL;?>://<?php echo $_SERVER["HTTP_HOST"]?>/"/>
		<link rel="icon" type="image/png" href="favicon.ico"/>
		<link rel="stylesheet" type="text/css" href="css/itvends.css"/>
		<link rel="stylesheet" type="text/css" href="css/start/jquery-ui-1.8.5.custom.css"/>
		<script type="text/javascript" src="js/jquery-1.6.3.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.16.js"></script>
		<script type="text/javascript">
		 $(document).ready(function() {
				$( "#vendbutton" ).button();
				$( "#vendbutton" ).click(function() { $('#awesome').submit(); });
		 });
		</script>
	</head>
	<body><center>
		<div id="vendit">
				<a href="/?action=vend" id="vendbutton"><span style="font-size:100px;">Vend!</span></a>
			<center>
<?php
//
// IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS IT VENDS
//
require_once("vendlist.php");
switch (@$_GET["action"]) {
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

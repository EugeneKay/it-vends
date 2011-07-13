<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
        <title>It Vends!</title>
        <link rel="stylesheet" type="text/css" href="css/itvends.css"/>
        <link rel="stylesheet" type="text/css" href="css/start/jquery-ui-1.8.5.custom.css"/>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js"></script>
        <script type="text/javascript">
          $(document).ready(function() {
                $( "#vendbutton" ).button();
                $( "#vendbutton" ).click(function() { $('#awesome').submit(); });
          });
        </script>
    </head>
    <body><center>
        <div id="vendit">
            <form action="." id="awesome" method="post">
                <input name="action" value="vend" type="hidden"/>
                <!-- <input id="vendbutton" value="Vend!" type="submit"/> -->
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
    </body>
</html>

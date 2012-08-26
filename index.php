<?php
// index.php
// it-vends
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//

//  Load functions
require_once 'common.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<title>It Blends!</title>
		<base href="<?php echo PROTOCOL; ?>://<?php echo $_SERVER['HTTP_HOST']; ?>/blend"/>
		<link rel="icon" type="image/png" href="img/favicon.png"/>
		<link rel="stylesheet" type="text/css" href="blend/css/itvends.css"/>
		<link rel="stylesheet" type="text/css" href="css/start/jquery-ui-1.8.5.custom.css"/>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.16.js"></script>
		<script type="text/javascript" src="js/jquery-plugins.js"></script>
		<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
	window.supply = <?php echo json_encode(vend(10)); ?>;
	$( "#vendbutton" ).button();
	$( "#vendbutton").attr("href","#");
	$( "#vendbutton" ).click(function() {
		$("#venditem").text( window.supply.shift() );
		$("#itvends-overlay").show().doTimeout("itvends-timeout", 10000, "fadeOut", "1000");
		if ( window.supply.length < 10 ) {
			$.getJSON("/vend?action=vend&count=10&format=json", function(data) {
				while (data.length > 0) {
					window.supply.push(data.pop());
				}
			});
		}
	});	
});
/* ]]> */
		</script>
	</head>
	<body>
		<div id="vendit">
			<a href="/blend/?action=vend" id="vendbutton"><span>Blend!</span></a>
		</div>
		<div id="itvends-overlay" class="<?php if (arg('action', FALSE) != 'vend') { echo 'hidden'; } ?>">
			<div id="itvends">IT BLENDS!</div>
			<div id="venditem"><?php echo vend(); ?></div>
		</div>
		<div id="footer"><div>Code available on <a href="https://github.com/cr5315/it-blends">GitHub</a> under the terms of the <a href="https://www.gnu.org/licenses/gpl.html">GPL</a></div></div>
	</body>
</html>

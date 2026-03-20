<?php
// index.php
// it-vends
//
// Copyright 2011 by It Vends Authors. Consult the README file included with
// this program for further information.
//


//
// Benchmarking
//

// Time of execution start
$bench['start'] = microtime(TRUE);

// Pick a display method: 'echo', 'hidden', or 'header'
$bench['method'] = 'header';


//
// Preparation
//

//  Load functions
require_once 'common.php';

//
// Runtime
//
header('Content-type: text/html; Charset=UTF-8');
?>
<!DOCTYPE html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<title>It Vends!</title>
		<base href="<?php echo PROTOCOL; ?>://<?php echo $_SERVER['HTTP_HOST']; ?>/"/>
		<link rel="icon" type="image/png" href="img/favicon.png"/>
		<link rel="stylesheet" type="text/css" href="css/itvends.css"/>
		<link rel="stylesheet" type="text/css" href="css/start/jquery-ui-1.8.5.custom.css"/>
		<script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.23.js"></script>
		<script type="text/javascript" src="js/jquery-plugins.js"></script>
		<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
	window.supply = <?php echo json_encode(vend(10)); ?>;
	$("#vendbutton").button();
	$("#vendbutton").attr("href","#");
	$("#vendbutton").click(function() {
		$("#venditem").text(window.supply.shift());
		$("#itvends-overlay").show().doTimeout("itvends-timeout", 10000, "fadeOut", "1000");
		if (window.supply.length < 10) {
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
			<a href="/?action=vend" id="vendbutton"><span>Vend!</span></a>
		</div>
		<div id="itvends-overlay" class="<?php if (arg('action', FALSE) != 'vend') { echo 'hidden'; } ?>">
			<div id="itvends">IT VENDS!</div>
			<div id="venditem"><?php echo vend(); ?></div>
		</div>
		<div id="footer"><div>Code available on <a href="https://github.com/eugenekay/it-vends">GitHub</a> under the terms of the <a href="https://www.gnu.org/licenses/gpl.html">GPL</a></div></div>
	</body>
</html>
<?php

// Per-request benchmarking
if (isset($bench['start']) && is_float($bench['start'])) {
	// Calculate the execution time up to here.
	$bench['time'] = round(microtime(TRUE) - $bench['start'], 5) * 1000;
	
	// Calculate the amount of ram used
	$bench['memory'] = byte_size(memory_get_peak_usage());
	
	// Use the chosen output method
	switch ($bench['method']) {
	case 'echo':
		// Output the time at the end of the page
		echo EOL;
		echo $bench['time'], 'ms', EOL;
		echo $bench['memory'], EOL;
		break;
	case 'hidden':
	// Output the time, but inside of a comment.
		echo EOL;
		echo '<!-- ', $bench['time'], 'ms -->', EOL;
		echo '<!-- ', $bench['memory'], ' -->', EOL;
		break;
	case 'header':
	default:
		header('It-Vends-Execution-Time: ' . $bench['time'] . 'ms');
		header('It-Vends-Memory-Usage: ' . $bench['memory']);
	}
}

?>

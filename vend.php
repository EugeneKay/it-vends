<?php
// vend.php
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
// Definitions
//

//  Load functions
require_once 'common.php';


//
// Runtime
//

// What are we doing?
$action = arg('action', 'vend');
// How much would you like?
$count = (int) arg('count', '1');
// How would you like it?
$format = arg('format', 'text');

// What are we doing again?
switch ($action) {
case 'formats':
	// List valid output formats
	echo format($formats, $format);
	break;
case 'give':
	// Load an item into the vendlist
	echo format(array('Item giving is currently not supported. Sorry'), $format);
	break;
case 'inventory':
	// List items in the machine
	echo format($vendlist, $format);
	break;
case 'vend':
default:
	// IT VENDS!
	echo format(vend($count), $format);
	break;
}

// Give us a newline for sanity
echo EOL;


//
// Cleanup
//

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

// That's all, folks!

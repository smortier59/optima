<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

$max = 1000000;


//ATF::$analyzer->flag('temoin');	
//for ($i=0;$i<$max;$i++) {
//}
//ATF::$analyzer->end('temoin');	
//
//ATF::$analyzer->flag('sans isset undefined');	
//for ($i=0;$i<$max;$i++) {
//	if ($$i) {
//		// Rien
//	}
//}
//ATF::$analyzer->end('sans isset undefined');	
//
//ATF::$analyzer->flag('avec isset undefined');	
//for ($y=0;$y<$max;$y++) {
//	if (isset($$y) && $$y) {
//		// Rien
//	}
//}
//ATF::$analyzer->end('avec isset undefined');	
//
//
//for ($i=0;$i<$max;$i++) {
//	$$k = $i;
//	$$j = $i;
//}
//
//
//ATF::$analyzer->flag('sans isset defined');	
//for ($i=0;$i<$max;$i++) {
//	if ($$k) {
//		// Rien
//	}
//}
//ATF::$analyzer->end('sans isset defined');	
//
//ATF::$analyzer->flag('avec isset defined');	
//for ($i=0;$i<$max;$i++) {
//	if (isset($$j) && $$j) {
//		// Rien
//	}
//}
//ATF::$analyzer->end('avec isset defined');	

ATF::$analyzer->flag('exists defined toto');	
$fuck = "toto";
for ($i=0;$i<$max;$i++) {
	if ($fuck) {
		// Rien
	}
}

ATF::$analyzer->flag('not exists defined toto');	
for ($i=0;$i<$max;$i++) {
	if (!$fuck) {
		// Rien
	}
}

unset($fuck);
ATF::$analyzer->flag('exists undefined toto');	
$fuck = "toto";
for ($i=0;$i<$max;$i++) {
	if ($fuck) {
		// Rien
	}
}

ATF::$analyzer->flag('not exists undefined toto');	
for ($i=0;$i<$max;$i++) {
	if (!$fuck) {
		// Rien
	}
}

ATF::$analyzer->display();
echo "\n";
?>
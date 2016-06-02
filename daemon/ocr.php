<?

$cmd = 'ps -efa | egrep -e "[0-9] php '.__FILE__.'"';
$result = explode("\n",trim(`$cmd`));
if (count($result)>1) {
	exit(-1);
}

include(dirname(__FILE__)."/../global.inc.php");
include_once(dirname(__FILE__)."/../libs/ATF/libs/gdrive.class.php");
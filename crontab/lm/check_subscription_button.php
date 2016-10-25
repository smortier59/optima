<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");


// Vérification si le script est déjà en train de tourner, dans ce cas là, on attends.
$cmd = 'ps -efa | egrep -e "[0-9] php '.__FILE__.'"';
$result = explode("\n",trim(`$cmd`));
if (count($result)>1) {
	echo "Crontab deja en cours d'éxecution\n";
	exit(-1);
}


ATF::define("tracabilite",false);
log::logger("START","qjanon");

$URLs = array(
	"http://www.leroymerlin.fr/v3/p/produits/camera-connectee-hd-interieure-myfox-security-camera-e1401167727",
	"http://www.leroymerlin.fr/v3/p/produits/alarme-maison-sans-fil-connectee-myfox-home-alarm-e1401167726",
	"http://www.leroymerlin.fr/v3/p/produits/alarme-maison-sans-fil-evology-zen-serie-limitee-e1401132682",
);

function get_curl_remote_ips($fp) {
    rewind($fp);
    $str = fread($fp, 8192);
    $regex = '/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/';
    if (preg_match_all($regex, $str, $matches)) {
        return array_unique($matches[0]);  // Array([0] => 74.125.45.100 [2] => 208.69.36.231)
    } else {
        return false;
    }
}

while (true) {
	foreach ($URLs as $l=>$u) {
		$ch = curl_init($u);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$wrapper = fopen('php://temp', 'r+');
		curl_setopt($ch, CURLOPT_STDERR, $wrapper);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		$content = curl_exec($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$h = substr($content, 0, $header_size);
		$content = substr($content, $header_size);

		curl_close($ch);

		$ips = get_curl_remote_ips($wrapper);
		fclose($wrapper); 

		preg_match('/Server: (.*)/', $h, $server);
		preg_match('/X-Px: (.*)/', $h, $xpx);
		// log::logger($content,"qjanon");
		// Détecter le présence de la box .offre .big-offre
		$toFind1 = '<div class="offre  big-offre ">';

		$flagBigOffre = false;
		if (strpos($content,$toFind1)) {
			$flagBigOffre = true;
		} else {
			// log::logger("BIG OFFRE NOT FOUND","qjanon");
		}

		// Détecter le présence de la modal
		$toFind2 = '<div id="offre_popin_1"';

		$flagModal = false;
		if (strpos($content,$toFind2)) {
			// log::logger("MODAL FOUND","qjanon");
			$flagModal = true;
		} else {
			// log::logger("MODAL NOT FOUND","qjanon");
		}

		$toInsert = array(
			"url"=>$u,
			"date"=>date("Y-m-d H:i:s"),
			"headers"=>$h,
			"server"=>$server[1],
			"xpx"=>$xpx[1],
			"ip"=>implode(", ", $ips),
			"big_offre"=>$flagBigOffre,
			"modal"=>$flagModal,
			// "content"=>$content
		);

		ATF::log_btn()->insert($toInsert);
	}
	sleep(10);
}
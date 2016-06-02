<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

echo "[debut script]\n";
$compteur=0;

for($i=0;$i<1000;$i++){
	$mot=util::generateRandWord(64,"abcdefhjmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWYXZ,;:!?./*+-é\"'(-è\\çà)=@ù*µ£%€");
	//Cryptage
	$aes=new aes();
	$crypt= $aes->crypt($mot);
	$IV=$aes->getIV();
	$seed=$aes->getKey();
	$aes->endCrypt();
	//Decryptage
	$aes=new aes(false);
	$aes->setSeed($IV.$seed);
	$decrypt= $aes->decrypt($crypt);
	$aes->endCrypt();
	
	if($mot!=$decrypt){
		$compteur++;
		echo "\nMot : ".$mot." -> ".$crypt." -> ".$decrypt." IV : ".$IV." Seed : ".$seed."\n";
	}
	echo ".";
	//echo "Mot : ".$mot." -> ".$crypt." -> ".$decrypt." IV : ".$IV." Seed : ".$seed."\n";
}

echo "Différence=".$compteur."\n";
echo "[fin script]\n"; 

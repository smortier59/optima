<?

define("__BYPASS__",true);
include("../global.inc.php");

//on selectionne le contenu de la table `emailing_contact` dans la BDD
$bases="optima_absystech";
$query="SELECT `id_emailing_contact`,`date`,`opt_in`,`email`,`email_valide` FROM ".$bases.".emailing_contact ";
if($data=ATF::db()->sql2array(ATF::db()->query($query))){
//on parcours le resultat
	foreach($data as $k=>$v){
	//si le champ `opt_in`est à oui (si abonné)
		if($v["opt_in"]=="oui"){
			echo($v["email"]);
			//on verefie si le champ `email` est valide 
			if(verifyMailbox($v["email"])){
				echo"\temail valide\n";
				//si le champ est valide, on met à jour le champ `date` avec la date de derniere verifivation et on met le champ `email_valide` à oui.
				$query="UPDATE ".$bases.".emailing_contact SET `date`=NOW() ,`email_valide`='oui' WHERE `id_emailing_contact` ='".$v["id_emailing_contact"]."'";
				ATF::db()->query($query);
			}else{
				echo"\temail non valide\n";
				//sinon, on met à jour la date de derniere verification, on met `email_valide` à non et on desabonne le contact(opt_in=non)  
				$query="UPDATE ".$bases.".emailing_contact SET `date`=NOW() ,`opt_in`='non', `email_valide`='non' WHERE `id_emailing_contact` ='".$v["id_emailing_contact"]."'";
				ATF::db()->query($query);			
				//echo $query."\n";
			}
		}
	}
}
//----------------------------------------------------------------------------
function verifyMailbox($email,$mailAddress="no-reply@no-mail.com") {
	$before = microtime();
	$err = false;
	if (!preg_match('/([^\@]+)\@(.+)$/', $email, $matches)) {
		return false;
	}
	$user = $matches[1]; $domain = $matches[2];
	if(!function_exists('checkdnsrr')) return $err;
	if(!function_exists('getmxrr')) return $err;
	// Get MX Records to find smtp servers handling this domain
	if(getmxrr($domain, $mxhosts, $mxweight)) {
		for($i=0;$i<count($mxhosts);$i++){
			$mxs[$mxhosts[$i]] = $mxweight[$i];
		}
		asort($mxs);
		$mailers = array_keys($mxs);
	}elseif(checkdnsrr($domain, 'A')) {
		$mailers[0] = gethostbyname($domain);
	}else {
		return false;
	}
	// Try to send to each mailserver
	$total = count($mailers);
	$ok = 0;
	for($n=0; $n < $total; $n++) {
		$timeout = 5;
		$errno = 0; $errstr = 0;
		//creation socket et connection
		if(!($sock = fsockopen($mailers[$n], 25, $errno , $errstr, $timeout))) {
			continue;
		}
		$response = fgets($sock);//lecture de la reponse
		stream_set_timeout($sock, 5);
		$meta = stream_get_meta_data($sock);
		$cmds = array(
			"HELO localhost",
			"MAIL FROM: <$mailAddress>",
			"RCPT TO: <$email>",
			"QUIT",
		);
		if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
			break;
		}
		$success_ok = 1;
		foreach($cmds as $cmd) {
			fputs($sock, "$cmd\r\n");
			$response = fgets($sock, 4096);
			if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
				$success_ok = 0;
				break;
			}
		}
		fclose($sock);
		if($success_ok){
			$ok = 1;
			break;
		}
	}
	$after = microtime();
	// Fail on error
	if(!$ok) return false;
	// Return a positive value on success
	//return $after-$before;
	return true;
}	
?>
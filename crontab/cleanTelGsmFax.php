<?

	/** Script qui permet de faire le ménage dans la table emailing contact pour repartir sur de bonne base
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	define("__BYPASS__",true);
	include(dirname(__FILE__)."/../global.inc.php");

	echo "\nCONTACT\n";
	echo "\n\nTELEPHONE\n\n";
	ATF::contact()->q->reset()
							->where("tel","%.%","OR",false,"LIKE")
							->where("tel","% %","OR",false,"LIKE")
							->where("tel","%/%","OR",false,"LIKE")
							->where("tel","%-%","OR",false,"LIKE")
							->where("tel","%(0)%","OR",false,"LIKE")
							;
	ATF::contact()->q->setToString();						
	echo "QUERY = ".ATF::contact()->sa()."\n\n";
	ATF::contact()->q->unsetToString();						
	$r = ATF::contact()->sa();
	echo "Nombre = ".count($r)."\n\n";
	
	foreach ($r as $k=>$i) {
		$nTel = str_replace(" ","",$i['tel']);
		$nTel = str_replace(".","",$nTel);
		$nTel = str_replace("/","",$nTel);
		$nTel = str_replace("(0)","",$nTel);
		$nTel = str_replace("-","",$nTel);
		
		echo utf8_decode("Numéro ".$i['tel']." de ".$i['nom']." ".$i['prenom']." a remplacer par : ".$nTel."\n");	

		$u[$i['id_contact']]["id_contact"]=$i['id_contact'];
		$u[$i['id_contact']]["tel"]=$nTel;
		
	}
	
	echo "\n\nGSM\n\n";
	ATF::contact()->q->reset()
							->where("gsm","%.%","OR",false,"LIKE")
							->where("gsm","% %","OR",false,"LIKE")
							->where("gsm","%-%","OR",false,"LIKE")
							->where("gsm","%/%","OR",false,"LIKE")
							->where("gsm","%(0)%","OR",false,"LIKE")
							;
	ATF::contact()->q->setToString();						
	echo "QUERY = ".ATF::contact()->sa()."\n\n";
	ATF::contact()->q->unsetToString();						
	$r = ATF::contact()->sa();
	echo "Nombre = ".count($r)."\n\n";
	
	foreach ($r as $k=>$i) {
		$nTel = str_replace(" ","",$i['gsm']);
		$nTel = str_replace(".","",$nTel);
		$nTel = str_replace("/","",$nTel);
		$nTel = str_replace("(0)","",$nTel);
		$nTel = str_replace("-","",$nTel);
		
		echo utf8_decode("Numéro ".$i['gsm']." de ".$i['nom']." ".$i['prenom']." a remplacer par : ".$nTel."\n");	

		$u[$i['id_contact']]["id_contact"]=$i['id_contact'];
		$u[$i['id_contact']]["gsm"]=$nTel;
		
	}
	
	echo "\n\FAX\n\n";
	ATF::contact()->q->reset()
							->where("fax","%.%","OR",false,"LIKE")
							->where("fax","% %","OR",false,"LIKE")
							->where("fax","%-%","OR",false,"LIKE")
							->where("fax","%/%","OR",false,"LIKE")
							->where("fax","%(0)%","OR",false,"LIKE")
							;
	ATF::contact()->q->setToString();						
	echo "QUERY = ".ATF::contact()->sa()."\n\n";
	ATF::contact()->q->unsetToString();						
	$r = ATF::contact()->sa();
	echo "Nombre = ".count($r)."\n\n";
	
	foreach ($r as $k=>$i) {
		$nTel = str_replace(" ","",$i['fax']);
		$nTel = str_replace(".","",$nTel);
		$nTel = str_replace("/","",$nTel);
		$nTel = str_replace("(0)","",$nTel);
		$nTel = str_replace("-","",$nTel);
		
		echo utf8_decode("Numéro ".$i['fax']." de ".$i['nom']." ".$i['prenom']." a remplacer par : ".$nTel."\n");	

		$u[$i['id_contact']]["id_contact"]=$i['id_contact'];
		$u[$i['id_contact']]["fax"]=$nTel;
		
	}
	
	foreach ($u as $c=>$o) {
		ATF::contact()->u($o);	
	}
	unset($u);
	echo "\SOCIETE\n";
	echo "\n\nTELEPHONE\n\n";
	ATF::societe()->q->reset()
							->where("tel","%.%","OR",false,"LIKE")
							->where("tel","% %","OR",false,"LIKE")
							->where("tel","%/%","OR",false,"LIKE")
							->where("tel","%-%","OR",false,"LIKE")
							->where("tel","%(0)%","OR",false,"LIKE")
							;
	ATF::societe()->q->setToString();						
	echo "QUERY = ".ATF::societe()->sa()."\n\n";
	ATF::societe()->q->unsetToString();						
	$r = ATF::societe()->sa();
	echo "Nombre = ".count($r)."\n\n";
	
	foreach ($r as $k=>$i) {
		$nTel = str_replace(" ","",$i['tel']);
		$nTel = str_replace(".","",$nTel);
		$nTel = str_replace("/","",$nTel);
		$nTel = str_replace("(0)","",$nTel);
		$nTel = str_replace("-","",$nTel);
		
		echo utf8_decode("Numéro ".$i['tel']." de ".$i['societe']." a remplacer par : ".$nTel."\n");	

		$u[$i['id_societe']]["id_societe"]=$i['id_societe'];
		$u[$i['id_societe']]["tel"]=$nTel;
		
	}
	
	
	echo "\n\FAX\n\n";
	ATF::societe()->q->reset()
							->where("fax","%.%","OR",false,"LIKE")
							->where("fax","% %","OR",false,"LIKE")
							->where("fax","%-%","OR",false,"LIKE")
							->where("fax","%/%","OR",false,"LIKE")
							->where("fax","%(0)%","OR",false,"LIKE")
							;
	ATF::societe()->q->setToString();						
	echo "QUERY = ".ATF::societe()->sa()."\n\n";
	ATF::societe()->q->unsetToString();						
	$r = ATF::societe()->sa();
	echo "Nombre = ".count($r)."\n\n";
	
	foreach ($r as $k=>$i) {
		$nTel = str_replace(" ","",$i['fax']);
		$nTel = str_replace(".","",$nTel);
		$nTel = str_replace("/","",$nTel);
		$nTel = str_replace("(0)","",$nTel);
		$nTel = str_replace("-","",$nTel);
		
		echo utf8_decode("Numéro ".$i['fax']." de ".$i['societe']." a remplacer par : ".$nTel."\n");	

		$u[$i['id_societe']]["id_societe"]=$i['id_societe'];
		$u[$i['id_societe']]["fax"]=$nTel;
		
	}
	
	foreach ($u as $c=>$o) {
		ATF::societe()->u($o);	
	}
	

?>
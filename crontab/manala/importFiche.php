<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "manala";
include(dirname(__FILE__)."/../../global.inc.php");


$path = dirname(__FILE__)."/fiches/";
$pathOK = dirname(__FILE__)."/fiches/ok/";
$pathNOK = dirname(__FILE__)."/fiches/nok/";

//$path = $pathNOK;

foreach (scandir($path) as $k=>$i) {
	if ($i=="." || $i=="..") continue;
	echo "FN = ".$i;
	unset($fiche);
	$ext = pathinfo($i, PATHINFO_EXTENSION);

	if ($ext != "eml") continue;

	$dom = new DOMDocument();
	$ctn = file_get_contents($path."/".$i);
	// On tronque pour ne prendre que la partie HTML
	$ctn = substr($ctn,strpos($ctn,"text/html"),strlen($ctn));

	//echo "FGC apres encodage = ".strlen($ctn)."\n";
	$dom->loadHTML($ctn);
	//$dom->loadHTMLFile($path."/".$i);

	$elements = $dom->getElementsByTagName('td');
	//echo "NB Elements TD = ".$elements->length."\n";
	foreach ($elements as $element) {
	    $nodes = $element->childNodes;
	    foreach ($nodes as $node) {
			$val = mb_convert_encoding(quoted_printable_decode($node->nodeValue), 'UTF-8', 'ISO-8859-1');
		    //echo "VAL = ".$val."\n";
		    if (preg_match("^Question^",$val)) {
			    $fiche = explode("\n",$val);
		    }
	    }			
	    if ($fiche) break;



  	}
/**/
	$ctn = mb_convert_encoding(quoted_printable_decode($ctn), 'UTF-8', 'ISO-8859-1');
	//print_r($ctn);
	$dom->loadHTML($ctn);
	$hrefs = $dom->getElementsByTagName('a');

	foreach ($hrefs as $k=>$href) {
	  $a = mb_convert_encoding(quoted_printable_decode($href->getAttribute("href")), 'UTF-8', 'ISO-8859-1');
    //echo "HREF = ".$k." - ".$href->getAttribute("href");
    if (!$k) {
    	array_push($fiche,"photo_identite",$href->getAttribute("href"));
    } elseif ($k==1) {
    	array_push($fiche,"photo_pleine",$href->getAttribute("href"));
    } elseif ($k==2) {
      $ext2 = pathinfo($href->getAttribute("href"), PATHINFO_EXTENSION);
      //echo "\n".$href->getAttribute("href")."\n"."Extension : ".$ext2."\n";

    	array_push($fiche,"cv",$href->getAttribute("href"));
    }

	}
  	// On s'occupe de la fiche trouvée
  	unset($toInsert);
  	foreach ($fiche as $k_=>$i_) {
  		if (!$i_) continue;
      echo ".";
  		$i_ = trim($i_);
		  $val = utf8_decode(trim($fiche[$k_+1]));
      $val = str_replace(">","",$val);
      $val = str_replace("<","",$val);
  		switch ($i_) {
  			case "Identité":
  			case utf8_encode("Identité"):
  				$lastSpacePos = strrpos($val," ");
  				$prenom = substr($val,0,$lastSpacePos);
  				$nom = substr($val,$lastSpacePos+1);
  				$toInsert['nom'] = utf8_encode($nom);
  				$toInsert['prenom'] = $prenom;
  			break;
  			case "Adresse":
  				preg_match("`Adresse de la rue: (.*)`",$val,$m);
  				$pos = strpos($m[1],"nom de rue ; 2ème ligne: ");
				  $toInsert['adresse'] = utf8_encode(substr($m[1],0,$pos));

  				preg_match("`nom de rue ; 2ème ligne: (.*)`",$val,$m);
  				$pos = strpos($m[1],"Ville: ");
				  $toInsert['adresse_2'] = utf8_encode(substr($m[1],0,$pos));

  				if (strpos($val,"État/Province: ")) {
  	  				preg_match("`Ville: (.*)`",$val,$m);
  	  				$pos = strpos($m[1],"État/Province: ");
  					$toInsert['ville'] = utf8_encode(substr($m[1],0,$pos));

  	  				preg_match("`État/Province: (.*)`",$val,$m);
  	  				$pos = strpos($m[1],"Code postal: ");
  					$toInsert['province'] = utf8_encode(substr($m[1],0,$pos));
  				}else {
    					preg_match("`Ville: (.*)`",$val,$m);
  	  				$pos = strpos($m[1],"Code postal: ");
  					$toInsert['ville'] = utf8_encode(substr($m[1],0,$pos));
  				}

  				preg_match("`Code postal: (.*)`",$val,$m);
  				$pos = strpos($m[1],"pays: ");
				  $toInsert['cp'] = utf8_encode(substr($m[1],0,$pos));

  				preg_match("`pays: (.*)`",$val,$m);
  				$pos = strlen($val);
				  $toInsert['id_pays'] = "FR";//utf8_encode(substr($m[1],0,$pos));

			 break;
  			case "Date de naissance":
  				$dates = explode(" ",$val);
  				//$toInsert['real_date_naissance'] = $dates[1]." ".$dates[0]." ".$dates[2];
  				$toInsert['date_naissance'] = date("Y-m-d",util::myStrtotime($dates[1]." ".$dates[0]." ".$dates[2]));
  			break;  			
  			case "Lieu de naissance":
  				$toInsert['lieu_naissance'] = $val;
  			break;  			
  			case "Numéro de sécurité sociale":
  			case utf8_encode("Numéro de sécurité sociale"):
  				$toInsert['num_secu'] = $val;
  			break;  			
  			case "Nationalité":
  			case utf8_encode("Nationalité"):
  				$toInsert['nationalite'] = $val;
  			break;  			
  			case "Quelle est votre taille?":
  				$toInsert['taille'] = $val;
  			break;  			
  			case "Taille vêtements (Haut)":
  			case utf8_encode("Taille vêtements (Haut)"):
  				$toInsert['mensuration_haut'] = $val;
  			break;  			
  			case "Taille vêtements (Bas)":
  			case utf8_encode("Taille vêtements (Bas)"):
  				$toInsert['mensuration_bas'] = $val;
  			break;  			
  			case "Email":
  				$toInsert['email'] = $val;
  			break;  			
  			case "Téléphone":
  			case utf8_encode("Téléphone"):
  				$toInsert['tel'] = $val;
  			break;  			
  			case "Avez-vous le permis de conduire?":
          if (preg_match("^oui^",strtolower($val))) {
            $toInsert['permis'] = "oui";
          } else {
            $toInsert['permis'] = "non";
          }
  			break;  			
  			case "Avez-vous une voiture?":
  				$toInsert['voiture'] = strtolower($val);
  			break;  			
  			case "Parlez-vous Anglais?":
  				if ($val=="Niveau de base") {
  					$val = "base";
  				} elseif($val=="Bonne maîtrise écrite et orale") {
  					$val = "maitrise";
  				} elseif($val=="Bilingue") {
  					$val = "bilingue";
  				}
  				$toInsert['anglais'] = strtolower($val);
  			break;  			
  			case "Maîtrisez vous une autre langue étrangère?":
  			case utf8_encode("Maîtrisez vous une autre langue étrangère?"):
  				$toInsert['langues'] = $val;
  			break;  			
  			case "Pour quel type de mission souhaitez-vous postuler?":
  				$toInsert['type_mission'] = $val;
  			break;  			
  			case "photo_identite":
  			case "photo_pleine":
  			case "cv":
  				$toInsert[$i_] = $val;
  			break;  			

  			default:
  				//$toInsert[$k] = $val;
  				//echo $i."\n";
  			break;
  		}
  		
  	}
    try {
      ATF::personnel()->insert($toInsert);
      echo "DONE !\n";
      $archive = dirname(__FILE__)."/fiches/ok/";
      util::rename($path."/".$i,$archive."/".$i);
    } catch (error $e) {
      echo "ERREUR !\n";
      $error = dirname(__FILE__)."/fiches/nok/";
      util::rename($path."/".$i,$error."/".$i);
      log::logger($i,"manalaErrorImport");
      log::logger($fiche,"manalaErrorImport");
      log::logger($e->getCode()." - ".$e->getMessage(),"manalaErrorImport");
    }


}


echo "\n\n******************************************************************\n";
echo "NE PAS OUBLIER D'EXECUTER LE SQL SUIVANT !\n\n";

/*
UPDATE `personnel` SET `nom`=REPLACE(`nom`,'Ã©','é') WHERE `nom` LIKE '%Ã©%';
UPDATE `personnel` SET `prenom`=REPLACE(`prenom`,'Ã©','é') WHERE `prenom` LIKE '%Ã©%';
UPDATE `personnel` SET `ville`=REPLACE(`ville`,'Ã©','é') WHERE `ville` LIKE '%Ã©%';
UPDATE `personnel` SET `adresse`=REPLACE(`adresse`,'Ã©','é') WHERE `adresse` LIKE '%Ã©%';
UPDATE `personnel` SET `adresse_2`=REPLACE(`adresse_2`,'Ã©','é') WHERE `adresse_2` LIKE '%Ã©%';

UPDATE `personnel` SET `nom`=REPLACE(`nom`,'Ã¨','è') WHERE `nom` LIKE '%Ã¨%';
UPDATE `personnel` SET `prenom`=REPLACE(`prenom`,'Ã¨','è') WHERE `prenom` LIKE '%Ã¨%';
UPDATE `personnel` SET `ville`=REPLACE(`ville`,'Ã¨','è') WHERE `ville` LIKE '%Ã¨%';
UPDATE `personnel` SET `adresse`=REPLACE(`adresse`,'Ã¨','è') WHERE `adresse` LIKE '%Ã¨%';
UPDATE `personnel` SET `adresse_2`=REPLACE(`adresse_2`,'Ã¨','è') WHERE `adresse_2` LIKE '%Ã¨%';

UPDATE `personnel` SET `nom`=REPLACE(`nom`,'Ã¢','â') WHERE `nom` LIKE '%Ã¢%';
UPDATE `personnel` SET `prenom`=REPLACE(`prenom`,'Ã¢','â') WHERE `prenom` LIKE '%Ã¢%';
UPDATE `personnel` SET `ville`=REPLACE(`ville`,'Ã¢','â') WHERE `ville` LIKE '%Ã¢%';
UPDATE `personnel` SET `adresse`=REPLACE(`adresse`,'Ã¢','â') WHERE `adresse` LIKE '%Ã¢%';
UPDATE `personnel` SET `adresse_2`=REPLACE(`adresse_2`,'Ã¢','â') WHERE `adresse_2` LIKE '%Ã¢%';

UPDATE `personnel` SET `nom`=REPLACE(`nom`,'Ã»','û') WHERE `nom` LIKE '%Ã»%';
UPDATE `personnel` SET `prenom`=REPLACE(`prenom`,'Ã»','û') WHERE `prenom` LIKE '%Ã»%';
UPDATE `personnel` SET `ville`=REPLACE(`ville`,'Ã»','û') WHERE `ville` LIKE '%Ã»%';
UPDATE `personnel` SET `adresse`=REPLACE(`adresse`,'Ã»','û') WHERE `adresse` LIKE '%Ã»%';
UPDATE `personnel` SET `adresse_2`=REPLACE(`adresse_2`,'Ã»','û') WHERE `adresse_2` LIKE '%Ã»%';

UPDATE `personnel` SET `nom`=REPLACE(`nom`,'Ãª','ê') WHERE `nom` LIKE '%Ãª%';
UPDATE `personnel` SET `prenom`=REPLACE(`prenom`,'Ãª','ê') WHERE `prenom` LIKE '%Ãª%';
UPDATE `personnel` SET `ville`=REPLACE(`ville`,'Ãª','ê') WHERE `ville` LIKE '%Ãª%';
UPDATE `personnel` SET `adresse`=REPLACE(`adresse`,'Ãª','ê') WHERE `adresse` LIKE '%Ãª%';
UPDATE `personnel` SET `adresse_2`=REPLACE(`adresse_2`,'Ãª','ê') WHERE `adresse_2` LIKE '%Ãª%';

UPDATE `personnel` SET `nom`=REPLACE(`nom`,'Ã¯','ï') WHERE `nom` LIKE '%Ã¯%';
UPDATE `personnel` SET `prenom`=REPLACE(`prenom`,'Ã¯','ï') WHERE `prenom` LIKE '%Ã¯%';
UPDATE `personnel` SET `ville`=REPLACE(`ville`,'Ã¯','ï') WHERE `ville` LIKE '%Ã¯%';
UPDATE `personnel` SET `adresse`=REPLACE(`adresse`,'Ã¯','ï') WHERE `adresse` LIKE '%Ã¯%';
UPDATE `personnel` SET `adresse_2`=REPLACE(`adresse_2`,'Ã¯','ï') WHERE `adresse_2` LIKE '%Ã¯%';
*/

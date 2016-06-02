<?
/**
* Crontab permettant la MAJ de la BDD d'optima (champs: marque, description, poids, short_description)
* @autor Antoine MAITRE <amaitre@absystech.fr>
* @date 10/08/2012
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

/*Le code en commentaire, permet de remettre les champs à NULL, utile quand on fait de la merde dans la table*/


//$u = array(
//	"description"=>NULL
//	,"short_description"=>NULL
//	,"marque"=>NULL
//);
//
////Preparation du querier
//ATF::stock()->q
//			->reset()
//			->where(1, 1)
//			->addValues($u);
//
//try {
////	log::logger(ATF::stock()->q,"amaitre");
//	ATF::db()->update(ATF::stock());
//} catch (error $e) {
//	throw $e;
//}

ATF::tracabilite()->maskTrace("stock");		//Permets d éviter la surchage de la table tracabilité

/*Recupere les champs en vue d'une MAJ des marques dans la table stcok de la BDD d'Optima*/

$tab = array(				//Array regroupant toutes les marques recensées dans le stock
			"Kingston",
			"Lenovo",
			"Microsoft",
			"TPM",
			"Seagate",
			"WD",
			"Fujisu",
			"Samsung",
			"LSI", 
			"MSI", 
			"Cisco",
			"Sony",
			"D-Link",
			"Linksys",
			"IBM",
			"Mitel",
			"Acer",
			"LG",
			"Philips",
			"Hyundai",
			"Sampo",
			"Asus",
			"Belinea",
			"3com",
			"SonicWALL",
			"Toshiba",
			"NEC",
			"HP",
			"PNY");

ATF::stock()->q->reset()
			->addField("id_stock")
			->addField("ref")
			->addField("libelle")
			->addField('GROUP_CONCAT(stock.id_stock)',"tables_des_id")
			->whereIsNotNull("ref")
			->addGroup("ref");
	
$db = ATF::stock()->sa();	//Selection des champs en vue du stockage de la description et du poids sur la table
$count = $totalModifie = 0;
$total = count($db);
foreach ($db as $k=>$i) {	//Parcours de la table

	echo "\nStock ID ".$i["id_stock"]." : ".$i["stock"]." (".$count."/".$total.") \n";
	
	$update = array("description"=>NULL, "short_description"=>NULL, "poids"=>NULL, "marque"=>NULL);
	$test = false;
	$count++;
	$pourcent = $count*100/$total."%";

	foreach ($tab as $j) {	//Parcours du tableau des marques pour essayer d'avoir des concorcandes
		if (preg_match("/\b".$j."\b/i", $i["libelle"],$r)) {
			$update['marque'] = $j;

			$urls = "http://prf.icecat.biz/?shopname=openIcecat-url;smi=product;vendor=".$update['marque'].";prod_id=".urlencode($i["ref"]).";lang=fr"; //Test de différente URL pour avoir acces à icecat et ses données.
			sleep(0.5); //Pour eviter de se faire repérer par Icecat
			echo "APPEL DE L'URL : ".$urls."\n\n";
			$test = file_get_contents($urls); // Recup du code source de la page Web
	
	
			if (preg_match('#Désolé, pour ce produit, nous n\'avons pas trouvé d\'autres informations produit.<br>Si vous n\'êtes pas redirigés automatiquement, veuillez cliquer#', $test) == 0 && $test != false) {
				try {
					preg_match('#<td   class="ds_data" >(([0-9]+)|([0-9]+.[0-9]+) k{0,1}g)?</td>#si', $test, $matches); //Catch de la description
					print_r($matches);
					if (preg_match('#<td   class="ds_data" >(([0-9]+)|([0-9]+.[0-9]+) kg)?</td>#si', $test, $matches)) {
						$update["poids"] = $matches[3];
					} 
					elseif (preg_match('#<td   class="ds_data" >(([0-9]+)|([0-9]+.[0-9]+) g)?</td>#si', $test, $matches)) {
						$update["poids"] = $matches[3] /1000;
					}
					$doc = new DOMDocument(); 			// Code magique trouvé sur internet
					$doc->strictErrorChecking = FALSE;	// Permet l'utilisation du xpath
					$doc->loadHTML($test);				// A utiliser forcement
					$xml = simplexml_import_dom($doc);	//
					$result = $xml->xpath("body/table[@class='mainTable']/tr/td/table/tr/td/form/table/tr/td[@class='main']/p"); // Récupération des données mais cette fois ci, grâce a l'xpath, de la description
					$result_img = $xml->xpath('body/table[@class="mainTable"]/tr/td/table/tr/td/form/table/tr/td/table/tr/td[@class="image"]/table/tr/td/a/img');
					// Récupération des données mais cette fois ci, grâce a l'xpath de l'url de l'image
					}
				catch (error $e) {
						throw $e;	
				}
				if ($result_img[0]["src"] != false) {
					$result_img[0]["src"] = str_replace("low", "high", $result_img[0]["src"]);
					echo $result_img[0]["src"];
					$img = file_get_contents($result_img[0]["src"]); // Recuperation de l url de l image et stockage du code source dans img
					$tmp_tabl = explode(",", $i["tables_des_id"]);
					if (count($tmp_tabl) > 1) {
						foreach ($tmp_tabl as $img_id) { // Ecriture de fichiers image
							if (file_exists(ATF::stock()->filepath($img_id, "photo")) == false) {
								$tmp1 = ATF::util()->file_put_contents(ATF::stock()->filepath($img_id, "photo"), $img);
							}
						}
					} else {
						if (file_exists(ATF::stock()->filepath($i['id_stock'], "photo")) == false) {
							$tmp1 = ATF::util()->file_put_contents(ATF::stock()->filepath($i['id_stock'], "photo"), $img);
						}
					}
					if ($tmp1 == false) {
						echo "\nProblème de création de l'image.\n";	
					} else {
						echo "\nMAJ de l'image réussi.\n";
					}
				}
				
				/*Preparation du tableau en vue de l'update de la table*/
				foreach ($result as $value) {
					if ($value) {
						$value = str_replace("\t","", $value);
						$value = str_replace("\n","", $value);
						$update["description"] = $value;
						$update["short_description"] = $value;
					}
				}
				
				$result = $xml->xpath("body/table[@class='mainTable']/tr/td/table/tr/td/form/table/tr/td[@class='main']");
				
				foreach ($result as $value) {
					if ($value) {
						$value = str_replace("\t","", $value);
						$value = str_replace("\n","", $value);
						$update["description"] = $update["description"].$value;
						if ($update["short_description"] == NULL) {
							$update["short_description"] = $i["libelle"];
						}
					}
				}
				
				$result = $xml->xpath("body/table[@class='mainTable']/tr/td/table/tr/td/form/table/tr/td[@class='main']/span[2]");
				
				foreach ($result as $value) {
					if ($value) {
						$value = str_replace("\t","", $value);
						$value = str_replace("\n","", $value);
						$update["description"] = $update["description"]." ".$value;
					}
				}
				echo "MISE A JOUR:\n";
				print_r($update);
				$update["description"] = $update["description"]."\n\n".$urls;
				$tmp_tabl = explode(",", $i["tables_des_id"]);

				$j = 0;
				while ($tmp_tabl[$j]) {
					$tmp_tabl[$j] = "id_stock = ".$tmp_tabl[$j];
					$j++;
				}
				print_r($tmp_tabl);
				/*Update de la table*/
				ATF::stock()->q
							->reset()
							->addValues($update);
				if (count($tmp_tabl) > 1) {
					print_r($tmp_tabl);
					ATF::stock()->q->whereMerged($tmp_tabl, "OR");
				} else {
					ATF::stock()->q->where("id_stock", $i["id_stock"]);
				}
				if (preg_match('#^(\n\nhttp)#', $update["description"]) == 0)
				if (ATF::db()->update(ATF::stock())) {
					$totalModifie++;	
				}
				break;
			} else {
				echo $i["id_stock"].", une erreur a ete trouvee.\n";
			}
		}
	}
}

echo "FIN DU SCRIPT : ".$totalModifie." enregistrements mis a jour\n";

ATF::tracabilite()->unmaskTrace("stock");

?>
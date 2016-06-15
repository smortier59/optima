<?	

class cout_copie {	
	
	public function parse(){

		$host = "zimbra.absystech.net"; 
		$port = 143;
		$mail = "compteurlex@absystech.fr";
		$password = "az78qs45";

		ATF::imap()->init($host, $port, $mail, $password);	
		//ATF::imap()->init($host, $port, $mail, $password, "INBOX/tmp");
		if (ATF::imap()->error) {
			throw new errorATF(ATF::imap()->error);
		}

		$mails = ATF::imap()->imap_fetch_overview('1:*');
		foreach ($mails as $i => $mail) {
			$body = preg_replace("/\r|\n/", " ", ATF::imap()->returnBody($mail->uid));

			$search = array(
				"num_serie" => "/(N|n)uméro.?de.?série.{1,10}\S{1,20}/",
				"nb_page_couleur" => "/(C|c)ouleur.{1,10}[0-9]{1,6}/",
				"nb_page_mono" => "/(M|m)ono.{1,10}[0-9]{1,6}/"
			);
			$infos = array();
			foreach ($search as $searched => $reg) {
				preg_match($reg, $body, $z);
				//print_r($searched."\n");
				if($z){
					$splitted = explode(":", array_shift($z));
					$infos[$searched] = trim($splitted[1]);
				}

				if(!$infos[$searched]) $infos[$searched] = "";
			}


			if($infos['num_serie'] != ""){
				
				$q = "SELECT commande_ligne.*, commande.id_affaire  FROM commande_ligne INNER JOIN commande ON commande.id_commande = commande_ligne.id_commande WHERE commande_ligne.ref = '".$infos['num_serie']."'";
				$r = array_shift(ATF::db()->sql2array($q));
				if($r){
					$infos['id_affaire'] = $r['id_affaire'];
					
					$infos['date'] = date("Y-m-d H:i:s", strtotime($mail->date));

					$verif_date_querie = "SELECT * FROM cout_copie WHERE num_serie = '".$infos['num_serie']."'  AND DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s') = '".$infos['date']."'";
					$verif = ATF::db()->sql2array($verif_date_querie);

					if(!$verif){
						ksort($infos);
						$champs = implode(", ", array_flip($infos));
						$val = implode("', '", $infos);
						$insert_querie = "INSERT INTO cout_copie (".$champs.") VALUES ('".$val."')";

						$r = ATF::db()->sql2array($insert_querie);

					}
				}
			}

		}
		

	}

	public function get_all_facture(){
		ATF::facture()->q->reset()->where("id_affaire",103)->addOrder("date_effective");
		$all_fact = ATF::facture()->select_all();

		//print_r($all_fact);
	

		foreach ($all_fact as $key => $fact) {
			$ligne = array();

			ATF::facture_ligne()->q->reset()->where("id_facture",$fact["id_facture"]);
			$lignes = ATF::facture_ligne()->sa();

		}

		$facture = array("facture"=>$all_fact[0], "values_facture"=>array("produits"=>json_encode($lignes)));
		
		print_r($facture);




		/*
		$query_select_all_facture = "SELECT * FROM facture WHERE id_affaire = 103 ORDER BY date_effective DESC";
		$r = ATF::db()->sql2array($query_select_all_facture);

		$last_fact = array_shift($r);

		if($last_fact){



			$query_select_all_line = "SELECT * FROM facture_ligne WHERE id_facture = '".$last_fact['id_facture']."' AND serial = '75410232507HH'";

			$all_line = ATF::db()->sql2array($query_select_all_line);

			ATF::facture_ligne()->insert($item);

			print_r($all_line);
		}*/


	}


};
?>
<?
/**
* Classe facturation_fournisseur
* @package Optima
* @subpackage Cléodis
*/
class facturation_fournisseur extends classes_optima {

	public $user_facturation = array(16,91);

	function __construct() {
		$this->table="facturation_fournisseur";
		parent::__construct();

		$this->files["grille_BDCSociete"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_BDCCode"] = array("type"=>"pdf","no_upload"=>true);
		$this->files["grille_BDCDate"] = array("type"=>"pdf","no_upload"=>true);

		$this->foreign_key["id_fournisseur"] = "societe";

	}




}

class facturation_fournisseur_boulanger extends facturation_fournisseur {

	public $user_facturation = array(16,116);

	function __construct() {
		$this->table="facturation_fournisseur";
		parent::__construct();

		$this->colonnes['fields_column'] = array(
			 'facturation_fournisseur.id_societe'
			 ,'facturation_fournisseur.id_affaire'
			 ,'facturation_fournisseur.id_commande_fournisseur'
			 ,'facturation_fournisseur.date_periode_debut'
			 ,'facturation_fournisseur.date_periode_fin'
			 ,'facturation_fournisseur.montant'
		);


		$this->foreign_key["id_societe"] = "societe";
		$this->foreign_key["id_affaire"] = "affaire";
		$this->foreign_key["id_commande_fournisseur"] = "commande_fournisseur";

		$this->fieldstructure();

		$this->onglets = array('facturation_fournisseur_ligne');

		$this->no_insert = true;
		$this->no_update = true;
		$this->no_delete = true;
	}


	public function generate_echeancier($commande,$affaire,$affaires_parentes,$devis){
		try {


			// On récupère les lignes de ce pack ayant des frequence fournisseur
			ATF::commande_ligne()->q->reset()
				->where("id_commande", $commande->get("id_commande"), "AND")
				->where("frequence_fournisseur", "sans", "AND", null, "!=")
				->whereIsNotNull("frequence_fournisseur", "AND");

			$ligne_fourn_recurente = ATF::commande_ligne()->select_all();

			// On regroupe par fournisseur et frequence
			$regroupe = array();

			foreach ($ligne_fourn_recurente as $key => $value) {
				$regroupe[$value["id_fournisseur"]][$value["frequence_fournisseur"]][] = $value;
			}


			foreach ($regroupe as $id_fournisseur => $frequence) {
				$date_debut = $commande->get("date_debut");
				$date_fin   = $commande->get("date_evolution");

				log::logger("----------" , "mfleurquin");
				log::logger($date_fin , "mfleurquin");

				foreach ($frequence as $key => $lignes) {
					$date = $date_debut;
					$montant = 0;

					foreach ($lignes as $kl => $vl) {
						$montant += ($vl["prix_achat"] * $vl["quantite"]);
					}

					log::logger("===================" , "mfleurquin");
					log::logger("Frequence : ".$key , "mfleurquin");
					log::logger($date_fin , "mfleurquin");


					$i=0;
					while(strtotime($date) < strtotime($date_fin)){


						log::logger("--".$date , "mfleurquin");

						switch ($key) {
							case 'mois':
								$date_periode_fin = date("Y-m-d", strtotime($date. "+ 1 month"));
							break;

							case 'trimestre':
								$date_periode_fin = date("Y-m-d", strtotime($date. "+ 3 month"));
							break;

							case 'semestre':
								$date_periode_fin = date("Y-m-d", strtotime($date. "+ 6 month"));
							break;

							case 'an':
								$date_periode_fin = date("Y-m-d", strtotime($date. "+ 1 year"));
							break;

							default:
								throw new errorATF("Frequence non prise en charge", 1);
							break;

						}
						$date_periode_fin = date("Y-m-d", strtotime($date_periode_fin. "-1 day"));

						$id_facturation_fournisseur = $this->i(array(
								"id_affaire" => $affaire->get("id_affaire"),
								"id_societe" => $affaire->get("id_societe"),
								"id_fournisseur" => $id_fournisseur,
								"montant" => $montant,
								"date_debut_periode"=> $date,
								"date_fin_periode" => $date_periode_fin
						));


						foreach ($lignes as $kl => $vl) {
							ATF::facturation_fournisseur_ligne()->i(array(
								"id_facturation_fournisseur"=> $id_facturation_fournisseur,
								"id_produit"=>  $vl["id_produit"],
								"produit"=>  $vl["produit"],
								"quantite"=>  $vl["quantite"],
								"montant" =>  $vl["prix_achat"],
								"ref" =>  $vl["ref"],
								"id_commande_ligne" => $vl["id_commande_ligne"]
							));
						}

						//Si on est dans la periode, il faut générer la commande fournisseur
						if(strtotime(date("Y-m-d")) < strtotime($date_fin) && strtotime(date("Y-m-d")) > strtotime($date)){
							$this->create_commande_fournisseur($id_facturation_fournisseur);
						}

						$date = date("Y-m-d", strtotime($date_periode_fin. "+1 day"));

						$i++;

					}
				}

			}
		} catch (errorATF $e) {
			log::logger($e->getMessage() , "mfleurquin");
			throw new errorATF($e, 100);
		}
	}


	public function create_commande_fournisseur($id_facturation_fournisseur){

		if(!$id_facturation_fournisseur) throw new errorATF("Pas d'id facturation fournisseur", 100);

		try {
			$facturation = $this->select($id_facturation_fournisseur);

			ATF::facturation_fournisseur_ligne()->q->reset()->where("id_facturation_fournisseur", $id_facturation_fournisseur);
			$facturation_fournisseur_ligne = ATF::facturation_fournisseur_ligne()->select_all();


			$affaire = ATF::affaire()->select($facturation["id_affaire"]);
			$societe = ATF::societe()->select($affaire["id_societe"]);

			$commande = ATF::commande()->select(ATF::commande_ligne()->select($facturation_fournisseur_ligne[0]["id_commande_ligne"] , "id_commande"));

			$bon_de_commande = array (
	            "id_societe" => $societe["id_societe"],
	            "id_commande" => $commande["id_commande"],
	            "id_fournisseur" => $facturation["id_fournisseur"],
	            "commentaire" => "",
	            "id_affaire" => $facturation["id_affaire"],
	            "bon_de_commande" => $commande["commande"]." - ".$societe["code_client"],
	            "id_contact" => "",
	            "prix" => $facturation["montant"],
	            "prix_cleodis" => $facturation["montant"],
	            "tva" => $commande["tva"],
	            "etat" => "envoyee",
	            "payee" => "non",
	            "date" => date("d-m-Y"),
	            "destinataire" => $societe['societe'],
	            "adresse" =>  $societe['adresse'],
	            "adresse_2" =>  $societe['adresse_2'],
	            "adresse_3" => $societe['adresse_3'],
	            "cp" => $societe['cp'],
	            "ville" => $societe['ville'],
	            "id_pays" => $societe['id_pays']
	        );

	        if(!ATF::$usr->getID()){
	        	ATF::$usr->set('id_user',16);
	        }

			$lignes = "";
			foreach ($facturation_fournisseur_ligne as $key => $value) {
				$q = 1;
				while($q <= $value["quantite"]){
					$lignes .= ",".$value["id_commande_ligne"];
					$q++;
				}
			}
		    //Ligne de commande
		    $bdc = ATF::bon_de_commande()->insert(array("bon_de_commande"=> $bon_de_commande , "commandes"=>$lignes));

		    $this->u(array("id_facturation_fournisseur"=>$id_facturation_fournisseur, "id_bon_de_commande"=>$bdc));

		    return $bdc;

		}catch(errorATF $e){
			throw new errorATF($e, 100);

		}

	}


	public function generate_facturation(){

		ATF::db($this->db)->begin_transaction();

		$date_debut=date("Y-m-d",strtotime(date("Y-m-01")."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_debut."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));

		try{


			$this->q->reset()->addField("facturation_fournisseur.*")
							 ->addField("LTRIM(`societe`.`societe`)","ltrimsociete")
							 ->addField("LTRIM(`societe`.`code_client`)","ltrimcode_client")

							 ->addJointure("facturation_fournisseur","id_affaire","affaire","id_affaire",false,false,false,false,"INNER")
							 ->addJointure("facturation_fournisseur","id_affaire","commande","id_affaire",false,false,false,false,"INNER")
							 ->addJointure("facturation_fournisseur","id_societe","societe","id_societe",false,false,false,false,"INNER")


							 ->whereIsNull("id_bon_de_commande")
							 ->where("date_debut_periode",$date_debut, "AND","periode", "<=")
							 ->where("date_fin_periode",$date_fin, "AND","periode", ">=")

							 ->addCondition("`affaire`.`etat`","perdue","AND",false,"<>")
							 ->addCondition("`affaire`.`nature`","vente","AND",false,"<>")

							 ->addCondition("`commande`.`etat`","arreter","AND",false,"<>")
							 ->addCondition("`commande`.`etat`","arreter","AND",false,"<>")
							 ->addCondition("`commande`.`etat`","AR","AND",false,"<>");

			$facturations = $this->select_all();


			$cmd_contrat = array();


			foreach ($facturations as $key => $item) {
				$id_bdc = $this->create_commande_fournisseur($item["id_facturation_fournisseur"]);

				if($id_bdc){
					$bdc_genere=$this->formateTabBDCGenere($bdc_genere,$item,"BDC",$bdc);
					$tab=$this->incrementeFacture($tab,"cg");
				}
			}

			/*if($bdc_genere){
				log::logger("Envoi du mail à Cléodis des Bon de commandes générées ...",__CLASS__);
				$this->sendGrille($bdc_genere,$tab["cg"],$date_debut,$date_fin,"grille_","Grille de facturation des Bon de commandes",$s);
			}

			//Envoi d'un pdf contenant toutes les factures contrat
			log::logger("Envoi d'un pdf contenant toutes les factures contrat...",__CLASS__);
			$this->sendFactures($date_debut,$date_fin,$bdc_genere,"global_","Commandes fournisseur générées",$s);*/

			$return["bdc_genere"]=$bdc_genere;

			log::logger("Batch terminé.",__CLASS__);

			log::logger("Commit de la transaction",__CLASS__);

			ATF::db($this->db)->commit_transaction();

			return $return;

		}catch(errorATF $e){
			log::logger($e->getMessage(),__CLASS__);
			ATF::db($this->db)->rollback_transaction();
		}


	}


	function formateTabBDCGenere($tab,$item,$prefix,$bdc=false){
		if($bdc){
			$i=$bdc;
		}else{
			$i=$item;
		}

		//PDF avec les memes facture mais pour des tris differents ensuite dans sendGrille
		$tab[$prefix."Societe"][$item["ltrimsociete"].$item["id_facturation_fournisseur"]]=$i;
		$tab[$prefix."Code"][$item["ltrimcode_client"].$item["id_facturation_fournisseur"]]=$i;
		$tab[$prefix."Date"][$item["date_debut_periode"].$item["id_facturation_fournisseur"]]=$i;
		return $tab;
	}


	/**
	* Incrémente le tableau renseignant le nombre d'enregistrement commande générée (cg)
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $tab
	* @param string $type
	* @param boolean $envoye
	* @return array $tab
	*/
	function incrementeFacture($tab,$type){
		$tab[$type]++;
		return $tab;
	}

	/**
	* Permet de supprimer toutes les facturations fournisseur (contrat et/ou prolongation) d'une affaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_affaire
	* @param string $type
	* @return boolean
	*/
	function delete_special($id_affaire) {


		$this->q->reset()->Where("id_affaire",$id_affaire);


		if($facturation=$this->sa()){
			foreach($facturation as $key=>$item){
				$this->d($item["id_facturation_fournisseur"]);
			}
		}

		//Suppression du fichier de facturation
		ATF::affaire()->delete_file($id_affaire);

		return true;
	}

	public function sendGrille($facturer,$cg,$date_debut,$date_fin,$type,$texte,$s){
		//$emailGrille["email"]=ATF::societe()->select(246,"email");
		foreach ($this->user_facturation as $key => $value) {
			$emailGrille["email"] .= ATF::user()->select($value, "email").",";
		}
		$emailGrille["email"] = substr($emailGrille["email"], 0, -1);
		$emailGrille["texte"]=$texte." pour la periode du ".$date_debut."  au ".$date_fin.".";
		$emailGrille["objet"]=$texte." pour la periode du ".$date_debut."  au ".$date_fin.".";

		foreach($facturer as $key=>$item){

			log::logger($type , "mfleurquin");
			log::logger($key , "mfleurquin");

			//Tri des BDC par rapport au code, Societe ou date
			ksort($item);
			$item["reserve"]['cg']=$cg;
			$item["reserve"]['date_debut']=$date_debut;
			$item["reserve"]['date_fin']=$date_fin;

			ATF::facturation_fournisseur()->move_files($date_debut."_".$date_fin,$s,false,NULL,$type.$key,$item);
			$path[$key]=$type.$key;
		}
		ATF::affaire()->mailContact($emailGrille,$date_debut."_".$date_fin,"facturation_fournisseur",$path);

	}

}

?>
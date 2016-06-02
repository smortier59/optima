<?
class comite_test extends ATF_PHPUnit_Framework_TestCase {
	
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction();
		ATF::suivi()->q->reset()->where("suivi.date","2014-01-01 00:00:00","AND",false,"<");
		if($suivis = ATF::suivi()->select_all()){
			foreach ($suivis as $key => $value) {
				ATF::suivi()->d($value["id_suivi"]);
			}
		}
		ATF::db()->commit_transaction();

		$this->initUser();
		
		$this->devis["devis"]=array(
								 "id_societe" => $this->id_societe
								,"id_filiale" => 246
								,"date" => date("Y-m-d")
								,"id_contact" => $this->id_contact
								,"devis" => "Tu comite"
								,"type_contrat" => "lld"
								,"validite" => date("Y-m-d",strtotime(date("Y-m-d")."- 15 day"))
								,"id_opportunite" =>NULL
								,"tva" => "1.196"
								,"prix" => "14 000.00"
								,"prix_achat" => "4 641.00"
								,"marge" => "66.85"
								,"marge_absolue" => "9 359.00"
        );
		
		$this->devis["values_devis"] = array(
             "loyer" => '[{"loyer__dot__loyer":"1000","loyer__dot__duree":"14","loyer__dot__assurance":"","loyer__dot__frais_de_gestion":"","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":14000}]'
            ,"produits" => '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 19","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-19","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"9","devis_ligne__dot__id_fournisseur_fk":"1351"},{"devis_ligne__dot__produit":"XSERIES 226","devis_ligne__dot__quantite":"1","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"O2-SRV-226-001","devis_ligne__dot__prix_achat":"3113.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"AUDIOPTIC TRADE SERVICES|#ref=c0529cb381c6dcf43fc554b910ce02e9","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"5","devis_ligne__dot__id_fournisseur_fk":"1358"},{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17","devis_ligne__dot__quantite":"2","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"DEL-WRK-OPTGX520-17","devis_ligne__dot__prix_achat":"759.00","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"DELL|#ref=164a1c62808dc1a3af6f7d99051db73b","devis_ligne__dot__visibilite_prix":"visible","devis_ligne__dot__id_produit_fk":"8","devis_ligne__dot__id_fournisseur_fk":"1351"}]'
        );
		$this->id_devis=classes::decryptId(ATF::devis()->insert($this->devis));
		$this->id_affaire=ATF::devis()->select($this->id_devis,"id_affaire");
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
//		echo ATF::db()->numberTransaction().")";
		ATF::db()->rollback_transaction(true);
	}

	//	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  	
	public function test_defaultValue(){
		ATF::_r('id_devis',13419);
		$affaire = ATF::affaire()->select(13446);


		$this->assertEquals(6618, $this->obj->default_value("id_societe"),'valeur societe');
		$this->assertEquals(ATF::$usr->get('id_user'),$this->obj->default_value("id_user"),'valeur id_user');
		$this->assertEquals(date("d-m-Y"),$this->obj->default_value("date"),'valeur date');
		$this->assertEquals(8239,$this->obj->default_value("id_contact"),'valeur id_contact');
		$this->assertEquals(8028.00,$this->obj->default_value("prix"),'valeur prix');
		$this->assertEquals("Location presse d'impression",$this->obj->default_value("description"),'valeur description');
		$this->assertEquals(ATF::affaire()->getCompteTLoyerActualise($affaire),$this->obj->default_value("loyer_actualise"),'valeur loyer_actualise');
		$this->assertEquals("",$this->obj->default_value("activite"),'valeur activite');
		$this->assertEquals(84.16,$this->obj->default_value("pourcentage_materiel"),'valeur pourcentage_materiel');	
		$this->assertEquals(NULL ,$this->obj->default_value("suivi_notifie"),'valeur suivi_notifie');

		ATF::_r("id_comite", 38);
		$this->assertEquals(array(35,18) ,$this->obj->default_value("suivi_notifie"),'valeur suivi_notifie 2');

		
	}


	//	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  	
	public function test_can_update(){
		$id_comite=$this->obj->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$this->id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));
		$this->assertEquals(true, $this->obj->can_update($id_comite), "Can_update 1 error");

		try {
			$this->obj->can_update(38);
		} catch (Error $e) {
			$error = $e->getMessage();
		}
		$this->assertEquals("Impossible de modifier/supprimer ce Comité car il n'est plus en 'En attente'", $error, "Can_update 2 error");
	}	



	//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_getInfosFromCREDITSAFE(){
		
		$infos = array("societe" => 246);
		$r = $this->obj->getInfosFromCREDITSAFE($infos);	
		
		$this->assertEquals("04/2004",$r['date_creation'],"La date de création de société n'est pas bonne. Elle a changée sur Credit Safe ?");
		$this->assertEquals("31/12/2013",$r['date_compte'],"La date de création n'est pas bonne. Elle a changée sur Credit Safe ?");		
		$this->assertEquals("55",$r['note'],"Le note de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("50000",$r['limite'],"Le limite de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("Location et location-bail d'autres machines, équipements et biens matériels n.c.a. ",$r['activite'],"Le activite de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("2448354",$r['ca'],"Le ca de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("125707",$r['resultat_exploitation'],"Le resultat_exploitation de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("625240",$r['capital_social'],"Le capital_social de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("782743",$r['capitaux_propres'],"Le capitaux_propres de société n'est pas bon. Il a changé sur Credit Safe ?");
		$this->assertEquals("1347479",$r['dettes_financieres'],"Le dettes_financieres de société n'est pas bon. Il a changé sur Credit Safe ?");
	}

	//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	public function test_decision(){
		$id_comite=$this->obj->i(array("date"=>"01/01/2015","id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$this->id_affaire,"etat"=>"accepte","id_societe"=>$this->id_societe,"description"=>"Tu description"));
		$id_comite2=$this->obj->i(array("date"=>"01/01/2015","id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$this->id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));
		

		$this->obj->decision(array("date"=>"01/01/2015","comboDisplay"=>"refus_comite", "id"=>$id_comite2, "commentaire"=>"Commentaire"));
		$this->assertEquals("refuse",$this->obj->select($id_comite2, "etat"),"Error decision 1");

		$this->obj->decision(array("date"=>"01/01/2015","comboDisplay"=>"attente_retour", "id"=>$id_comite2, "commentaire"=>"Commentaire"));
		$this->assertEquals("en_attente",$this->obj->select($id_comite2, "etat"),"Error decision 2");

		$this->obj->decision(array("date"=>"01/01/2015","comboDisplay"=>"autre", "id"=>$id_comite2, "commentaire"=>"Commentaire"));
		$this->assertEquals("accepte",$this->obj->select($id_comite2, "etat"),"Error decision 3");
		$this->assertEquals("Commentaire",$this->obj->select($id_comite2, "commentaire"),"Error decision 4");
		$this->assertEquals("accord_non utilise",$this->obj->select($id_comite, "etat"),"Error decision 5");
		
		ATF::$msg->getNotices();
	}

	public function test_updateDate(){
		$id_comite=$this->obj->i(array("date"=>"01/01/2015","id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$this->id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description"));
		
		$infos = array("id_comite"=>$id_comite,
					   "key"=>"comite.validite_accord",
					   "value"=>date("Y-m-d")
					  );
		$this->obj->updateDate($infos);
		$this->assertEquals(date("Y-m-d"),$this->obj->select($id_comite, "validite_accord"),"Error validite_accord");

		$infos = array("id_comite"=>$id_comite,
					   "key"=>"comite.date_cession",
					   "value"=>date("Y-m-d")
					  );
		$this->obj->updateDate($infos);
		$this->assertEquals(date("Y-m-d"),$this->obj->select($id_comite, "date_cession"),"Error validite_accord");

		ATF::$msg->getNotices();
	}


	//	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  
	public function test_update(){
		$refresh = array();		
		
		$infos["date"]=date("Y-m-d");
		$infos["id_contact"]=$this->id_contact;
		$infos["id_refinanceur"]=1;
		$infos["id_affaire"]=$this->id_affaire;
		$infos["id_societe"]=$this->id_societe;
		$infos["description"]="Tu description";
		$infos["etat"]="en_attente";
		$id_comite=$this->obj->i($infos);
		$this->assertTrue(is_array($this->obj->select($id_comite)),'la comite ne s est pas bien inséré');

		$infos["comite"]["id_comite"]=$id_comite;
		$infos["comite"]["id_affaire"]=$this->id_affaire;
		$infos["comite"]["id_societe"]=$this->id_societe;
		$infos["comite"]["id_refinanceur"]=1;	
		$infos["comite"]["etat"]="accepte";
		$infos["comite"]["suivi_notifie"] = array(16,17);
		$this->obj->update($infos,$this->s,NULL,$cadre_refreshed,false,true);
		$etat = ATF::comite()->select($id_comite, "etat");
		$this->assertEquals("accepte",$etat, "Etat ne s'est pas mis à jour !");


		ATF::$msg->getNotices();
	}


	//	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  
	public function test_insert(){
		$data["comite"]["date"]=date("Y-m-d");
		$data["comite"]["id_contact"]=$this->id_contact;
		$data["comite"]["id_refinanceur"]=1;
		$data["comite"]["id_affaire"]=$this->id_affaire;
		$data["comite"]["id_societe"]=$this->id_societe;
		$data["comite"]["description"]="Tu description";		
		$data["comite"]["etat"]="accepte";	
		$data["comite"]["suivi_notifie"] = array(0=>35);
		$data["preview"]=true;
		$id_comite=$this->obj->decryptId($this->obj->insert($data,$this->s,NULL,$cadre_refreshed,false,true));

		unset($data["preview"]);
		$id_comite=$this->obj->decryptId($this->obj->insert($data,$this->s,NULL,$cadre_refreshed,false,true));
		$this->assertTrue(is_array($this->obj->select($id_comite)),'la comite 2 ne s est pas bien inséré');

		ATF::$msg->getNotices();

	}

	//	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  
	public function test_update2(){		
		$refresh = array();		
		
		$infos["date"]=date("Y-m-d");
		$infos["id_contact"]=$this->id_contact;
		$infos["id_refinanceur"]=1;
		$infos["id_affaire"]=$this->id_affaire;
		$infos["id_societe"]=$this->id_societe;
		$infos["description"]="Tu description";
		$infos["etat"]="en_attente";
		$id_comite=$this->obj->i($infos);
		$this->assertTrue(is_array($this->obj->select($id_comite)),'la comite ne s est pas bien inséré');

		$infos["comite"]["id_comite"]=$id_comite;
		$infos["preview"]=true;
		$infos["comite"]["description"]="Tu description 2";
		$this->obj->update($infos,$this->s,NULL,$cadre_refreshed,false,true);
		$this->assertEquals("Tu description 2",ATF::comite()->select($id_comite, "description"), "description s'est mis à jour");

		ATF::$msg->getNotices();
	}


};


class mock_mail extends mail {

	public function send(){
		return true;	
	}
};
?>
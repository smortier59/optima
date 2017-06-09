<?
/**
* Classe de test sur le module societe_cleodis
*/
class facture_fournisseur_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
	}

	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		$notices = ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}

	public function initFactureFournisseur(){
		$this->id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
		$this->id_commande=ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$this->id_affaire));

		$this->id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$this->id_commande,"produit"=>"produit a","id_produit"=>5,"quantite"=>1));
		$this->id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$this->id_commande,"produit"=>"produit b","id_produit"=>5,"quantite"=>1));

		$this->bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"ref" => "aaa"
								,"id_commande" => $this->id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $this->id_affaire
								,"bon_de_commande" => "AffaireTu"
								,"tva" =>"1.196"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"cp" => "69003"
								,"ville" => "LYON"
								,"prix" => "10.00"
								,"id_user" => $this->id_user
        );

		$this->id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->i($this->bon_de_commande));

		$this->id_facture_fournisseur=$this->obj->i(array("ref"=>"reftu","id_fournisseur"=>1351, "type"=>"achat","prix"=>"10.00","tva"=>"1.196","etat"=>"impayee","id_affaire"=>$this->id_affaire,"id_bon_de_commande"=>$this->id_bon_de_commande,"date_echeance"=>date("Y-m-d"),"date"=>date("Y-m-d")));
	}


	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_updateDate(){

		$this->initFactureFournisseur();

		$infos["value"]="undefined";
		$infos["key"]="date_paiement";
		$infos["id_facture_fournisseur"]="aa";

		$this->assertFalse($this->obj->updateDate($infos),"updateDate devrait renvoyer false puisqu'il y a pas d'id_facture_fournisseur");
		$this->obj->updateDate($infos);

		$infos["id_facture_fournisseur"]=$this->id_facture_fournisseur;
		$infos["value"]=date("Y-m-d");

		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_paiement' de l'enregistrement 'reftu' effectuée avec succès.",
										"title" => "Succès !",
										'timer' => null,
										'type' => 'success'
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("payee",
							$this->obj->select($this->id_facture_fournisseur,"etat"),
							"La demande refi ne passe pas en payee !");

		unset($infos["value"]);
		$this->obj->updateDate($infos);

		$this->assertEquals(array(
								array(
										"msg" => "Modification de 'date_paiement' de l'enregistrement 'reftu' effectuée avec succès.",
										"title" => "Succès !",
										'timer' => null,
										'type' => 'success'
										)
							),
							ATF::$msg->getNotices(),
							"Les notices ne sont pas cohérentes !");

		$this->assertEquals("impayee",
							$this->obj->select($this->id_facture_fournisseur,"etat"),
							"La demande refi ne passe pas en impayee !");
	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
	public function test_defaultValues(){

		$this->initFactureFournisseur();

		ATF::_r('id_bon_de_commande',$this->id_bon_de_commande);
		$this->assertEquals($this->bon_de_commande["bon_de_commande"]["id_fournisseur"],$this->obj->default_value("id_fournisseur"),'valeur id_fournisseur');
		$this->assertEquals($this->id_affaire,$this->obj->default_value("id_affaire"),'valeur id_affaire');
		$this->assertEquals("0.00000",$this->obj->default_value("prix"),'valeur prix');
		$this->assertEquals($this->bon_de_commande["bon_de_commande"]["tva"],$this->obj->default_value("tva"),'valeur tva');
		$this->assertNull($this->obj->default_value("default_value"),'valeur default_value');
	}

	public function test_insert(){

		$id_affaire=ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","date_garantie"=>date("Y-m-d")));
		$id_commande=ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire));

		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"produit"=>"produit a","quantite"=>1,"id_produit"=>5));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"produit"=>"produit b","quantite"=>1,"id_produit"=>6));

		$bon_de_commande["bon_de_commande"]=array(
								 "id_societe" => $this->id_societe
								,"ref" => "aaa"
								,"id_commande" => $id_commande
								,"id_fournisseur" => 1351
								,"id_affaire" => $id_affaire
								,"bon_de_commande" => "AffaireTu"
								,"tva" =>"1.196"
								,"date" => date("Y-m-d")
								,"destinataire" => "AXXES"
								,"adresse" => "26 rue de La Vilette - Part Dieu"
								,"cp" => "69003"
								,"ville" => "LYON"
								,"prix" => "10.00"
								,"id_user" => $this->id_user
        );

		$id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->i($bon_de_commande));

		ATF::facture_non_parvenue()->i(array("ref"=>"Facture non parvenue"
											,"prix"=>"10"
											,"tva"=>"1.196"
											,"etat"=>"impayee"
											,"id_affaire"=> $id_affaire
											,"id_bon_de_commande"=> $id_bon_de_commande
											,"date"=>date("Y-m-d")));


		$id_bon_de_commande_ligne1=ATF::bon_de_commande_ligne()->decryptId(ATF::bon_de_commande_ligne()->i(array("id_bon_de_commande"=>$id_bon_de_commande,"produit"=>"produit a","quantite"=>1,"id_commande_ligne"=>$id_commande_ligne1)));
		$id_bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->decryptId(ATF::bon_de_commande_ligne()->i(array("id_bon_de_commande"=>$id_bon_de_commande,"produit"=>"produit b","quantite"=>1,"id_commande_ligne"=>$id_commande_ligne2)));

		$id_parc1=ATF::parc()->i(array("id_societe"=>$this->id_societe,"id_affaire"=>$id_affaire,"libelle"=>"produit b","serial"=>"1111111111111","etat"=>"loue"));
		$id_parc2=ATF::parc()->i(array("id_societe"=>$this->id_societe,"id_affaire"=>$id_affaire,"libelle"=>"produit a","serial"=>"2222222222222","etat"=>"loue"));

		$facture_fournisseur["facture_fournisseur"]=array(
															"ref"=>"refFFTU",
															"id_fournisseur"=>1351,
															"prix"=>10.00,
															"tva"=>"1.196",
															"etat"=>"impayee",
															"id_affaire"=>$id_affaire,
															"id_bon_de_commande"=>$id_bon_de_commande,
															"date"=>date("Y-m-d"),
															"date_echeance"=>date("Y-m-d"),
															//"numero_cegid"=>null,
															"type"=>"achat",
															"periodicite"=>null,
															//"deja_exporte"=>"non"
														);

		try {
			$id_facture_fournisseur=classes::decryptId($this->obj->insert($facture_fournisseur,$this->s));
		} catch (errorATF $e) {
			$error1 = $e->getCode();
		}
		$this->assertEquals(877,$error1,'Erreur facture fournisseur sans ligne non declenchee');

		$facture_fournisseur["values_facture_fournisseur"]["produits"]='[{"facture_fournisseur_ligne__dot__produit":"produit a","facture_fournisseur_ligne__dot__quantite":"1","facture_fournisseur_ligne__dot__id_produit":"5","facture_fournisseur_ligne__dot__id_facture_fournisseur_ligne":"'.$id_bon_de_commande_ligne1.'"},{"facture_fournisseur_ligne__dot__produit":"produit b","facture_fournisseur_ligne__dot__quantite":"1","facture_fournisseur_ligne__dot__id_produit":"6","facture_fournisseur_ligne__dot__id_facture_fournisseur_ligne":"'.$id_bon_de_commande_ligne2.'"}]';
		$id_facture_fournisseur=classes::decryptId($this->obj->insert($facture_fournisseur,$this->s));

		ATF::facture_non_parvenue()->q->reset()->addCondition("id_facture_fournisseur",$id_facture_fournisseur);
		$facture_non_parvenue=ATF::facture_non_parvenue()->sa();

		$ff=array("id_facture_fournisseur"=>$this->obj->decryptId($id_facture_fournisseur),
					"ref"=>"refFFTU",
					"id_fournisseur"=>1351,
					"prix"=>"10.00",
					"tva"=>"1.196",
					"etat"=>"impayee",
					"id_affaire"=>$id_affaire,
					"id_bon_de_commande"=>$id_bon_de_commande,
					"date"=>date("Y-m-d"),
					"date_paiement"=>NULL,
					"date_echeance"=>date("Y-m-d"),
					//"numero_cegid"=>null,
					"type"=>"achat",
					"periodicite"=>null,
					"deja_exporte_immo"=>"non",
					"deja_exporte_achat"=>"non");

		$this->assertEquals($ff,$this->obj->select($id_facture_fournisseur),'La facture fournisseur ne s insère pas bien');

		ATF::facture_fournisseur_ligne()->q->reset()->addCondition("id_facture_fournisseur",$id_facture_fournisseur);
		$facture_fournisseur_ligne=ATF::facture_fournisseur_ligne()->sa();

		$ffl1=array("id_facture_fournisseur_ligne"=>$facture_fournisseur_ligne[0]["id_facture_fournisseur_ligne"],
					"id_facture_fournisseur"=>$id_facture_fournisseur,
					"ref"=>NULL,
					"produit"=>"produit a",
					"quantite"=>1,
					"prix"=>NULL,
					"id_bon_de_commande_ligne"=>$id_bon_de_commande_ligne1,
					"serial"=>NULL,
					"id_produit"=>5);

		$ffl2=array("id_facture_fournisseur_ligne"=>$facture_fournisseur_ligne[1]["id_facture_fournisseur_ligne"],
					"id_facture_fournisseur"=>$id_facture_fournisseur,
					"ref"=>NULL,
					"produit"=>"produit b",
					"quantite"=>1,
					"prix"=>NULL,
					"id_bon_de_commande_ligne"=>$id_bon_de_commande_ligne2,
					"serial"=>NULL,
					"id_produit"=>6);

		$this->assertEquals($ffl1,$facture_fournisseur_ligne[0],'La facture fournisseur ligne 1 ne s insère pas bien');
		$this->assertEquals($ffl2,$facture_fournisseur_ligne[1],'La facture fournisseur ligne 2 ne s insère pas bien');

		$fnp=array( "id_facture_non_parvenue"=>$facture_non_parvenue[0]["id_facture_non_parvenue"],
					"ref"=>"refFFTU-FNP",
					"id_facture_fournisseur"=>$id_facture_fournisseur,
					"prix"=>"-10.00",
					"tva"=>"1.196",
					"etat"=>"impayee",
					"id_affaire"=>$id_affaire,
					"id_bon_de_commande"=>$id_bon_de_commande,
					"date"=>date("Y-m-d 00:00:00"),
					'facturation_terminee' => 'non'
					);

		$this->assertEquals($fnp,$facture_non_parvenue[0],'La facture non parvenue ne s insère pas bien');

	}

	public function test_delete(){
		$this->initFactureFournisseur();
		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"impayee"));
		$ffDelete1["id"][]=$this->id_facture_fournisseur;
		$this->obj->delete($ffDelete1);
		$this->assertNull($this->obj->select($this->id_facture_fournisseur),'La FactureFournisseur ne se supprime pas 1');

		$this->id_facture_fournisseur=$this->obj->i(array("ref"=>"reftu","id_fournisseur"=>1351, "type"=>"achat","prix"=>"10.00","tva"=>"1.196","etat"=>"impayee","id_affaire"=>$this->id_affaire,"id_bon_de_commande"=>$this->id_bon_de_commande,"date_echeance"=>date("Y-m-d"),"date"=>date("Y-m-d")));
		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"impayee"));
		$ffDelete2=$this->id_facture_fournisseur;
		$this->obj->delete($ffDelete2);
		$this->assertNull($this->obj->select($this->id_facture_fournisseur),'La FactureFournisseur ne se supprime pas 2');

		$this->id_facture_fournisseur=$this->obj->i(array("ref"=>"reftu","id_fournisseur"=>1351, "type"=>"achat","prix"=>"10.00","tva"=>"1.196","etat"=>"impayee","id_affaire"=>$this->id_affaire,"id_bon_de_commande"=>$this->id_bon_de_commande,"date_echeance"=>date("Y-m-d"), "date"=>date("Y-m-d")));
		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"impayee"));
		$ffDelete3["id"]=$this->id_facture_fournisseur;
		$this->obj->delete($ffDelete3);
		$this->assertNull($this->obj->select($this->id_facture_fournisseur),'La FactureFournisseur ne se supprime pas 2');
	}

	public function test_can_delete(){
		$this->initFactureFournisseur();
		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"payee"));
		try {
			$this->obj->can_delete($this->id_facture_fournisseur);
		} catch (errorATF $e) {
			$error1 = $e->getCode();
		}
		$this->assertEquals(883,$error1,'Can_delete ne doit pas permettre la suppression d une facture payee');

		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"impayee"));

		$this->assertTrue($this->obj->can_delete($this->id_facture_fournisseur),'Can_delete doit paermettre la suppression d une facture impayee');
	}

	public function test_can_update(){
		$this->initFactureFournisseur();
		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"payee"));

		try {
			$this->obj->can_update($this->id_facture_fournisseur);
		} catch (errorATF $e) {
			$error1 = $e->getCode();
		}
		$this->assertEquals(883,$error1,'Can_delete ne doit pas permettre la suppression d une facture payee');

		$this->obj->u(array("id_facture_fournisseur"=>$this->id_facture_fournisseur,"etat"=>"impayee"));

		$this->assertTrue($this->obj->can_update($this->id_facture_fournisseur),'Can_delete doit paermettre la suppression d une facture impayee');
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function testAjoutTitre(){
        $Excel = new objet_excell();
        $sheets=array("refi"=>$Excel);

        $this->obj->ajoutTitre($sheets);

        $this->assertEquals("Type",$sheets['refi']->A1,"La valeur de la cellule A1 est incorrecte");
    }

    public function test_export_data(){
    	$this->obj->u(array("id_facture_fournisseur"=>5062, "type"=>"cout_copie"));
    	$q=ATF::_s("pager")->getAndPrepare("facture_fournisseurTest");
		$q->reset()->where("facture_fournisseur.date","2015-01-01","AND",false,">=")->where("facture_fournisseur.date","2015-02-01","AND",false,"<=")->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
    	ob_start();
    		$this->obj->export_data(array("onglet"=>"facture_fournisseurTest", "tu"=>true));
    	ob_end_clean();

    }


    public function test_export_cegid(){
    	$this->obj->u(array("id_facture_fournisseur"=>5062, "type"=>"cout_copie"));
    	$q=ATF::_s("pager")->getAndPrepare("facture_fournisseurTest");
		$q->reset()->where("facture_fournisseur.date","2015-01-01","AND",false,">=")->where("facture_fournisseur.date","2015-02-01","AND",false,"<=")->setView(array("order"=>$fields))->setCount();
		$this->obj->setQuerier($q);
    	ob_start();
    		$this->obj->export_cegid(array("onglet"=>"facture_fournisseurTest", "tu"=>true , "force"=>true));
    	ob_end_clean();

    }
};


class objet_excell {
		public function __construct(){
			$this->sheet=new object_sheet();
		}
		public function write($col,$valeur) {
			$this->$col=$valeur;
		}
	}
	class object_sheet {
		public function getColumnDimension($col){
			return $this;
		}
		public function setAutoSize($bool){
			$this->size=true;
		}
	}

?>
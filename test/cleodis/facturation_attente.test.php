<?
/**
* Classe de test sur le module societe_cleodis
*/
class facturation_attente_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {		
		$this->initUser();
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	public function test_envoye_facturation(){
		$id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("etat"=>"commande","date"=>date("Y-m-d"),"ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","nature"=>"affaire")));
		$id_loyer1=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>200,"duree"=>3,"assurance"=>20,"frais_de_gestion"=>2,"frequence_loyer"=>"mois")));
		$id_loyer2=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>100,"duree"=>1,"assurance"=>10,"frais_de_gestion"=>1,"frequence_loyer"=>"trimestre")));
		$id_loyer3=ATF::loyer()->decryptId(ATF::loyer()->i(array("id_affaire"=>$id_affaire,"loyer"=>50,"duree"=>1,"assurance"=>5,"frais_de_gestion"=>0,"frequence_loyer"=>"an")));
		
		$id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("ref"=>"refTu","id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"id_filiale"=>246,"prix"=>600,"date"=>date("Y-m-d"),"devis"=>"AffaireTu","type_contrat"=>"lld","date_accord"=>date("Y-m-d"),"etat"=>"gagne","id_contact"=>$this->id_contact,"id_affaire"=>$id_affaire,"tva"=>"1.196","loyer_unique"=>"non","prix_achat"=>0,"validite"=>date("Y-m-d"))));
		$id_devis_ligne=ATF::devis_ligne()->i(array("type"=>"portable","id_devis"=>$id_devis,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui","visibilite_prix"=>"visible"));
		$id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"commande"=>"AffaireTu","prix_achat"=>0,"prix"=>600,"date"=>date("Y-m-d"),"id_devis"=>$id_devis,"etat"=>"mis_loyer","id_user"=>$this->id_user,"tva"=>"1.196","clause_logicielle"=>"non","date_debut"=>date("2010-01-01"),"date_evolution"=>date("2010-12-31"),"type"=>"prelevement","id_affaire"=>$id_affaire)));
		$id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
		$id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));

		$commande = new commande_cleodis($id_commande);
		$devis = new devis($id_devis);
		$affaire=new affaire_cleodis($id_affaire);
		ATF::facturation()->insert_facturations($commande,$affaire,false,$devis,"contrat");

		ATF::facturation()->q->reset()->where("id_affaire", $id_affaire);
		$facturation = ATF::facturation()->select_all();

		$this->id_facture=ATF::facture()->decryptId(ATF::facture()->i(array(
                                                                        "ref"=>"refTU",
                                                                        "type_facture"=>"facture",
                                                                        "id_societe"=>$this->id_societe,
                                                                        "prix"=>120,
                                                                        "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
                                                                        "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),
                                                                        "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
                                                                        "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 4 month")),
                                                                        "date_periode_debut"=>date("Y-m-01"),
                                                                        "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
                                                                        "tva"=>"1.196",
                                                                        "id_user"=>$this->id_user,
                                                                        "id_commande"=>$d_commande,
                                                                        "id_affaire"=>$id_affaire
                                                                        )));

        $this->id_facture_ligne1=ATF::facture_ligne()->decryptId(ATF::facture_ligne()->i(array(
                                                                                            "id_facture"=>$this->id_facture,
                                                                                            "produit"=>"produitTU",
                                                                                            "quantite"=>1,
                                                                                            )));
        
        $this->id_facture_ligne2=ATF::facture_ligne()->decryptId(ATF::facture_ligne()->i(array(
                                                                                            "id_facture"=>$this->id_facture,
                                                                                            "produit"=>"produitTU2",
                                                                                            "quantite"=>2,
                                                                                            )));

        ATF::facturation()->u(array("id_facturation"=>$facturation[0]["id_facturation"], "id_facture"=>$this->id_facture));

        $data = array("mail"=>json_encode(array("email"=>"tu@absystech.fr","texte"=>"Votre facture pour la pu00e9riode 2015-09-01 - 2015-09-30")),
        			  "id_facture"=>$this->id_facture,
        			  "nom_table"=>"facture",
        			  "path"=>json_encode(array("facture"=>"fichier_joint")),
        			  "envoye"=>"non",
        			  "id_facturation"=>$facturation[0]["id_facturation"]
        			 );
        $id = $this->obj->insert($data);

      	$this->obj->envoye_facturation();

      	$this->assertEquals("oui", $this->obj->select($id, "envoye"), "Probleme dans envoye facturation ??");
      	$this->assertEquals("oui",  ATF::facturation()->select($facturation[0]["id_facturation"], "envoye"), "Probleme dans envoye facturation 2??");
	}

}
?>
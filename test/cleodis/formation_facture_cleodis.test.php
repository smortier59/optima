<?
class formation_facture_cleodis_test extends ATF_PHPUnit_Framework_TestCase {

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);	
		$this->environnementFormation();
		$infos = array(  "numero_dossier" => "123456789"
						,"thematique" => "La thématique de la formation"
						,"date" => date("Y-m-d")
						,"nb_heure" => "20"
						,"prix" => "50"
						,"id_owner"=>97
						,"id_contact"=>81
						,"id_societe" => $this->id_societe
						,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
						,"type"=>"normal"
				 );
		$dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
																				"formation_devis_ligne__dot__date_deb_matin"=> "8h",
																				"formation_devis_ligne__dot__date_fin_matin"=> "12h30",
																				"formation_devis_ligne__dot__date_deb_am"=> "14h",
																				"formation_devis_ligne__dot__date_fin_am"=> "18h"),
																		  array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
																		  		"formation_devis_ligne__dot__date_deb_matin"=> "8h",
																				"formation_devis_ligne__dot__date_fin_matin"=> "12h30")
																		)
																),
						"formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
																				 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
																				array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
																					  "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")																				
																				)
																		)
					);
		$this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));
		$this->id_commande_formation = ATF::formation_commande()->i(array(	"id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => date("Y-m-d") ));
	}
	
	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_construct(){
		new formation_facture();
	}
	
	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_insert(){
		$infos = array("id_formation_devis" => $this->id_devis_formation ,"id_societe" => $this->id_societe ,"type"=>"normale", "ref" => "La ref de facture", "prix"=> "2500", "num_dossier"=> "Le num de dossier", "date"=>date("Y-m-d") );
		$return = $this->obj->insert(array("preview" => true, "formation_facture" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$formation_facture = $this->obj->insert(array("formation_facture" => $infos));
		$this->assertNotNull($formation_facture,'formation_facture non créé 2 !');

	}


	/*
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function test_default_value(){
		 ATF::_r('id_formation_commande','');
		$this->assertEquals("", $this->obj->default_value("num_dossier"), "default_value 1 Incorrect");
		$this->assertEquals(date("Y-m-d"), $this->obj->default_value("date"), "default_value 2 Incorrect");

		ATF::_r('id_formation_commande',$this->id_commande_formation);
		$this->assertEquals($this->id_devis_formation, $this->obj->default_value("id_formation_devis"), "default_value 3 Incorrect");
		$this->assertEquals($this->id_societe, $this->obj->default_value("id_societe"), "default_value 4 Incorrect");
		$this->assertEquals("3000", $this->obj->default_value("prix"), "default_value 5 Incorrect");
		$this->assertEquals(date("Y-m-d"), $this->obj->default_value("date"), "default_value 6 Incorrect");
		$this->assertEquals("", $this->obj->default_value("num_dossier"), "default_value 7 Incorrect");
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function beginTransaction($codename){		
		ATF::db()->select_db("optima_".$codename);
    	ATF::$codename = $codename;
    	ATF::db()->begin_transaction(true);		
	}

	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	private function RollBackTransaction($codename){	
		ATF::db()->rollback_transaction(true);
        ATF::$codename = "cleodis";
        ATF::db()->select_db("optima_cleodis");
	}


	// @author Morgan FLEURQUIN <mfleurquin@absystech.fr> 
    public function test_export_special(){

    	ATF::$usr = new usr(16);

    	$infos = array("id_formation_devis" => $this->id_devis_formation ,"id_societe" => $this->id_societe ,"type"=>"normale", "ref" => "La ref de facture", "prix"=> "2500", "num_dossier"=> "Le num de dossier", "date"=>date("Y-m-d") );
		$return = $this->obj->insert(array("preview" => true, "formation_facture" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$formation_facture = $this->obj->insert(array("formation_facture" => $infos));
		$this->assertNotNull($formation_facture,'formation_facture non créé 2 !');

		$data = array();

		foreach ($this->obj->select_all() as $key => $value) {
			foreach ($value as $k => $v) {
				$data[$key]["formation_facture.".$k] = $v;
			}
			
		}
		
        $q=ATF::_s("pager")->create("gsa_test_lol");  
        ob_start();  
      	$this->obj->export_special(array('onglet'=>"gsa_lol_lol", "tu"=>"oui", "data"=>$data));
        ob_end_clean();           
    }


    // @author Morgan FLEURQUIN <mfleurquin@absystech.fr> 
    public function test_export_special2(){

    	ATF::$usr = new usr(16);

    	$infos = array("id_formation_devis" => $this->id_devis_formation ,"id_societe" => $this->id_societe ,"type"=>"normale", "ref" => "La ref de facture", "prix"=> "2500", "num_dossier"=> "Le num de dossier", "date"=>date("Y-m-d") );
		$return = $this->obj->insert(array("preview" => true, "formation_facture" => $infos));
		$this->assertNotNull($return,'Formation_commande non créé 1 !');

		$formation_facture = $this->obj->insert(array("formation_facture" => $infos));
		$this->assertNotNull($formation_facture,'formation_facture non créé 2 !');

		$data = array();

		foreach ($this->obj->select_all() as $key => $value) {
			foreach ($value as $k => $v) {
				$data[$key]["formation_facture.".$k] = $v;
			}
			
		}
	    $q=ATF::_s("pager")->create("gsa_test_lol");  
        ob_start();  
      	$this->obj->export_special(array('onglet'=>"gsa_test_lol", "rejet"=>"oui", "tu"=>"oui", "data"=>$data));
        ob_end_clean();           
    }
};


class objet_excel_ff {	
	public function __construct(){
		$this->sheet=new objet_sheet();
	}
	public function write($col,$valeur) {
		$this->$col=$valeur;
	}
}
class objet_sheet_ff {
	public function getColumnDimension($col){
		return $this;
	}
	public function setAutoSize($bool){
		$this->size=true;
	}
}

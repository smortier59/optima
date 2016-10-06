<?
class user_cleodis_test  extends ATF_PHPUnit_Framework_TestCase {
	
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


	public function test_export_user_infos(){
        ATF::loyer()->i(array("id_affaire"=>13538, "loyer"=>200,"duree"=>5));


		$infos = array("id_user"=> 95, "tu"=>true);

        ob_start();
        $this->obj->export_user_infos($infos , true);
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
    }

    public function test_getFeuillesCap(){
        $c = new user_cap();
        $this->assertEquals(array("Export Optima","RDV","Appels") , $c->getFeuilles(), "Retour incorrect");
    }
};

class objet_excel_user { 
    public function __construct(){
        $this->sheet=new objet_sheet();
    }
    public function write($col,$valeur) {
        $this->$col=$valeur;
    }
}
class objet_sheet_user {
    public function getColumnDimension($col){
        return $this;
    }
    public function setAutoSize($bool){
        $this->size=true;
    }
}
?>
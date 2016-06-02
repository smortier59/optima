<?
class agence_cleodis_test  extends ATF_PHPUnit_Framework_TestCase {
	   
    private function beginTransaction($codename, $begin, $commit){
        if($begin){
            ATF::db()->select_db("extranet_v3_".$codename);
            ATF::$codename = $codename;
            ATF::db()->begin_transaction(true);
        }
        
        if($commit){
            ATF::db()->rollback_transaction(true);
            ATF::$codename = "cleodis";
            ATF::db()->select_db("extranet_v3_cleodis");
        }
        
    }


	public function test_export_agence_infos(){
        $this->beginTransaction("cleodisbe", true, false);

        ATF::loyer()->i(array("id_affaire"=>7, "loyer"=>200,"duree"=>5));
        ATF::societe()->u(array("id_societe"=>6750, "id_owner"=>94));

		$infos = array("id_agence"=> 1, "tu"=>true);

        ob_start();
        $this->obj->export_agence_infos($infos);
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();

        $this->beginTransaction("cleodis", false, true);

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
    }

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_construct(){        
        $c = new agence_cleodis();    

        $this->assertTrue($c instanceOf agence_cleodis, "L'objet agence_cleodis n'est pas de bon type");
      
    }
};

class objet_excel_agence { 
    public function __construct(){
        $this->sheet=new objet_sheet();
    }
    public function write($col,$valeur) {
        $this->$col=$valeur;
    }
}
class objet_sheet_agence {
    public function getColumnDimension($col){
        return $this;
    }
    public function setAutoSize($bool){
        $this->size=true;
    }
}
?>
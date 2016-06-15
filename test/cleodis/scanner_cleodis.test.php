<?
/**
* Classe de test sur le module societe_cleodis
*/
class scanner_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
		
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);		
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	public function test_transfert(){
		$id = $this->obj->insert(array("nbpages"=>2, "provenance"=>"toto@absystech.fr"));
		
		$infos["id_scanner"] = $id;
		$infos["comboDisplay"] = "devis.retourBPA";
		$infos["transfert"] = "Devis - Retour Bon Pour Accord";
		$infos["reference"] = "test";
		
		try{
			$this->obj->transfert($infos);
		}catch(errorATF $e){
			$error = $e->getMessage();
		}
		$this->assertEquals("Il n'y a pas de Devis  ayant la référence test", $error, "1 - Error Ref inconnue");		
	}
}
?>	
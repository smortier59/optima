<?
/* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
class suivi_notifie_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		$this->initUser();
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	public function testNom(){
		// Ajout d'un suivi
		$id_suivi=ATF::suivi()->i(array("id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"texte"=>"TU"));

		$id_suivi_notifie=$this->obj->i(array("id_suivi"=>$id_suivi,"id_user"=>$this->id_user));
		$suivi_notifie=$this->obj->nom($id_suivi_notifie);
		$this->assertEquals("TU ".ATF::user()->nom($this->id_user),$suivi_notifie,"Mauvais nom retourner");
	}
}
?>
<?
/* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */
class suivi_contact_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		$this->initUser();
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	public function testNom(){
		// Ajout d'un suivi
		$id_suivi=ATF::suivi()->i(array("id_user"=>$this->id_user,"id_societe"=>$this->id_societe,"texte"=>"TU"));

		$id_suivi_contact=$this->obj->i(array("id_suivi"=>$id_suivi,"id_contact"=>$this->id_contact));
		$suivi_contact=$this->obj->nom($id_suivi_contact);
		$this->assertEquals("TU ".ATF::contact()->nom($this->id_contact),$suivi_contact,"Mauvais nom retourner");
	}
}
?>
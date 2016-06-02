<?
/**
* Classe de test des mails hotline
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* ATTENTION : Il est normal que la classe ne soit pas testée au complet. En effet les tests de mails sont testés dans les méthodes métiers de hotline et hotline_interaction
*/
class contact_absystech_test extends ATF_PHPUnit_Framework_TestCase {
			
	protected function setUp() {
		$this->initUser();
	}
	
	protected function tearDown() {
        ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
	}

	public function test_sendMailTeamViewer(){
		$this->obj->sendMailTeamViewer(array("id_contact"=>3032));
		$this->assertEquals(1, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");

	}

}
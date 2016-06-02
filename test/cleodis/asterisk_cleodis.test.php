<?
/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
class asterisk_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_getAgentConcerned(){
		$this->assertEquals(201,$this->obj->getAgentConcerned("0546990037"),"mauvais utilisateur concerne");
		$this->assertEquals(201,$this->obj->getAgentConcerned("0324332939"),"mauvais utilisateur concerne 2");
		$this->assertNull($this->obj->getAgentConcerned("numeroimpossible"),"mauvais utilisateur concerne 3");
	}
}
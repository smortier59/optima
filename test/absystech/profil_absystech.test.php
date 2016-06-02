<?php
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class profil_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true); 
		$this->obj = new profil_absystech();
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::db()->query("ALTER TABLE `profil` AUTO_INCREMENT =1"); // Protection autoincremente
	}
	
	public function testUpdate(){
		$infos["id_profil"]=1;
		$infos["seuil"]=1000;
		$this->obj->update($infos);
		$this->assertNull($this->obj->select(1,"seuil"),'Le privilege associé doit rester à NULL');
		
		$infos["id_profil"]=4;
		$infos["seuil"]=900;
		$this->obj->update($infos);
		$this->assertEquals("900",$this->obj->select(4,"seuil"),'Les privileges autres que associé doivent pouvoir être modifié');
	}
	
};
?>
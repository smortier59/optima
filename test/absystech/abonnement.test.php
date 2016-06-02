<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class abonnement_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	public function test_insertMassif(){
		$this->obj->insertMassif();
		$this->obj->q->reset();		
	
		$liste_abonnement=$this->obj->select_all();
		
		$this->assertTrue(count($liste_abonnement)>0,"Problème dans la génération des abonnements");
		foreach($liste_abonnement as $cle=>$infos){
			$this->assertTrue(!is_null($infos['codename']),"Le codename n'a pas été renseigné");
			
			$nbre_user+=$infos['nbre_user_actif'];
			$espace_utilise+=$infos['espace_utilise'];
		}
		$this->assertTrue($nbre_user>0,"Il n'y a aucun utilisateur actif dans aucun codename");
		$this->assertTrue($espace_utilise>0,"Il n'y a aucun espace utilise dans aucun codename");
	}
};
?>
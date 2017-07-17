<?
/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
class user_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->initUser();
	}
	
	public function tearDown() {
		ATF::db()->rollback_transaction(true);
	}
	
	public function test_autoCompleteSpecifiques(){
		$retour=$this->obj->autocompleteAssDirection();
		foreach ($retour as $u) {
			$this->assertEquals("Assistant de direction",ATF::profil()->nom(ATF::user()->select($u[0],"id_profil")),$u[1]." n'a pas le profil Assistante de direction ou alors son libellé a changé");
		}
		$this->obj->q->reset('order')->addOrder('id_user');
		$retour=$this->obj->autocompleteTechnicien(NULL);
		foreach ($retour as $u) {
			$erreur = false;	
			$p = ATF::profil()->nom(ATF::user()->select($u[0],"id_profil"));
			if ($p=="Technicien" || $p=="Développeur" || $p=="Associé" || $p=="Commercial") {
				$erreur = false;	
			} else {
				$erreur = true;	
			}
			$this->assertFalse($erreur,$u[1]." n'a pas l'un des profils attendus qui sont : Technicien, Commercial, Associé ou Développeur. Ou alors les libellés ont changés");
		}
	}
	public function test_updateMailUser(){
		try{
			ATF::user()->_updateMailUser();
		}catch(errorATF $e){
			$errorCode = $e->getCode();
			$errorMessage = $e->getMessage();
		}
		$this->assertEquals(500,$errorCode,"ne retourne pas le bon code d'erreur");
		$this->assertEquals("Il manque le mot de passe",$errorMessage,"ne retourne pas le bon message d'erreur");

		$post = array('password_mail'=> "test");
		try{
			ATF::user()->_updateMailUser(false,$post);
		}catch(errorATF $e){
			$errorCode = $e->getCode();
			$errorMessage = $e->getMessage();
		}
		$this->assertEquals(500,$errorCode,"ne retourne pas le bon code d'erreur");
		$this->assertEquals("Il manque l'id user",$errorMessage,"ne retourne pas le bon message d'erreur");

		$post["id_user"] = $this->id_user;
		$ret = ATF::user()->_updateMailUser(false,$post);
		$this->assertTrue($ret,"Doit retourner True");

	}
};
?>
<?
class candidat_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécutée après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/*--------------------------------------------------------------*/
	/*                   Tests unitaires                            */
	/*--------------------------------------------------------------*/
	
	/** Test du constructeur
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function test_candidat_constructeur(){
		new candidat();
	}	
	
	//@author Nicolas BERTEMONT <nbertemont@absystech.fr>
	public function testValidation(){
		//creation d'un candidat bidon
		$id_candidat=$this->obj->i(array("nom"=>"nom","prenom"=>"prenom","annee_de_naissance"=>"1985","niveau_diplome"=>"BAC +1"));
		
		//test du mail
		try{
			$this->obj->validation(array("id_candidat"=>$id_candidat,"etat"=>"non","raison"=>"test"));
			$this->assertTrue(false,'Une erreur aurait du etre généré car pas de mail spécifié');
		}catch(error $e){
			//c good, check de l'update effectué
			$this->assertEquals("non",$this->obj->select($id_candidat,'etat'),"L'état aurait du changer en non");
		}
		
		$this->obj->u(array("id_candidat"=>$id_candidat,"email"=>"toto@hotmail.fr"));
		$this->obj->validation(array("id_candidat"=>$id_candidat,"etat"=>"oui","raison"=>"test2"));
		$this->assertEquals("oui",$this->obj->select($id_candidat,'etat'),"L'état aurait du changer en oui");
		$notice=ATF::$msg->getNotices();
		$this->assertEquals(ATF::$usr->trans("mail_envoye",$this->obj->table),$notice[0]['msg'],"Le contenu de la notice est incorrecte");
	}
};
?>
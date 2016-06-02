<?
class pdf_affaire_test extends ATF_PHPUnit_Framework_TestCase {
	

	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);
	}
	
	/* Méthode post-test, exécute après chaque test unitaire */
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}


	/** Test du constructeur
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function test__construct(){
		$c = new pdf_affaire();	
		$this->assertTrue($c instanceOf pdf_affaire, "L'objet pdf_affaire n'est pas de bon type");
	}


	public function test_imageExist(){
		$this->assertEquals(false , $this->obj->imageExist(1), "Il y a une image??");
	}

	public function test_getFichierJoint(){
		$this->assertEquals(false , $this->obj->getFichierJoint(1), "Il y a un fichier_joint??");
	}

	public function test_getUrlImagePDF(){
		$return = array('URL'  => 'http://dev.optima.absystech.net/pdf_affaire-e8cc8cdd5888313ddf6bb8bc89dc9a7a-previewPDF',
						'URLDL'=> 'http://dev.optima.absystech.net/pdf_affaire-e8cc8cdd5888313ddf6bb8bc89dc9a7a-previewPDF');
		$this->assertEquals($return , $this->obj->getUrlImagePDF(1), "Erreur d'url");
	}

	public function test_select_all(){
		$this->obj->q->setLimit(5);
		$return = $this->obj->select_all(true,"asc");
	}


};
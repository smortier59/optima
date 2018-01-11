<?
class pdf_societe_test extends ATF_PHPUnit_Framework_TestCase {


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
		$c = new pdf_societe();
		$this->assertTrue($c instanceOf pdf_societe, "L'objet pdf_societe n'est pas de bon type");
	}


	public function test_imageExist(){
		$this->assertEquals(false , $this->obj->imageExist(1), "Il y a une image??");
	}

	public function test_getFichierJoint(){
		$this->assertEquals(false , $this->obj->getFichierJoint(1), "Il y a un fichier_joint??");
	}

	public function test_getUrlImagePDF(){
		$return = array('URL'  => 'http://dev.optima.absystech.net/pdf_societe-c08702d15886266251a42557eba48668-previewPDF',
						'URLDL'=> 'http://dev.optima.absystech.net/pdf_societe-c08702d15886266251a42557eba48668-previewPDF');
		$this->assertEquals($return , $this->obj->getUrlImagePDF(1), "Erreur d'url");
	}

	public function test_select_all(){
		$id_societe = 246;
		$file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";

		$id_pdf_societe = ATF::pdf_societe()->i(array("id_societe"=>$id_societe, "nom_document"=>"test"));


		copy($file, __ABSOLUTE_PATH__."../data/testsuite/pdf_societe/".$this->obj->cryptId($id_pdf_societe).".fichier_joint");
		copy($file, __ABSOLUTE_PATH__."../data/testsuite/pdf_societe/".$id_pdf_societe.".fichier_joint");
		$this->obj->dynamicPicture($id_pdf_societe);


		$this->obj->q->setLimit(5)->addOrder("id_pdf_societe",'desc');
		$return = $this->obj->select_all(true,"asc");

		log::logger($return , "mfleurquin");
	}


	public function test_dynamicPicture(){

		$id_societe = 246;
		$file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";

		$id_pdf_societe = ATF::pdf_societe()->i(array("id_societe"=>$id_societe, "nom_document"=>"test"));


		copy($file, __ABSOLUTE_PATH__."../data/testsuite/pdf_societe/".$id_pdf_societe.".fichier_joint");


		$return = $this->obj->dynamicPicture($id_pdf_societe);

		$attendu = array('URL'  => 'http://dev.optima.absystech.net/pdf_societe-'.$id_pdf_societe.'-previewPDF',
						'URLDL'=> 'http://dev.optima.absystech.net/pdf_societe-'.$id_pdf_societe.'-previewPDF');
		$this->assertEquals($attendu , $return, "Erreur retour dynamicPicture");
	}


};
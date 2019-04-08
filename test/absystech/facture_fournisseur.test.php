<?
class facture_fournisseur_test extends ATF_PHPUnit_Framework_TestCase {
	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function setUp() {
		$this->initUser();
	}

	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function tearDown(){
		$this->rollback_transaction();
	}

	function insert(){
		$array = array("date"=> date("Y-m-d"), "nb_page" => 0);
		$this->obj->insert($array);
		$all = $this->obj->select_all();
		$id = $all[0]["id_facture_fournisseur"];

		$filename = $this->obj->filepath($id,"fichier_joint");
        $tmppdf = touch($filename);
        $this->assertFileExists($filename,"Pas de fichier joint !");
        $this->id =  $id;
	}



	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function test_insert(){
		$array = array("date"=> date("Y-m-d"), "nb_page" => 0);
		$all = $this->obj->select_all();
		$this->obj->insert($array);
		$id = $all[0]["id_facture_fournisseur"];
		$all = $this->obj->select_all();

		$this->assertNotEquals($id , $all[0]["id_facture_fournisseur"] , "Insert incorrect");
		$this->assertEquals(date("Y-m-d") , $all[0]["date"] , "Insert incorrect 2");
		$this->assertEquals("attente" , $all[0]["statut"] , "Insert incorrect 3");
	}


	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function test_getNextToOCR(){
		$this->insert();
		$return = $this->obj->getNextToOCR();
		$this->assertEquals($this->id , $return[0], "L'id du 1er fichier sans OCR incorrect");

		$this->obj->u(array("id_facture_fournisseur" => $this->id, "ocr"=> "test"));

		$array = array("date"=> date("Y-m-d"), "nb_page"=>0);
		$this->obj->insert($array);
		//$this->assertNotNull($this->obj->getNextToOCR(), "Pas d'autre fichier sans OCR et sans fichier??");

	}

	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	/*function test_dynamicPicture(){
		$this->assertEquals(false , $this->obj->dynamicPicture(), "Ne dois pas générer d'image si pas d'id facture !");
		$this->insert();
		$return = $this->obj->dynamicPicture($this->id);
		$this->assertEquals( "http://dev.optima.absystech.net/facture_fournisseur-".$this->id."-previewPDF" , $return['URL'] , "Retour incorrect !");
	}*/


	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function test_imageExist(){
		$this->insert();
		$this->assertEquals(false , $this->obj->imageExist($this->id) , "Image existante ??");
	}

	//@author  Morgan FLEURQUIN <mfleurquin@ absystech.fr>
	function test_getUrlImagePDF(){
		$this->insert();
		$res = $this->obj->getUrlImagePDF($this->id);
		$this->assertEquals("http://dev.optima.absystech.net/facture_fournisseur-".$this->obj->cryptId($this->id)."-previewPDF" , $res["URL"]  , "Error getUrlImagePDF");
	}

	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function test_getFichierJoint(){
		$this->insert();
		$this->assertEquals(true , $this->obj->getFichierJoint($this->id) , "Error getFichierJoint");
	}


	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	/*function test_select_all(){
		$this->insert();
		$this->obj->q->setCount(true);
        $data=$this->obj->select_all();
	}*/

	//@author  Morgan FLEURQUIN <mfleurquin@absystech.fr>
	function test_setInfos(){
		$this->insert();
		$this->obj->setInfos(array("id_facture_fournisseur"=> $this->id, "field" =>"montant_ht", "value"=> 587));
		$this->assertEquals(587, $this->obj->select($this->id,  "montant_ht") , "Le setInfos n'a pas mis à jour !");

		$this->assertEquals(1, count(ATF::$msg->getNotices()), "Nombre de notices incorrect");


	}


};
?>
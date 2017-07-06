<?
class echeancier_ligne_periodique_test extends ATF_PHPUnit_Framework_TestCase {
	// @author Cyril Charlier <ccharlier@absystech.fr>
	private $echeancier_ligne_periodique;
	private $echeancier;
	private $societe;

	public function setUp(){
		ATF::db()->begin_transaction(true);
		$this->environnement_test();

	}
	private function environnement_test(){
		ATF::db()->truncate("echeancier_ligne_periodique");
		// societe 
		$commercial = ATF::user()->i(array("login" =>"userTest",
			'password'=>"az78qs45",
			'nom'=>'test',
			'civilite'=>'m',
			'prenom'=>'test prenom'
		));

		$this->societe = ATF::societe()->i(array("societe"=>"Societe Test",
			'id_commercial'=>$commercial
		));
		$affaire =ATF::affaire()->insert(array(
			"id_societe" =>$this->societe,
			'affaire'=>"Affaire de test",
		));
		$this->echeancier =ATF::echeancier()->insert(array(
			"id_societe" =>$this->societe,
			'id_affaire'=>$affaire,
			'designation'=>"echeancier test",
			'debut' => date("Y-m-d")
		));
		$this->echeancier_ligne_periodique =ATF::echeancier_ligne_periodique()->insert(array(
			"designation" =>"echeancier ligne periodique test TU",
			'quantite'=>2,
			'puht'=> 5.2,
			'valeur_variable' => 'oui',
			'id_echeancier' => $this->echeancier,
			'mise_en_service' => date("Y-m-d"),
			'ref' => "Ref de test",
			'id_compte_absystech' => 1
		));
	}
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ATF::$msg->getNotices();

	}
	public function test_table(){
		$this->assertEquals("echeancier_ligne_periodique",$this->obj->name(),"Probleme de nom de classe echeancier_ligne_periodique");
	}
	public function test_GET(){

		$ret =ATF::echeancier_ligne_periodique()->_GET();
		$this->assertEquals("echeancier ligne periodique test TU",$ret[0]["designation"],"Probleme de message retour designation echeancier ligne periodique");
		$this->assertEquals(2,$ret[0]["quantite"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals("oui",$ret[0]["valeur_variable"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals($this->echeancier,$ret[0]["id_echeancier_fk"],"Probleme de message retour id_echeancier echeancier ligne periodique");
		$this->assertEquals(1,$ret[0]["id_compte_absystech_fk"],"Probleme de message retour compte_absystech echeancier ligne periodique");
		$this->assertEquals('Ref de test',$ret[0]["ref"],"Probleme de message retour ref echeancier ligne periodique");
	}

	public function test_GETWithSearch(){

		ATF::echeancier_ligne_periodique()->insert(array(
			"designation" =>"deuxieme ligne",
	    	'quantite'=>2,
	    	'puht'=> 5.2,
	    	'valeur_variable' => 'oui',
			'id_echeancier' => $this->echeancier,
	    	'mise_en_service' => date("Y-m-d"),
	    	'ref' => "Ref de test2",
	    	'id_compte_absystech' => 1
    	));
    	ATF::echeancier_ligne_periodique()->insert(array(
			"designation" =>"troisieme ligne",
	    	'quantite'=>2,
	    	'puht'=> 8.52,
	    	'valeur_variable' => 'non',
			'id_echeancier' => $this->echeancier,
	    	'mise_en_service' => date("Y-m-d"),
	    	'ref' => "Ref de test3",
	    	'id_compte_absystech' => 1
    	));
    	$get  = array('search' => "deuxieme");
		$ret =ATF::echeancier_ligne_periodique()->_GET($get);		
		$this->assertEquals("deuxieme ligne",$ret[0]["designation"],"Probleme de message retour designation echeancier ligne periodique");
		$this->assertEquals(2,$ret[0]["quantite"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals("oui",$ret[0]["valeur_variable"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals($this->echeancier,$ret[0]["id_echeancier_fk"],"Probleme de message retour id_echeancier echeancier ligne periodique");
		$this->assertEquals(1,$ret[0]["id_compte_absystech_fk"],"Probleme de message retour compte_absystech echeancier ligne periodique");
		$this->assertEquals('Ref de test2',$ret[0]["ref"],"Probleme de message retour ref echeancier ligne periodique");
		
	}
	public function test_GETById(){

		$maLignePeriodique = ATF::echeancier_ligne_periodique()->insert(array(
			"designation" =>"Test designation",
			'quantite'=>8,
			'puht'=> 5.2,
			'valeur_variable' => 'non',
			'id_echeancier' => $this->echeancier,
			'mise_en_service' => date("Y-m-d"),
			'ref' => "Ref de TU",
			'id_compte_absystech' => 2
		));

		$get  = array('id' => $maLignePeriodique);
		$ret =ATF::echeancier_ligne_periodique()->_GET($get);
		$this->assertEquals("Test designation",$ret["designation"],"Probleme de message retour designation echeancier ligne periodique");
		$this->assertEquals(8,$ret["quantite"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals("non",$ret["valeur_variable"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals($this->echeancier,$ret["id_echeancier_fk"],"Probleme de message retour id_echeancier echeancier ligne periodique");
		$this->assertEquals(2,$ret["id_compte_absystech_fk"],"Probleme de message retour compte_absystech echeancier ligne periodique");
		$this->assertEquals('Ref de TU',$ret["ref"],"Probleme de message retour ref echeancier ligne periodique");
		
	}
	
	public function test_PUT(){
		$post = array(
			"designation" =>"echeancier test TU",
			'quantite'=>3,
			'puht'=> 2.4,
			'valeur_variable' => 'oui',
			'mise_en_service' => date("d-m-Y"),
			'ref' => "Ref de test after PUT",
			'id_compte_absystech' => 3,
			'id_echeancier_ligne_periodique' => $this->echeancier_ligne_periodique
		);
		$ret =ATF::echeancier_ligne_periodique()->_PUT(false,$post);
		$this->assertEquals("echeancier test TU",$ret['row']["designation"],"Probleme de message retour designation echeancier ligne periodique");
		$this->assertEquals(3,$ret['row']["quantite"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals("non",$ret['row']["valeur_variable"],"Probleme de message retour quantite echeancier ligne periodique");
		$this->assertEquals(3,$ret['row']["id_compte_absystech"],"Probleme de message retour compte_absystech echeancier ligne periodique");
		$this->assertEquals('Ref de test after PUT',$ret['row']["ref"],"Probleme de message retour ref echeancier ligne periodique");
	}
	public function test_PUTError(){
		try {
			$post = array(
				"designation" =>"echeancier test TU",
				'quantite'=>3,
				'puht'=> 2.4,
				'valeur_variable' => 'oui',
				'mise_en_service' => date("d-m-Y"),
				'ref' => "Ref de test after PUT",
				'id_compte_absystech' => "",
			);
			ATF::echeancier_ligne_periodique()->_PUT('',$post);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(500,$error,'problème sur post lorsqu il y a des fields manquants');
	}


	public function test_DELETEWithoutId(){
		try {
			$get = array('test'=> 'test');
			ATF::echeancier_ligne_periodique()->_DELETE($get);
		} catch (errorATF $e) {
			$error = $e->getCode();
		}
		$this->assertEquals(1000,$error,'problème sur le delete lorsqu il y a des fields manquants');
	}

	public function test_DELETE(){
		$get = array('id'=> $this->echeancier_ligne_periodique);
		$ret = ATF::echeancier_ligne_periodique()->_DELETE($get);
		$this->assertEquals(1,$ret['result'],'problème sur le retour du delete');
		$this->assertEquals('success',$ret['notices'][0]['type'],'problème sur le retour du delete');

	}
	public function test_POSTWithoutIdEcheancier(){
		try {
			$post = array('test'=> 'test');
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		}catch (errorATF $e){
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		}
		$this->assertEquals("ID_ECHEANCIER_MISSING",$message_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');

		$this->assertEquals(5042,$code_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');
	}
	
	public function test_POSTWithoutRef(){
		try {
			$post = array('id_echeancier'=> $this->echeancier);
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		} catch (errorATF $e) {
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		}
		$this->assertEquals("REF_MISSING",$message_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');

		$this->assertEquals(5042,$code_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');
	}

	public function test_POSTWithoutDesignation(){
		try {
			$post = array(
				'id_echeancier'=> $this->echeancier,
				'ref'=> "MaRef"	
			);
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		} catch (errorATF $e) {
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		}
		$this->assertEquals("DESIGNATION_MISSING",$message_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');

		$this->assertEquals(5042,$code_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');
	}
	public function test_POSTWithoutQte(){
		try {
			$post = array(
				'id_echeancier'=> $this->echeancier,
				'ref'=> "MaRef",
				'designation'=> "Ma Designation"
			);
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		} catch (errorATF $e) {
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		}
		$this->assertEquals("QTE_MISSING",$message_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');

		$this->assertEquals(5042,$code_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');
	}
	public function test_POSTWithoutMiseEnService(){
		try {
			$post = array(
				'id_echeancier'=> $this->echeancier,
				'ref'=> "MaRef",
				'designation'=> "Ma Designation",
				'quantite'=> 3
			);
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		} catch (errorATF $e) {
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		}
		$this->assertEquals("MISE_EN_SERVICE_MISSING",$message_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');

		$this->assertEquals(5042,$code_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');
	}
	public function test_POSTWithoutVentilation(){
		try {
			$post = array(
				'id_echeancier'=> $this->echeancier,
				'ref'=> "MaRef",
				'designation'=> "Ma Designation",
				'quantite'=> 3,
				'mise_en_service'=> date('d-m-Y')
			);
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		} catch (errorATF $e) {
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		};
		$this->assertEquals("VENTILATION_MISSING",$message_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');

		$this->assertEquals(5042,$code_error,'problème sur l\'erreur du post lorsqu il y a des fields manquants');
	}
	public function test_POST(){
		$post = array(
			'id_echeancier'=> $this->echeancier,
			'ref'=> "Marefzer",
			'designation'=> "Ma Designation",
			'quantite'=> 3,
			'mise_en_service'=> date('d-m-Y'),
			'id_compte_absystech'=> 2,
			'valeur_variable' => 'on'
		);
		$ret = ATF::echeancier_ligne_periodique()->_POST(false,$post);
		$this->assertEquals("MAREFZER",$ret['row']['ref'],'problème sur la ref retournée par le post');
		$this->assertEquals($this->echeancier, $ret['row']['id_echeancier'],'problème sur l\' id echeancier retournée par le post');
		$this->assertEquals(3, $ret['row']['quantite'],'problème sur la quantité retournée par le post');
		$this->assertEquals("oui", $ret['row']['valeur_variable'],'problème sur la valeur_variable retournée par le post');
		$this->assertEquals("Ma Designation", $ret['row']['designation'],'problème sur la designation retournée par le post');
		$this->assertEquals(2, $ret['row']['id_compte_absystech'],'problème sur l\' id_compte_absystech retournée par le post');
	}
	public function test_POSTErrorInsert(){
		try{
			$post = array(
				'id_echeancier'=>'azerty',
				'ref'=> "Marefzer",
				'designation'=> "Ma Designation",
				'quantite'=> 3,
				'mise_en_service'=> date('d-m-Y'),
				'id_compte_absystech'=> 2,
				'valeur_variable' => 'on'
			);
			ATF::echeancier_ligne_periodique()->_POST(false,$post);
		} catch (errorATF $e) {
			$code_error = $e->getCode();
		};
		$this->assertEquals(500, $code_error,'probleme retour code erreur post');

	}
}
?>
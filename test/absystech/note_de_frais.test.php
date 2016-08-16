<?
/**
* Tu module note de frais
* @author Quentin JANON <qjanon@absystech.fr>
* @date 23-03-2011
*/
class note_de_frais_test extends ATF_PHPUnit_Framework_TestCase {
		
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	protected function setUp() {
		$this->initUserOnly();
 		$this->obj = ATF::note_de_frais();
		ATF::note_de_frais()->truncate();
		
		util::rm("/home/optima/data/testsuite/note_de_frais/*");
		util::rm("/home/optima/temp/testsuite/note_de_frais/*");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	protected function tearDown() {
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_default_value() {
		$r = $this->obj->default_value("id_user");
		$this->assertEquals(ATF::$usr->getID(),$r,"Erreur 1");
		$r = $this->obj->default_value("civilite");
		$this->assertNull($r,"Erreur 2");
	}
	
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function test_insertN1() {
		// TEST 1
		$array = array(
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-02-02 00:00:00"
			)
		);
		try{
			$r = $this->obj->insert($array);
			$erreur=false;
		}catch(errorATF $e){
			$erreur=true;
		}
		$this->assertTrue($erreur,"Ca devrait faire une erreur, il n'y a pas de lignes");
		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function test_insertN2() {
		$array = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050")
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-02-02 00:00:00"
			)
		);
		$r = $this->obj->insert($array);
		$this->assertNotNull($r,"Erreur lors de l'insertion");
		$this->assertEquals("2050-02",$this->obj->select($r,"note_de_frais"),"Erreur de libellé");
		$c = ATF::note_de_frais_ligne()->ss("id_note_de_frais",$r,false,"asc",false,true);
		$this->assertEquals(2,$c['count'],"Le nombre de ligne n'est pas bon");
		
		
	}
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function test_insertN3() {
	
		$array = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050")
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
				,"date"=>"2051-02-02 00:00:00"
				,"filestoattach"=>array("justificatifs")
			),
		);
		// Création de quelques fichier dans le dossier temporaire pour simuler l'envoi de justificatifs
		$dir = dirname($this->obj->filepath(ATF::$usr->getID(),"*",true));
		util::file_put_contents($dir."/fichierTU1.jpg","hndgdsbs");
		util::file_put_contents($dir."/".ATF::$usr->getID().".justificatifs.fichierTU2.pdf","zefghfrghfdgs");
		util::file_put_contents($dir."/".ATF::$usr->getID().".justificatifs.fichierTU3.png","nhfdndfsgndf");
		util::file_put_contents($dir."/".ATF::$usr->getID().".justificatifs.fichierTU4.doc","gtrzergezrgtrg");
		
		$this->obj->lastID = $r = $this->obj->insert($array);
		$this->assertNotNull($r,"Erreur lors de l'insertion");
		$this->assertEquals("2051-02",$this->obj->select($r,"note_de_frais"),"Erreur de libellé");
		$c = ATF::note_de_frais_ligne()->ss("id_note_de_frais",$r,false,"asc",false,true);
		$this->assertEquals(2,$c['count'],"Le nombre de ligne n'est pas bon");
		
		$zip = $this->obj->filepath($r,"justificatifs");

		$this->assertFileExists($zip,"Erreur, le zip ne s'est pas créer");
		$this->assertTrue(file_exists($dir."/fichierTU1.jpg"),"Erreur, le fichier qui ne devait pas être mis dans le zip a disparu.");
		$this->assertFalse(file_exists($dir."/".ATF::$usr->getID().".fichierTU2.pdf"),"Erreur, le fichier PDF devrait être dans le zip et plus ici.");
		$this->assertFalse(file_exists($dir."/".ATF::$usr->getID().".fichierTU3.png"),"Erreur, le fichier PNG devrait être dans le zip et plus ici.");
		$this->assertFalse(file_exists($dir."/".ATF::$usr->getID().".fichierTU4.doc"),"Erreur, le fichier DOC devrait être dans le zip et plus ici.");
		
		util::rm($zip);
		util::rm($dir);
		
		
	}

	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_update() {
		$i = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050")
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
			)
		);
		$idNF = $this->obj->insert($i);
		
		$c = ATF::note_de_frais_ligne()->ss("id_note_de_frais",$idNF);
		
		$u = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__id_note_de_frais_ligne"=>$c[0]['id_note_de_frais_ligne'],"note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__id_note_de_frais_ligne"=>$c[1]['id_note_de_frais_ligne'],"note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050","note_de_frais_ligne__dot__id_societe_fk"=>1,"note_de_frais_ligne__dot__id_frais_kilometrique"=>"Phoque")
					,array("note_de_frais_ligne__dot__montant"=>2500,"note_de_frais_ligne__dot__date"=>"2050-02-22 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 20 février 2050","note_de_frais_ligne__dot__id_societe"=>1,"note_de_frais_ligne__dot__id_frais_kilometrique_fk"=>1)
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
				,"id_note_de_frais"=>$idNF
			)
		);
		$r = $this->obj->update($u);
		
		$this->assertNotNull($r,"Erreur lors de l'update");
		$c = ATF::note_de_frais_ligne()->ss("id_note_de_frais",$idNF,false,"asc",false,true);
		$this->assertEquals(3,$c['count'],"Le nombre de ligne n'est pas bon");
		
		$u = array(
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
				,"id_note_de_frais"=>$idNF
			)
		);
		try{
			$r = $this->obj->update($u);
			$erreur=false;
		}catch(errorATF $e){
			$erreur=true;
		}
		$this->assertTrue($erreur,"Ca devrait faire une erreur, il n'y a pas de lignes");

		// TEST AVECC LES FICHIERS
		$u = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__id_note_de_frais_ligne"=>$c[0]['id_note_de_frais_ligne'],"note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__id_note_de_frais_ligne"=>$c[1]['id_note_de_frais_ligne'],"note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050","note_de_frais_ligne__dot__id_societe_fk"=>1)
					,array("note_de_frais_ligne__dot__montant"=>2500,"note_de_frais_ligne__dot__date"=>"2050-02-22 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 20 février 2050","note_de_frais_ligne__dot__id_societe"=>1)
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
				,"id_note_de_frais"=>$idNF
			)
		);
		// Création de quelques fichier dans le dossier temporaire pour simuler l'envoi de justificatifs
		$dir = dirname($this->obj->filepath(ATF::$usr->getID(),"*",true));

		util::file_put_contents($dir."/fichierTU1.jpg","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU2.pdf","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU3.png","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU4.doc","");
		
		$r = $this->obj->update($u);
		
		$zip = $this->obj->filepath($idNF,"justificatifs");
		$this->assertTrue(file_exists($zip),"Erreur, le zip ne s'est pas créer");
		$this->assertTrue(file_exists($dir."/fichierTU1.jpg"),"Erreur, le fichier qui ne devait pas être mis dans le zip a disparu.");
		$this->assertFalse(file_exists($dir."/".ATF::$usr->getID().".fichierTU2.pdf"),"Erreur, le fichier PDF devrait être dans le zip et plus ici.");
		$this->assertFalse(file_exists($dir."/".ATF::$usr->getID().".fichierTU3.png"),"Erreur, le fichier PNG devrait être dans le zip et plus ici.");
		$this->assertFalse(file_exists($dir."/".ATF::$usr->getID().".fichierTU4.doc"),"Erreur, le fichier DOC devrait être dans le zip et plus ici.");
		
		util::rm($dir);

		// TEST AVEC LE ZIP 0000
		$dir = dirname($this->obj->filepath(ATF::$usr->getID(),"*",true));

		util::file_put_contents($dir."/fichierTU1.jpg","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU2.pdf","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU3.png","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU4.doc","");
		
		chmod($zip,0000);

		$erreur=false;
		try {
			$r = $this->obj->update($u);
			$erreur=false;
		} catch (errorATF $e) {
			$erreur=true;
		}
		$this->assertTrue($erreur,"Ca devrait faire une erreur, le zip n'a pas le bon chmod");
		$this->assertEquals(501,$e->getCode(),"Ca devrait faire une erreur 501");
		
		util::rm($dir);

		util::file_put_contents($dir."/fichierTU1.jpg","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU2.pdf","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU3.png","");
		util::file_put_contents($dir."/".ATF::$usr->getID().".fichierTU4.doc","");
		
		chmod($zip,0777);
		chmod($dir."/".ATF::$usr->getID().".fichierTU2.pdf",0000);

		$erreur=false;
		try {
			$r = $this->obj->update($u);
			$erreur=false;
		} catch (errorATF $e) {
			$erreur=true;
		}
		$this->assertTrue($erreur,"Ca devrait faire une erreur, le fichier n'a pas le bon chmod");
		$this->assertEquals(502,$e->getCode(),"Ca devrait faire une erreur 502");
		
		util::rm($zip);
		util::rm($dir);

	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function test_canValid() {
		$this->assertFalse($this->obj->canValid(),"Erreur : Pas d'id_user donc false...");
		
		ATF::user()->update(array('id_user'=>ATF::$usr->getID(),'id_superieur'=>ATF::$usr->getID()));
		$this->assertTrue($this->obj->canValid(ATF::$usr->getID()),"Erreur : On est soi même superieur, donc on devrait avoir le droit.");
		
		ATF::$usr->set('id_user',3);
		$this->assertTrue($this->obj->canValid(),"Erreur : Sebastien a le droit de tous faire");
	} 
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_select_all() {
		$idUser = ATF::user()->i(array(
			"login"=>"utfihgtfvkgy"
			,"password"=>"kugtfkjhlh"
			,"prenom"=>"kjhygvkhgh"
			,"nom"=>"kghckghkjil"
			,"id_superieur"=>ATF::$usr->getID()
		));
		
		$i = array(
			1=>array(
				"id_user"=>ATF::$usr->getID()
				,"date"=>"2050-02-05 00:00:00"
				,"note_de_frais"=>"Pour les TU 1" 
			)
			,2=>array(
				"id_user"=>$idUser
				,"date"=>"2050-02-05 00:00:00"
				,"note_de_frais"=>"Pour les TU 2"
			)
		);
		$this->obj->i($i[1]);
		$this->obj->i($i[2]);
		$this->obj->q->reset()->setCount()->addOrder("note_de_frais.id_note_de_frais","desc");
		$r = $this->obj->select_all();
		$r = $r['data'];

		$this->assertEquals(2,count($r),"Erreur, on devrait voir les deux notes de frais !");
		$this->assertTrue($r[0]['canValid'],"Erreur, on devrait avoir canValid = TRUE !");
		$this->assertFalse($r[1]['canValid'],"Erreur, on devrait avoir canValid = FALSE !");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function test_can_update() {
				
		$ndfExtended = new ndfExtended();
		try {
			$error = NULL;
			$ndfExtended->can_update(666);
		} catch (errorATF $e) {
			$error = $e->getCode();	
		}
		$this->assertEquals(8766,$error,"Erreur de depassement de date non attrapee");
		
		$d = mktime(0,0,0,date('m'),19,date('Y'));
		$nf = array(
			"note_de_frais"=>"Test TU"
			,"date"=>date("Y-m-d H:i:s",$d)
			,"id_user"=>12
		);
		$id = $this->obj->i($nf);
		try {
			$error = NULL;
			$this->obj->can_update($id);
		} catch (errorATF $e) {
			$error = $e->getCode();	
		}
		$this->assertEquals(8764,$error,"Erreur de mauvais user non attrapee");
		
		$ndfExtended2 = new ndfExtended2();
		ATF::user()->u(array("id_user"=>12,"id_superieur"=>ATF::$usr->getID()));
		$this->assertTrue($ndfExtended2->can_update($id),"Erreur : le supérieur peut modifier");
		
		ATF::$usr->set("id_user",3);
		$this->assertTrue($ndfExtended2->can_update($id),"Erreur : le god peut tout faire");
		
		
	}
	
	public function test_canUpdate2() {
		$d = mktime(0,0,0,date('m'),19,date('Y'));
		$nf = array(
			"note_de_frais"=>"Test TU"
			,"date"=>date("Y-m-d H:i:s",$d)
			,"id_user"=>12
		);
		$id = $this->obj->i($nf);
		$ndfExtended2 = new ndfExtended2();
		ATF::$usr->set("id_user",ATF::$usr->getId());
		$error = false;
		try {
			$ndfExtended2->can_update($id);
		} catch (errorATF $e) {
			$error = true;
			$errorno = $e->getCode();	
		}
		$this->assertTrue($error,"Erreur non catché");
		$this->assertEquals(8765,$errorno,"Erreur : le propriétaire est le seul a pouvoir modifier, erreur 8765");
		
		ATF::$usr->set("id_user",12);
		$this->assertTrue($ndfExtended2->can_update($id),"Aucun cas présent, return true !");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_isExists() {
		$array = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050")
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
			)
		);
		$id = $this->obj->insert($array);
		
		$this->assertEquals($id,$this->obj->isExists(),"Erreur, la note de frais n'existe pas ?!");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_getDateReference() {
		$d = mktime(0,0,0,3,01,2011);  

		$this->assertEquals("2011-03",$this->obj->getDateReference($d),"Erreur 1");

		$d = mktime(0,0,0,9,01,2011);  
		$this->assertEquals("2011-09",$this->obj->getDateReference($d),"Erreur 2");

		$d = mktime(0,0,0,9,26,2011);  
		$this->assertEquals("2011-10",$this->obj->getDateReference($d),"Erreur 3");
		
		if (date('d',time())>=$this->obj->getJourLimit()) {
			$date = mktime(0,0,0,date('m')+1,1,date('Y'));	
		} else {
			$date = mktime(0,0,0,date('m'),1,date('Y'));	
		}
			
		$this->assertEquals(date('Y-m',$date),$this->obj->getDateReference(),"Erreur 4");

	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/
	public function test_getJourLimit() {
		$this->assertEquals(25,$this->obj->getJourLimit(),"Erreur !?");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_valid() {
		$array = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__etat"=>"ok","note_de_frais_ligne__dot__montant"=>500,"note_de_frais_ligne__dot__date"=>"2050-02-02 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 2 février 2050")
					,array("note_de_frais_ligne__dot__etat"=>"en_cours","note_de_frais_ligne__dot__montant"=>2500,"note_de_frais_ligne__dot__date"=>"2052-02-22 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 22 février 2052")
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
			)
		);
		$infos['id_note_de_frais'] = $this->obj->insert($array);
		
		$r = $this->obj->valid($infos);
		$this->assertTrue($r,"Erreur 1");
		$this->assertEquals("ok",$this->obj->select($infos['id_note_de_frais'],'etat'),"Erreur 2");
		
		foreach (ATF::note_de_frais_ligne()->ss('id_note_de_frais',$infos['id_note_de_frais']) as $k=>$i) {
			$this->assertEquals("ok",$i['etat'],"Erreur dans les lignes, elle n'est pas passé en OK");
		}
		
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_refus() {
		$array = array(
			"values_note_de_frais"=>array(
				"depenses"=>json_encode(array(
					array("note_de_frais_ligne__dot__etat"=>"nok","note_de_frais_ligne__dot__montant"=>1500,"note_de_frais_ligne__dot__date"=>"2050-02-12 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 12 février 2050")
					,array("note_de_frais_ligne__dot__etat"=>"en_cours","note_de_frais_ligne__dot__montant"=>2500,"note_de_frais_ligne__dot__date"=>"2052-02-22 00:00:00","note_de_frais_ligne__dot__objet"=>"Frais de 22 février 2052")
				))
			),
			"note_de_frais"=>array(
				"id_user"=>ATF::$usr->getID()
			)
		);
		$infos['id_note_de_frais'] = $this->obj->insert($array);
		
		$r = $this->obj->refus($infos);
		$this->assertTrue($r,"Erreur 1");
		$this->assertEquals("nok",$this->obj->select($infos['id_note_de_frais'],'etat'),"Erreur 2");
		foreach (ATF::note_de_frais_ligne()->ss('id_note_de_frais',$infos['id_note_de_frais']) as $k=>$i) {
			$this->assertEquals("nok",$i['etat'],"Erreur dans les lignes, elle n'est pas passé en NOK");
		}
	}
};

class ndfExtended extends note_de_frais {
	public function checkDateLimite($time,$nf) {
		return true;
	}
}

class ndfExtended2 extends note_de_frais {
	public function checkDateLimite($time,$nf) {
		return false;
	}
}

?>
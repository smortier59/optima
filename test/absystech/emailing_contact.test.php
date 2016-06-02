<?
include_once "emailing.test.php";

class emailing_contact_test extends emailing_test {
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_source,emailing_contact");
 		$this->obj = ATF::emailing_contact();
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	*/ 
	function tearDown() {
		ATF::db()->rollback_transaction(true);
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	*/ 
	public function test_getColsSpmPlugins() {
		$r = $this->obj->getColsSpmPlugins();
		
		$this->assertEquals("Civilité",$r[0]['label'],"Erreur du retour 1");
		$this->assertEquals("Nom",$r[1]['label'],"Erreur du retour 2");
		$this->assertEquals("Prénom",$r[2]['label'],"Erreur du retour 3");
	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 29-09-2010
	*/ 
	public function test_unregister() {
		$r = $this->obj->unregister($this->ec[0]['id_emailing_contact']);
		
		$this->assertEquals(1,$r,"Erreur de modification du contact 1");
		$this->assertEquals("non", $this->obj->select($this->ec[0]['id_emailing_contact'],'opt_in'),"Erreur : le contact 1 est toujours inscrit");

		$r = $this->obj->unregister(md5($this->ec[1]['id_emailing_contact']));
		
		$this->assertEquals(1,$r,"Erreur de modification du contact 2");
		$this->assertEquals("non", $this->obj->select($this->ec[1]['id_emailing_contact'],'opt_in'),"Erreur : le contact 2 est toujours inscrit");



	}

	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 06-12-2010
	*/ 
	function test_fromMD5() {
		$r = $this->obj->fromMD5(md5($this->ec[0]['id_emailing_contact']));
		$this->assertEquals($this->ec[0]['id_emailing_contact'],$r,"Erreur de FROM MD5: mauvais retour d'ID");
		$this->assertEquals(1,count($r),"Erreur de FROM MD5: aucun retour");
		
		$r = $this->obj->fromMD5(md5($this->ec[1]['id_emailing_contact']),"nom");
		$this->assertEquals($this->ec[1]['nom'],$r,"Erreur de FROM MD5: mauvais retour du nom");
		$this->assertEquals(1,count($r),"Erreur de FROM MD5: aucun retour");
	}
    
    public function test_exportBrutError() {
        $id = ATF::emailing_source()->i(array("emailing_source"=>"ES TU","id_user"=>ATF::$usr->getId()));
        try {
            $erreur = false;
            $infos['onglet'] = "gsa_emailing_source_".$id;
            $this->obj->export_brut($infos);
        } catch (error $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Pas d'erreur ?");
            
    }
     
    
    public function test_exportBrut() {
        $id = ATF::emailing_source()->i(array("emailing_source"=>"ES TU","id_user"=>ATF::$usr->getId()));
        $id_ec = ATF::emailing_contact()->i(array("email"=>"qjanon@absystech.fr","id_emailing_source"=>$id));
        
        $div='gsa_emailing_source_'.$id;
        $parent_class=ATF::emailing_source();
        $q=NULL;
        $view=NULL;
        $url_extra=NULL;
        $extra=NULL;
        $infos=$this->obj->genericSelectAll($div,$parent_class,$q,$view,$url_extra,$extra,array("emailing_contact.id_emailing_source"=>$id),NULL,ATF::_s(),false);
        
        
        //lancer l'initialisation de la récupération du fichier
        ob_start();
        $infos = array('onglet'=>$div);
        $this->obj->export_brut($infos,ATF::_s());
        
        //récupération des infos
        $fichier=ob_get_clean();
        $this->assertNotNull($fichier,"Erreur, rien en retour");    
    }
    
    public function test_contactsBySource() {
        $this->assertFalse($this->obj->contactsBySource(),"Pas d'id");
        
        $id = ATF::emailing_source()->i(array("emailing_source"=>"ES TU","id_user"=>ATF::$usr->getId()));
        $id_ec = ATF::emailing_contact()->i(array("email"=>"qjanon@absystech.fr","id_emailing_source"=>$id));
        $id_ec2 = ATF::emailing_contact()->i(array("email"=>"qjanon2@absystech.fr","id_emailing_source"=>$id));
        
        
        $this->assertEquals(2,$this->obj->contactsBySource($id,true),"Mauvais nombre en retour");
        $r = $this->obj->contactsBySource($id);
        
        $this->assertEquals("qjanon@absystech.fr", $r[0]['email'],"Mauvais contact");
        $this->assertEquals("qjanon2@absystech.fr", $r[1]['email'],"Mauvais contact 2");
    }
   
};
?>
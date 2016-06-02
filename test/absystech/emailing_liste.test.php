<?
include_once "emailing.test.php";

class emailing_liste_test extends emailing_test {	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-11-2010
	*/ 
	function setUp() {
		parent::setUp("emailing_source,emailing_contact,emailing_liste");
 		$this->obj = ATF::emailing_liste();		

		$options = array(
			"name"=>"Filtre TU"
			,"mode"=>"AND"
			,"conditions"=>array(
				array(
					"field"=>"emailing_contact.email"
					,"operand"=>"LIKE%"
					,"value"=>"tu"
				)
			)
			,"choix_join"=>"left");
		
		$this->id_filtre = ATF::filtre_optima()->i(array(
			"filtre_optima"=>"Filtre TU pour emailing liste"
			,"id_module"=>ATF::module()->from_nom("emailing_liste")
			,"id_user"=>ATF::$usr->getID()
			,"options"=>serialize($options)
		));
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
    * @date 12-04-2010
    */
    public function test_can_update() {
        $this->assertTrue($this->obj->can_update($this->el['id_emailing_liste']),"Erreur : on devrait pouvoir la modifier !");  
    }
 
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 12-04-2010
    */ 
    public function test_insert() {
        // Test liste seule
        $id = $this->obj->insert(array("emailing_liste"=>"Liste TU n°1"));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 1 aurait-elle foiré ?");
        $this->assertNull(ATF::emailing_liste_contact()->ss("id_emailing_liste",$id),"Erreur : je n'ai pas mis de contact, c'est pas normal qu'il y en ai sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'1 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'1 Aucun doublon, donc 0 en premier caractère.');
        
        // Test liste avec source
        $id2 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°2","selNodes"=>"source_".$this->es['id_emailing_source']));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 2 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id2);
        $this->assertNotNull($r,"Erreur 1 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'2 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'2 Aucun doublon, donc 0 en premier caractère.');
        
        // Test liste avec filtre
        $id3 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°3","selNodes"=>"filtre_".$this->id_filtre));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 3 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id3);
        $this->assertNotNull($r,"Erreur 2 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'3 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'3 Aucun doublon, donc 0 en premier caractère.');
        
        // Test liste avec liste
        $id4 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°4","selNodes"=>"liste_".$id2));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 4 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id4);
        $this->assertNotNull($r,"Erreur 2 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'4 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'4 Aucun doublon, donc 0 en premier caractère.');
        
        // Test liste avec source+filtre+liste + doublons en pagaille
        $id5 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°5","selNodes"=>"liste_".$id2.",liste_".$id3.",source_".$this->es['id_emailing_source'].",source_".$this->es['id_emailing_source'].",filtre_".$this->id_filtre));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 5 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id5);
        $this->assertNotNull($r,"Erreur 2 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'5 Le nombre de notices est incorrect');
        $this->assertEquals(32,substr($notices[0]['msg'],0,2),'5 Plein de doublon, donc 32 en premier caractère.');
        
        // Test erreur de création de liste
        try {
            $id6 = $this->obj->insert(array("description"=>"Liste TU n°6"));
            $error = false;
        } catch (error $e) {
            $error = true;
        }
        $this->assertTrue($error,"Devrait généré une erreur");
        
    }   
     
    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2013
    */ 
    public function test_updateWithSource() {
        // Insertion liste
        $id = $this->obj->insert(array("emailing_liste"=>"Liste TU n°1"));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 1 aurait-elle foiré ?");
        $this->assertNull(ATF::emailing_liste_contact()->ss("id_emailing_liste",$id),"Erreur : je n'ai pas mis de contact, c'est pas normal qu'il y en ai sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'1 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'1 Aucun doublon, donc 0 en premier caractère.');
        
        
        // Test liste avec source
        $this->obj->update(array("id_emailing_liste"=>$id,"emailing_liste"=>"Liste TU n°2","selNodes"=>"source_".$this->es['id_emailing_source']));
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id);
        $this->assertNotNull($r,"Erreur 1 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'2 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'2 Aucun doublon, donc 0 en premier caractère.');
    }
    
        
    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2013
    */ 
    public function test_updateWithFiltre() {
        // Insertion liste
        $id = $this->obj->insert(array("emailing_liste"=>"Liste TU n°1"));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 1 aurait-elle foiré ?");
        $this->assertNull(ATF::emailing_liste_contact()->ss("id_emailing_liste",$id),"Erreur : je n'ai pas mis de contact, c'est pas normal qu'il y en ai sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'1 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'1 Aucun doublon, donc 0 en premier caractère.');
 
         // Test liste avec filtre
        $id3 = $this->obj->update(array("id_emailing_liste"=>$id,"emailing_liste"=>"Liste TU n°3","selNodes"=>"filtre_".$this->id_filtre));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 3 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id3);
        $this->assertNotNull($r,"Erreur 2 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'3 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'3 Aucun doublon, donc 0 en premier caractère.');
    }
    
    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2013
    */ 
    public function test_updateWithListe() {
        // Insertion liste
        $id = $this->obj->insert(array("emailing_liste"=>"Liste TU n°1"));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 1 aurait-elle foiré ?");
        $this->assertNull(ATF::emailing_liste_contact()->ss("id_emailing_liste",$id),"Erreur : je n'ai pas mis de contact, c'est pas normal qu'il y en ai sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'1 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'1 Aucun doublon, donc 0 en premier caractère.');
 
        $id2 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°2","selNodes"=>"source_".$this->es['id_emailing_source']));
        $notices = ATF::$msg->getNotices();
        $r2 = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id2);
        
        // Test liste avec liste
        $this->obj->update(array("id_emailing_liste"=>$id,"emailing_liste"=>"Liste TU n°4","selNodes"=>"liste_".$id2));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 4 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id);
        $this->assertNotNull($r,"Erreur 2 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'4 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'4 Aucun doublon, donc 0 en premier caractère.');
    }
    
    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2013
    */ 
    public function test_updateWithAll() {
        // Insertion liste
        $id = $this->obj->insert(array("emailing_liste"=>"Liste TU n°1"));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 1 aurait-elle foiré ?");
        $this->assertNull(ATF::emailing_liste_contact()->ss("id_emailing_liste",$id),"Erreur : je n'ai pas mis de contact, c'est pas normal qu'il y en ai sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'1 Le nombre de notices est incorrect');
        $this->assertEquals(0,substr($notices[0]['msg'],0,1),'1 Aucun doublon, donc 0 en premier caractère.');
 
        $id2 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°2","selNodes"=>"source_".$this->es['id_emailing_source']));
        $id3 = $this->obj->insert(array("emailing_liste"=>"Liste TU n°3","selNodes"=>"filtre_".$this->id_filtre));
        $notices = ATF::$msg->getNotices();

        // Test liste avec source+filtre+liste + doublons en pagaille
        $id5 = $this->obj->update(array("id_emailing_liste"=>$id,"emailing_liste"=>"Liste TU n°5","selNodes"=>"liste_".$id2.",liste_".$id3.",source_".$this->es['id_emailing_source'].",source_".$this->es['id_emailing_source'].",filtre_".$this->id_filtre));
        $this->assertNotNull($id,"Pas d'id de retourné, l'insertion 5 aurait-elle foiré ?");
        $r = ATF::emailing_liste_contact()->ss("id_emailing_liste",$id5);
        $this->assertNotNull($r,"Erreur 2 :Il devrait y avoir des contacts sur cette liste");
        $this->assertEquals(8,count($r),"Erreur :Il devrait y avoir exactement 8 contacts sur cette liste");
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),1,'5 Le nombre de notices est incorrect');
        $this->assertEquals(32,substr($notices[0]['msg'],0,2),'5 Plein de doublon, donc 32 en premier caractère.');
        
        
    }
	
    
    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 21-01-2013
    */ 
    public function test_updateWithError() {
        // Insertion liste
        $id = $this->obj->insert(array("emailing_liste"=>"Liste TU n°1"));
        $erreur = false;
        try {
            $this->obj->update(array("id_emailing_liste"=>"fgfdftrgbdfbsbgsdfbgstheytvrthztesghczethervyhrtsgshtrhbdfgb","emailing_liste"=>"Liste TU n°5","toto"=>"titi"));
        } catch (error $e) {
            $erreur = true;
        }
            
        $notices = ATF::$msg->getNotices();
        $this->assertTrue($erreur,"L'erreur ne s'est pas declenché");
        
    }
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 12-04-2010
    */
    public function test_getNodesToFill() {
        ATF::_g('vide',true);
        $this->assertEquals("[]",$this->obj->getNodesToFill(),"Erreur : aucun node il devrait y avoir ici !");  
        
        ATF::filtre_optima()->truncate();
        $this->id_filtre2 = ATF::filtre_optima()->i(array(
            "filtre_optima"=>"Filtre TU pour emailing liste"
            ,"id_module"=>ATF::module()->from_nom("emailing_contact")
            ,"id_user"=>ATF::$usr->getID()
            ,"options"=>serialize($options)
        ));

        ATF::_g('vide',false);
        $r1 = $this->obj->getNodesToFill();
        $r1 = json_decode($r1,true);
        $this->assertNotNull($r1[0],"Erreur : Les sources ne sont pas là !");   
        $this->assertEquals("source",$r1[0]["id"],"Erreur : Les sources ne sont pas a la bonne position !");    
        $this->assertNotNull($r1[1],"Erreur : Les filtre ne sont pas là !");    
        $this->assertEquals("filtre",$r1[1]["id"],"Erreur : Les filtre ne sont pas a la bonne position !"); 
        $this->assertNotNull($r1[2],"Erreur : Les liste ne sont pas là !"); 
        $this->assertEquals("liste",$r1[2]["id"],"Erreur : Les liste ne sont pas a la bonne position !");   
        
        ATF::filtre_optima()->truncate();
        ATF::emailing_source()->truncate();
        ATF::emailing_liste()->truncate();
        $source = array(
            "text" => "Sources"
            ,"cls" => "folder"
            ,"id" => "source"
           , "children" => array()
        );
        $filtre = array(
            "text" => "Filtres de contact d'emailing"
            ,"cls" => "folder"
            ,"id" => "filtre"
           , "children" => array()
        );
        $liste = array(
            "text" => "Liste de diffusion existante"
            ,"cls" => "folder"
            ,"id" => "liste"
           , "children" => array()
        );
        $r = "[".json_encode($source).",".json_encode($filtre).",".json_encode($liste)."]";
        $r2 = $this->obj->getNodesToFill();
        $this->assertEquals($r,$r2,"Erreur : Le JSON est foireux quand il n'y a ni source, ni filtre, ni liste !"); 
    }
    
     
};
?>
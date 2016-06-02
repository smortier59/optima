<?
/** 
* @deprecated Classe stock absystech test
* @since mardi 24 mai 2011
* @version 1.0
* @author MOUAD EL HIZABRI
* @package test
*/
class stock_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/**
	* instance de la classe stock
	* pour l'isolement des tests
	* @access private
	*/
	private $_stock;
	private $_stock_etat;	
	private $_devis;
	private $_affaire;
	private $_commande;
	private $_bon_de_commande;
	private $_bon_de_commande_ligne;
	private $_livraison;
	private $_livraison_ligne;
	
	
	/** 
	* test apres chaque requete SQL d'un jeu de test 
	*/
	private function requete_valide($table){
		return $this->assertNotNull($this->_.$table["id_".$table],"La requête de ".$table." ne se crée pas... - assert ");
	}
	
	
	// Initialisation d'un jeu de test 
	private function environnement_test(){
		ATF::db()->truncate("stock");
		//devis
		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis_stock';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"REF_DEVIS","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');
		
		//                    [instance DEVIS]
		$this->_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->requete_valide("devis");
		
		//                   [instance AFFAIRE]
		$this->_affaire = ATF::devis()->select($this->_devis,"id_affaire");
		$this->requete_valide("affaire");

		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->_affaire;
		$commande["commande"]["id_devis"]=$this->_devis;
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"REF_Produit","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);
		
		//                   [instance COMMANDE]
		$this->_commande = ATF::commande()->insert($commande,$this->s);
		$this->requete_valide("commande");
		$this->commande=$commande;
	 	//bon_de_commande
		$bon_de_commande["bon_de_commande"]=$this->commande["commande"];
		$bon_de_commande["bon_de_commande"]["ref"]="REF_bon_de_commande";
		$bon_de_commande["bon_de_commande"]["id_commande"] = $this->_commande; 
		$bon_de_commande["bon_de_commande"]["id_fournisseurFinal"]= 1589;
		$bon_de_commande["bon_de_commande"]["id_fournisseur"]= 1589;	 	
		$bon_de_commande["values_bon_de_commande"]=array("produits"=>'[{"bon_de_commande_ligne__dot__ref":"REF_Produit","bon_de_commande_ligne__dot__produit":"Tu_bon_de_commande","bon_de_commande_ligne__dot__quantite":"15","bon_de_commande_ligne__dot__prix":"10","bon_de_commande_ligne__dot__prix_achat":"10","bon_de_commande_ligne__dot__id_fournisseur":"1","bon_de_commande_ligne__dot__serial":"AZSQDERF12","bon_de_commande_ligne__dot__id_compte_absystech":"1","bon_de_commande_ligne__dot__marge":97.14,"bon_de_commande_ligne__dot__id_fournisseur_fk":"1"}]');
		unset($bon_de_commande["bon_de_commande"]["prix_achat"]);
		unset($bon_de_commande["bon_de_commande"]["id_devis"]);
		$this->bon_de_commande=$bon_de_commande;
		
		//                   [instance BON DE COMMANDE]
		$this->_bon_de_commande = ATF::bon_de_commande()->insert($this->bon_de_commande,$this->s);
		$this->requete_valide("bon_de_commande");
		
		//                   [instance BON DE COMMANDE LIGNE]
		$this->_bon_de_commande_ligne = ATF::bon_de_commande_ligne()->ss("id_bon_de_commande",$this->_bon_de_commande);
		$this->requete_valide("bon_de_commande_ligne");

		//				         [instance STOCK]
		$this->_stock = $this->obj->sa();
		$this->requete_valide("stock");
		
		//                       [instance STOCK ETAT]
		$this->_stock_etat = ATF::stock_etat()->sa();
		$this->requete_valide("stock_etat");	
	}
	
	
	/**
	* jeu de test pour une livraison
	*/
	private function init_livraison(){
		$this->livraison["livraison"]["id_commande"] = $this->_commande;
		$this->livraison["livraison"]["id_societe"]=$this->id_societe;
		$this->livraison["livraison"]["id_affaire"]=$this->_affaire;
		$this->livraison["livraison"]["date"]=date('Y-m-d');
		$this->livraison["values_livraison"]=array("produits"=>'[{"stock__dot__id_stock_fk":'.$this->_stock[0]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"kokfghghhghhhgys"},{"stock__dot__id_stock_fk":'.$this->_stock[1]["id_stock"].',"stock__dot__ref":"1201","stock__dot__libelle":"manette","stock__dot__serial":"klmlkmlkh"}]');	
		//                      [instance LIVRAISON]
		$this->_livraison = ATF::livraison()->insert($this->livraison);
		$this->requete_valide("livraison");
		
		// 						[instance LIVRAISON LIGNE]	
		$this->_livraison_ligne= ATF::livraison_ligne()->sa();
		$this->requete_valide("livraison_ligne");
		
		//                       [instance STOCK ETAT]
		$this->_stock_etat = ATF::stock_etat()->sa();
		$this->requete_valide("stock_etat");	
	}


	/**
	* cette méthode est appelée avant le test.
	* @access protected
	*/
	protected function setUp(){
		ATF::constante()->setValue('__STORE_URL__', 'http://dev.store.absystech.fr/');
		ATF::constante()->setValue('__STORE_PATH__', '/home/www/absystech.fr-store/');
		$this->initUser();
		$this->environnement_test();
		$this->ref = 'XW477AT';
	}
	
		
	/**
	* cette méthode est appelée apres le test.
	* @access protected
	*/
	protected function tearDown(){
//echo ATF::db()->numberTransaction();
		ATF::db()->rollback_transaction(true);
		
		//Flush des notices
		ATF::$msg->getNotices();
		ATF::$msg->getWarnings();
	}
/*
    // @author Quentin JANON <qjanon@absystech.fr>
    public function test_updateForMagentoDelete() {
        $this->test_updateForMagentoSend();
                
         // Séléction de l'un des stock insérer 
        $this->obj->q->reset()->where("ref","BONANA");
        $r = $this->obj->sa();

        $p1ID = $r[0]["id_stock"];
        $p2ID = $r[1]["id_stock"];
        
        $params = array("id_stock"=>$p1ID,"nb"=>"yes");
        
        $this->obj->updateForMagento($params);
        
        $p1After = $this->obj->select($p1ID);
        $p2After = $this->obj->select($p2ID);
        
        $this->assertEquals("non",$p1After["to_magento"],"Le premier produit n'a pas le flag to_magento a non");
        $this->assertEquals("divers",$p1After["categories_magento"],"Le premier produit de même ref n'as pas vu sa catégorie conservé");

        $this->assertEquals("non",$p2After["to_magento"],"Le deuxième produit n'a pas le flag to_magento a oui");
        $this->assertEquals("divers",$p2After["categories_magento"],"Le deuxième produit de même ref n'as pas vu sa catégorie conservé");

    }     
   
    // @author Quentin JANON <qjanon@absystech.fr>
    public function test_updateForMagentoReplace() {
        $this->test_updateForMagentoSend();
                
         // Séléction de l'un des stock insérer 
        $this->obj->q->reset()->where("ref","BONANA");
        $r = $this->obj->sa();

        $p1ID = $r[0]["id_stock"];
        $p2ID = $r[1]["id_stock"];
        
        $params = array("id_stock"=>$p1ID);
        
        $this->obj->updateForMagento($params);
        
        $p1After = $this->obj->select($p1ID);
        $p2After = $this->obj->select($p2ID);
        
        $this->assertEquals("non",$p1After["to_magento"],"Le premier produit n'a pas le flag to_magento a non");
        $this->assertEquals("divers",$p1After["categories_magento"],"Le premier produit de même ref n'as pas vu sa catégorie conservé");

        $this->assertEquals("oui",$p2After["to_magento"],"Le deuxième produit n'a pas le flag to_magento a oui");
        $this->assertEquals("divers",$p2After["categories_magento"],"Le deuxième produit de même ref n'as pas vu sa catégorie conservé");
    }    
    */
    // @author Quentin JANON <qjanon@absystech.fr>
    public function test_getQuantity() {
        $infos = array("libelle" => "toto", "quantite" => 6, "ref" => "Super Heros", "etat" => "stock", 'to_magento' => 'oui');
        $this->obj->insert($infos);
        $this->assertFalse($this->obj->getQuantity(NULL), "Erreur, pas de ref donc FALSE");
        $this->assertEquals(6, $this->obj->getQuantity("Super Heros"), "Erreur, la quantité de ref Super Heros doit être à 6");
    }

    // @author Antoine MAITRE <amaitre@absystech.fr>
    public function test_getMarque() {
        $test1 = $this->obj->getMarque(NULL);
        $test2 = $this->obj->getMarque("Toto fais du pédalo");
        $test3 = $this->obj->getMarque("Hp et Lenovo");
        $test4 = $this->obj->getMarque("Hp est supérieur");
        $this->assertNull($test1, "Erreur, la marque doit être inexistante si le param est NULL");
        $this->assertNull($test2, "Erreur, la marque doit être inexistante, aucune marque n'est présente dans le param");
        $this->assertEquals("Lenovo,HP", $test3, "Erreur dans le T.U getMarque, marque incorrecte.");
        $this->assertEquals("HP", $test4, "Erreur dans le T.U getMarque, marque incorrecte.");
    }

    public function test_getDataFromIcecatError910() {
        $erreur = false;
        try {
            $this->obj->getDataFromIcecat("iydufcjthf","toto",1);
        } catch (error $e) {
            $erreur = true;
        }
    }
    

    public function test_getImageFromIcecatDone() {
        $s = array(
            "ref" => "EF227AT",
            "libelle" => "Moniteur LCD 20 pouces HP LP2065"
        );

        $id = $this->obj->i($s);
        
        $this->assertNotNull($id,"Le produit pour le test ne s'est pas insérer");
        
        $dom = new DOMDocument();
        $dom->load("http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/435466.xml"); // Fiche avec tout ce qui faut comme infos


        $this->obj->getImageFromIcecat($dom,$id);
        
        $this->assertFileExists($this->obj->filepath($id, "photo"),"Le fichier image n'est pas sur le serveur");
    }
    
    public function test_getImageFromIcecatNoImage() {
        $s = array(
            "ref" => "WB708ET_EF227AT",
            "libelle" => "Moniteur LCD 20 pouces HP LP2065"
        );

        $id = $this->obj->i($s);
        unlink($this->obj->filepath($id, "photo"));
        $this->assertNotNull($id,"Le produit pour le test ne s'est pas insérer");

        $dom = new DOMDocument();
        $dom->load("http://openIcecat-xml:freeaccess@data.icecat.biz/export/freexml.int/FR/4142895.xml"); // Fiche sans photo
        
        $this->obj->getImageFromIcecat($dom,$id);
        
        $this->assertFileNotExists($this->obj->filepath($id, "photo"),"Le fichier image est pas sur le serveur ? WTF !?");
        $r = ATF::$msg->getWarnings();
        $this->assertEquals(1,count($r),"Il devrait y avoir 1 warning");
        $this->assertEquals("Erreur création image",$r[0]['title'],"Le titre du warning n'est pas correct");
    }

    public function test_ParserIcecatNoMarque() {
        $erreur = false;
        try {
            $d = array();
            $this->obj->ParserIcecat($d);
        } catch (error $e) {
            $erreur = true;
        }
        
        $this->assertTrue($erreur);
        $this->assertEquals(909,$e->getCode());
        
    }
        
    public function test_ParserIcecat() {
        $s = array(
            "ref" => "EF227AT",
            "libelle" => "Moniteur LCD 20 pouces HP LP2065"
        );

        $id = $this->obj->i($s);
        
        $this->assertNotNull($id,"Le produit pour le test ne s'est pas insérer");

        $d = array("ref" => "EF227AT","marque"=>"HP","id_stock"=>$id);
        $this->assertTrue($this->obj->ParserIcecat($d));
        
        $this->assertArrayHasKey("description", $d);
        $this->assertArrayHasKey("short_description", $d);
        $this->assertArrayHasKey("poids", $d);
        
    }

    public function test_switch_stock(){
        $oldid = $this->_stock[0]["id_stock"];
        $oldAffaire = $this->_stock[0]["id_affaire"];
        $oldbdcl = $this->_stock[0]["id_bon_de_commande_ligne"];
        $infos = array("id" => $this->obj->cryptId($oldid),
                       "serial" => "toootoootoootoootoootoooto",
                       "affaire"=> ATF::affaire()->cryptId($this->_stock[0]["id_affaire"]));
       
        $return = $this->obj->switchStock($infos);        
        
        $this->assertEquals("error",$return);

        $this->obj->u(array("id_stock" => $oldid , "serial" => "SerialTest1"));
        $this->_stock= $this->obj->sa();


        $newStock = $this->_stock[0];
        unset($newStock["id_stock"], $newStock["id_bon_de_commande_ligne"], $newStock["id_affaire"] );
        $newStock["ref"] = "Ref_produit autre";
        $newStock["serial"] = "toootoootoootoootoootoooto";

        $id = $this->obj->i($newStock);    
        ATF::stock_etat()->i(array("date" => date("Y-m-d H:i:s"), "id_stock" => $id , "etat" => "stock"));
        
        $return = $this->obj->switchStock($infos);


        $newStock = $this->obj->select($id);
        ATF::stock_etat()->q->reset()->where("id_stock" , $id);
        $etatNewStock = ATF::stock_etat()->select_all(); 
        $oldStock = $this->obj->select($oldid);
        ATF::stock_etat()->q->reset()->where("id_stock" , $oldid);
        $etatOldStock = ATF::stock_etat()->select_all();
        $etatOldStock = $etatOldStock[0]; 

        $this->assertEquals(NULL,$oldStock["id_bon_de_commande_ligne"], "1 - Id bon_de_commande_ligne doit etre null");
        $this->assertEquals(NULL,$oldStock["id_affaire"], "2 - Id affaire doit etre null");
        
        $this->assertEquals($oldbdcl ,$newStock["id_bon_de_commande_ligne"], "3 - Id bon_de_commande_ligne ne doit pas etre null");
        $this->assertEquals($oldAffaire ,$newStock["id_affaire"], "4 - Id affaire ne doit pas etre null");        
        

        $this->assertEquals("reception" , $etatNewStock[0]["etat"] , "5 - etat new stock incorrect");
        $this->assertEquals("Switch de stock avec stock Serial : SerialTest1" , $etatNewStock[0]["commentaire"] , "6 - commentaire new stock incorrect");
        
        $this->assertEquals("stock" , $etatOldStock["etat"] , "7 - etat old stock incorrect");
        $this->assertEquals("Switch de stock affaire Tu_devis_stock" , $etatOldStock["commentaire"] , "8 - commentaire old stock incorrect");
    }


    public function test_checkInventaire2013(){
        $idstock = $this->_stock[0]["id_stock"];
        $this->obj->checkInventaire2013(array("id_stock" => $idstock));
        $this->assertEquals("oui" , $this->obj->select($idstock , "inventaire2013") , "Error checkInventaire");
        
        ATF::stock_etat()->q->reset()->where("id_stock", $idstock);
        $se = ATF::stock_etat()->select_all();
        $this->assertEquals("Inventaire AT ".date("Y") , $se[0]["commentaire"] , "Error commentaire");

        
    }
    
   
/*    public function test_updateForMagentoSendError() {

        $infos = array("libelle" => "HP TU", "ref" => "BONANA", 'quantite' => 2, "prix"=>115);
        $this->obj->insert($infos);
        
        // Séléction de l'un des stock insérer 
        $this->obj->q->reset()->where("ref","BONANA");
        $r = $this->obj->sa();

        $p1ID = $r[0]["id_stock"];
        $p2ID = $r[1]["id_stock"];
        
        $p1 = array(
            "id_stock"=>$p1ID,
            "description"=>"Description de TU",
            "short_description"=>"Short_description de TU"
        );
        $this->obj->u($p1);
        
        $params = array("id_stock"=>$p1ID,"action"=>"send","icecatParsing"=>true,"prix"=>189,"categories_magento"=>"divers");
        $erreur = false;
        try {
            $this->obj->updateForMagento($params);
        } catch (error $e) {
            $erreur = true;
        }

        $this->assertTrue($erreur,"Pas d'erreur de remontée");
    }        
*/

    public function test_getDataFromIcecat() {
        
        $s = array(
            "ref" => "EF227AT",
            "libelle" => "Moniteur LCD 20 pouces HP LP2065"
        );

        $id = $this->obj->i($s);
        
        $this->assertNotNull($id,"Le produit pour le test ne s'est pas insérer");

        $r = $this->obj->getDataFromIcecat("HP","EF227AT",$id);

        $this->assertNotNull($r['short_description'],"Pas de short_description ce n'est pas normal");
        $this->assertNotNull($r['description'],"Pas de description ce n'est pas normal");
        $this->assertEquals("5,75 kg",$r['poids'],"Pas le bon poids ce n'est pas normal");
                
    }
    
    //* @test Test du constructeur stock. 
    public function test_stock_constructeur(){
        $this->_stock = new stock();    
    }
    

    //* @test Test de setDelivered stock livraison du stock. 
    public function test_setDelivered_partielement_terminee(){
        $this->init_livraison();        
        //test de la methode setDelivered
        $result = $this->obj->setDelivered(array("id_stock"=>$this->_stock[0]["id_stock"]));
        if(is_numeric($result)){
            $new_etat_stock= ATF::stock_etat()->select($result,'etat');
            $new_etat_livraison= ATF::livraison_ligne()->select($this->_livraison_ligne[0]["id_livraison_ligne"],'etat');
            //test etat stock: le stock est livré
            $this->assertEquals("livr",$new_etat_stock,"le stock doit passer en livrer si il est en livraison.");
            //test etat livraison: la livraison est terminer
            $this->assertEquals("termine",$new_etat_livraison,"la livraison doit passer en terminer si elle est en cours.");
        }   
    }
    
    
    //* @test Test de setDelivered stock livraison du stock. 
    public function test_setDelivered_terminee(){
        $this->init_livraison();        
        //test de la methode setDelivered
        $result = $this->obj->setDelivered(array("id_stock"=>$this->_stock[0]["id_stock"]));
        $result_2 = $this->obj->setDelivered(array("id_stock"=>$this->_stock[1]["id_stock"]));
        if(is_numeric($result) && is_numeric($result_2) ){
            $new_etat_stock= ATF::stock_etat()->select($result,'etat');
            $new_etat_livraison= ATF::livraison_ligne()->select($this->_livraison_ligne[0]["id_livraison_ligne"],'etat');
            //test etat stock: le stock est livré
            $this->assertEquals("livr",$new_etat_stock,"le stock doit passer en livrer si il est en livraison.");
            //test etat livraison: la livraison est terminer
            $this->assertEquals("termine",$new_etat_livraison,"la livraison doit passer en terminer si elle est en cours.");
        }   
        try {
            $error = NULL;
            $result_2 = $this->obj->setDelivered(array("id_stock"=>$this->_stock[0]["id_stock"]));
        } catch (errorStock $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(901,$error,"erreur etat identique");
    }


    //* @test Test de notice_content stock.
    public function test_notice_content(){
        $notice_test = $this->obj->notice_content("notice test","cool notice",$this->_stock[0]["id_stock"]);
        $notice_1 = ATF::$msg->getNotices();
        $this->assertTrue(is_array($notice_1),"assert notice_contenent n'est pas un tableau");

    }

    //* @test Test de setReceived : reception du stock cas normal.  
    public function test_setReceived_cas_normal(){
        //id_stock erroné
        try {
            unset($this->_stock[0]["id_stock"]);
            $result = $this->obj->setReceived(array("id_stock"=>$this->_stock[0]["id_stock"]));
        } catch (error $e) {
            $erreur_capter = $e->getCode();
        }
        $this->assertEquals(900,$erreur_capter,"ERREUR NON ATTRAPPEE (id_stock erroné)");
        // error: absence du serial
        try {
            $result = $this->obj->setReceived(array("id_stock"=>$this->_stock[1]["id_stock"]));
        } catch (error $e) {
            $erreur_capter = $e->getCode();
        }
        $this->assertEquals(900,$erreur_capter,"ERREUR NON ATTRAPPEE (serial non insere)");
        //correction d'erreur
        $this->obj->update(array("formulaire"=>false,"serial"=>"EZFGTRB1245T","id_stock"=>$this->_stock[1]["id_stock"]),$this->s);
        if(is_numeric($result)){ 
            //test d'etat stock
            $new_etat= ATF::stock_etat()->select($result,'etat');
            $this->assertEquals("stock",$new_etat,"le stock doit passer en stock si il est en reception.");
        }
    }
    
    //* @test Test de setReceived : reception du stock cas normal.  
    public function test_setReceived_avec_serial(){
        $cadre_refreshed=array();
        $inf = array("serial"=>"EZFGTRB1245T","id_stock"=>$this->_stock[0]["id_stock"]);
        //mise à jours du stock avec un nouveau serial
        $stock_1_up = $this->obj->update($inf,$this->s,NULL,$cadre_refreshed);
        //application de la methode pour passer le stock de reception-->stock
        $result_2 = $this->obj->setReceived(array("id_stock"=>$this->_stock[0]["id_stock"]));
        if(is_numeric($result_2)){
            //test d'etat stock
            $new_etat= ATF::stock_etat()->select($result_2,'etat');
            $this->assertEquals("stock",$new_etat,"le stock doit passer en stock si il est en reception.");
        }
        try {
            $error = NULL;
            $result_2 = $this->obj->setReceived(array("id_stock"=>$this->_stock[0]["id_stock"]));
        } catch (errorStock $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(901,$error,"erreur etat identique");
    }


    //* @test Test du select_all stock. 
    public function test_select_all(){
        //les lignes de stock existantes
        $data = $this->obj->select_all();
        //test nombre de stock
        $this->assertEquals(15,count($data),"stock select all assert 1");        
        //test premier stock    
        $stok_1 = $data[4];
        $this->assertTrue(is_array($stok_1));
        $this->assertEquals("reception",$stok_1["etat"],"stock assert etat stock_1");       
        //nouveau stock
        $new_stock = array(
                 "id_affaire" => $this->_affaire
                ,"ref" => "efrgte"
                ,"libelle" => "Imprimante TMS"
                ,"date_achat" => date("Y-m-d")
                ,"prix" => "230"
                ,"serial" => "FRT2GHHJ45HJ"
                ,"etat" => "livraison"
                ,"adresse_mac" => "FGHRGHFDFDG4"
                ,"date_fin_immo" => "2010-12-11"
                ,"affectation" => "affectation"
                ,"serialAT" => "084530-151010-00-AT"
         );
        $this->_stock = $this->obj->insert($new_stock);
        $this->requete_valide("stock"); 
        //nouveau stock
        $new_stock2 = array(
                 "id_affaire" => $this->_affaire
                ,"ref" => "efsdfgrgte"
                ,"libelle" => "Imprimante TMS"
                ,"date_achat" => date("Y-m-d")
                ,"prix" => "230"
                ,"serial" => "FRT2dsfgsdfGHHJ45HJ"
                ,"etat" => "livraison"
                ,"adresse_mac" => "FGHRDFDG4"
                ,"date_fin_immo" => "2010-12-11"
                ,"affectation" => "affectation"
                ,"serialAT" => "084530-1ssdfg51010-00-AT"
                ,"etat"=>"stock"
         );
        $this->_stock2 = $this->obj->insert($new_stock2);
        $this->requete_valide("stock"); 
        $this->obj->q->setCount();  
        $r =$this->obj->select_all();
        $data = $r['data'];
        $this->assertEquals(17,count($data),"2 select all assert ");
        //test dernier stock    
        $stok_end = $data[1];
        $stok_end2 = $data[0];
        $this->assertTrue(is_array($stok_end));
        $this->assertEquals("livraison",$stok_end["etat"],"assert etat stock_6");   
        $this->assertEquals("stock",$stok_end2["etat"],"assert etat stock_7");   
        $this->assertArrayHasKey("allowLivre",$data[0],"Il manque le allowLivre en retour");    
    }

        //*@test Test de insert stock, redirection vers l'affaire. 
    public function test_insert_affaire(){
        //nouveau stock
        $cadre_refreshed=array();
        $new_stock = array(
                "ref" => "efrgte"
                ,"libelle" => "Imprimante TMS"
                ,"date_achat" => "14-77-1222"
                ,"prix" => "230"
                ,"etat" => "livroz"
                ,"serial" => "FRT2GHHJ45HJ"
                ,"adresse_mac" => "FGHRGHFDFDG4"
                ,"date_fin_immo" => "2010-22-11"
                ,"redirection_custom"=>true
                ,"id_affaire"=>$this->_affaire

         );
         //erreur 1: la date_achat n'a pas le bon type 
         try {
            $result = $this->obj->insert($new_stock,$this->s,NULL);
        } catch (error $e) {
            //debugage avec le message d'erreur
            $erreur_capter = $e->getMessage();
            ATF::db()->commit_transaction(true);
            $erreur = $e->getCode();
        }
        $this->assertEquals(102,$erreur,"ERREUR NON ATTRAPPEE 1");
        $new_stock["date_achat"] = date("Y-m-d");
        
        //erreur 2: etat du stock enixistant
         try {
            $result = $this->obj->insert($new_stock,$this->s,NULL);
        } catch (error $e) {
            ATF::db()->commit_transaction(true);
            $erreur = $e->getcode();
        }
        $this->assertEquals(102,$erreur,"ERREUR NON ATTRAPPEE 2");
        $new_stock["etat"] = "livr";
        
        //erreur 3: date fin immo incorrecte
         try {
            $result = $this->obj->insert($new_stock,$this->s,NULL);
        } catch (error $e) {
            ATF::db()->commit_transaction(true);
            $erreur = $e->getcode();
        }
        $this->assertEquals(102,$erreur,"ERREUR NON ATTRAPPEE 3");
        $new_stock["date_fin_immo"] = "2012-12-12";
        
        //insertion final
         try {
            $result = $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            ATF::db()->commit_transaction(true);
            $erreur = $e->getcode();
        }
        $this->assertEquals(102,$erreur,"ERREUR NON ATTRAPPEE 4");
    }
    
    
    //*@test Test de insert stock, redirection vers select_all. 
    public function test_insert_select_all(){
        //nouveau stock
        $cadre_refreshed=array();
        $new_stock = array(
                "ref" => "efrgte"
                ,"libelle" => "Imprimante TMSTR"
                ,"date_achat" => "1999-12-12"
                ,"prix" => "230"
                ,"etat" => "livraison"
                ,"serial" => "FRT2GHHJ45HJ"
                ,"adresse_mac" => "FGHRGHFDFDG4"
                ,"date_fin_immo" => "2012-12-12"
                ,"id_affaire"=>$this->_affaire
         );
         //insertion 
         $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
         $this->requete_valide("stock");     
    }
    
    
    //* @test Test de insert stock, redirection vers module stock. 
    public function test_insert_select_all_avec_direction(){
        //nouveau stock
         $cadre_refreshed=array();
         $redirection_custom=true;
        $new_stock = array(
                "ref" => "efrgte"
                ,"libelle" => "Imprimante TMS"
                ,"prix" => "230"
                ,"etat" => "livraison"
                ,"adresse_mac" => "FGHRGHFDFDG4"
                ,"date_fin_immo" => "2012-12-12"
                ,"date_achat" => date("Y-m-d")
                ,"quantite"=>1
                ,"grouper"=>"non"
         );
         //erreur 1: quantité absente
         try {
            $result = $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(906,$erreur,"ERREUR NON ATTRAPPEE 1");
        $new_stock["quantite"] = "1";
        //erreur 2: serial vide
        try {
            $result = $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(906,$erreur,"ERREUR NON ATTRAPPEE 2");
        $new_stock["serial"] = "1FDGF140DFG";
        //erreur 3: grouper
        try {
            $result = $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(906,$erreur,"ERREUR NON ATTRAPPEE 3");
        $new_stock["grouper"] = "oui";
        try {
            $result = $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
        }catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(908,$erreur,"ERREUR NON ATTRAPPEE 4");  
    }
    
    
    //* @test Test insertion_stock quantité inferieure à 1
    public function test_insert_stock_negatif(){
        //nouveau stock
        $cadre_refreshed=array();
        $redirection_custom=true;
        $new_stock = array(
                "ref" => "efrgte"
                ,"libelle" => "Imprimante TMS"
                ,"prix" => "230"
                ,"etat" => "livraison"
                ,"adresse_mac" => "FGHRGHFDFDG4"
                ,"date_fin_immo" => "2012-12-12"
                ,"date_achat" => date("Y-m-d")
         );
         //erreur 1: quantité negatif 
         try {
            $result = $this->obj->insert_stock($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(907,$erreur,"ERREUR NON ATTRAPPEE 1");
        $new_stock["quantite"] = "-10";
        
        try {
            $result = $this->obj->insert_stock($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->requete_valide("stock");
    }
    
    
    //* @test Test insertion_stock massive
    public function test_insert_stock_massive(){
        //nouveau stock
         $redirection_custom=true;
        $new_stock = array(
                "ref" => "efrgte"
                ,"libelle" => "Imprimante TMS"
                ,"prix" => "230"
                ,"etat" => "livraison"
                ,"adresse_mac" => "FGHRGHFDFDG4"
                ,"date_fin_immo" => "2012-12-12"
                ,"date_achat" => date("Y-m-d")
         );
         //erreur 1: quantité absente
         try {
            $result = $this->obj->insert_stock($new_stock,$this->s);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(907,$erreur,"ERREUR NON ATTRAPPEE 1");
        $new_stock["quantite"] = "42";
        $new_stock["serial"] = "SDFSDF147HGH";
        unset($new_stock["adresse_mac"]);
        //erreur 2: serial renseigner
        try {
            $result = $this->obj->insert_stock($new_stock,$this->s);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(908,$erreur,"ERREUR NON ATTRAPPEE 2");
        unset($new_stock["serial"]);
        //erruer 3: grouper
        try {
            $result = $this->obj->insert_stock($new_stock,$this->s);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(908,$erreur,"ERREUR NON ATTRAPPEE 3");
        $new_stock["grouper"] = "non";
        try {
            $result = $this->obj->insert_stock($new_stock,$this->s);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(906,$erreur,"ERREUR NON ATTRAPPEE 4"); 
    }
    
    
    //* @test Test insertion_stock 
    public function test_insert_stock_un(){
        //nouveau stock
         $cadre_refreshed=array();
         $redirection_custom=true;
        $new_stock = array(
                "ref" => "efrgte"
                ,"libelle" => "Imprimante TMS"
                ,"prix" => "230"
                ,"etat" => "stock"
                ,"adresse_mac" => "FGHRGHFDFDG3"
                ,"date_fin_immo" => "2012-12-12"
                ,"date_achat" => date("Y-m-d")
         );
         //erreur 1: quantité absente
         try {
            $result = $this->obj->insert_stock($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(907,$erreur,"ERREUR NON ATTRAPPEE 1");
        $new_stock["quantite"] = "1";
        $new_stock["adresse_mac"] = "FGHRGHFDFDG1";
        
        try {
            $result = $this->obj->insert_stock($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(907,$erreur,"ERREUR NON ATTRAPPEE 2");
        $new_stock["grouper"] = "non";
        $new_stock["adresse_mac"] = "FGHFGHFDFDG3";
        //erreur 1: serial
         try {
            $result = $this->obj->insert_stock($new_stock,$this->s,NULL,$cadre_refreshed);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(906,$erreur,"ERREUR NON ATTRAPPEE 3");
        $new_stock["serial"] = "ZEERZRZ";
        $new_stock["adresse_mac"] = "FGHR1HFDFDG3";
        $result = $this->obj->insert_stock($new_stock,$this->s,NULL,$cadre_refreshed);
    }
    
    
    //* @test Test de update stock. 
    public function test_update_affaire(){
        //les lignes de stock existantes
        $data = $this->obj->select_all();
        //test nombre de stock
        $this->assertEquals(15,count($data),"stock update assert 1");        
        //test premier stock    
        $stock_1 = $data[4];
        //le serial n'existe pas 
        $this->assertTrue(empty($stock_1["serial"]),"assert serial n'existe pas");
        //nouveau serial
        $cadre_refreshed =array();
        $inf = array("serial"=>"EZFGTRB1245T","id_stock"=>$stock_1["stock.id_stock"],"etat"=>"stock");
        $stock_1_up = $this->obj->update($inf,$this->s,NULL,$cadre_refreshed);
        $this->requete_valide("stock"); 
        //test serial
        $serial = $this->obj->select($stock_1["stock.id_stock"],"serial");
        $this->assertEquals("EZFGTRB1245T",$serial,"le serial n'est pas mis à jours");
    }

    //* @test Test de update stock. 
    public function test_update_select_all(){
        //les lignes de stock existantes
        $data = $this->obj->select_all();
        //test nombre de stock
        $this->assertEquals(15,count($data),"stock update assert 1");        
        //test premier stock    
        $stock_1 = $data[4];
        //le serial n'existe pas 
        $this->assertTrue(empty($stock_1["serial"]),"assert serial n'existe pas");
        //nouveau serial
        $cadre_refreshed =array();
        $inf = array("serial"=>"EZFGTRB1245T","id_stock"=>$stock_1["stock.id_stock"]);
        $stock_1_up = $this->obj->update($inf,$this->s,NULL,$cadre_refreshed);
        $this->requete_valide("stock"); 
        //test serial
        $serial = $this->obj->select($stock_1["stock.id_stock"],"serial");
        $this->assertEquals("EZFGTRB1245T",$serial,"le serial n'est pas mis à jours");
    }
    
    //* @test Test de update stock. 
    public function test_update_select_all_formulaire(){
        //les lignes de stock existantes
        $data = $this->obj->select_all();
        //test nombre de stock
        $this->assertEquals(15,count($data),"stock update assert 1");        
        //test premier stock    
        $stock_1 = $data[4];
        //le serial n'existe pas 
        $this->assertTrue(empty($stock_1["serial"]),"assert serial n'existe pas");
        //nouveau serial
        $cadre_refreshed =array();
        $inf = array("formulaire"=>true,"serial"=>"EZFGTRB1245T","id_stock"=>$stock_1["stock.id_stock"]);
        try{
            $stock_1_up = $this->obj->update($inf,$this->s,NULL,$cadre_refreshed);
        }catch(error $e) {
            ATF::db()->rollback_transaction();
            $erreur = $e->getCode();
        }
        $this->requete_valide("stock");
    }
    
    //* @test Test de update_from stock (etat:reception). 
//  public function test_update_from_select_all(){
//      //les lignes de stock existantes
//      $data = $this->obj->select_all(); 
//      //print_r($data);
//      //test nombre de stock
//      $this->assertEquals(5,count($data),"stock update assert 1");        
//      //test premier stock    
//      $stock_1 = $data[4];
//      //le serial n'existe pas 
//      $this->assertTrue(empty($stock_1["serial"]),"assert serial n'existe pas");
//      //nouveau serial
//      $cadre_refreshed =array();
//      try{
//          $inf = array("serial"=>"EZFGTRB1245T","id_stock"=>$stock_1["stock.id_stock"]);
//          $stock_1_up = $this->obj->update_from($inf,$this->s,NULL,$cadre_refreshed);
//          $this->requete_valide("stock");
//      } catch (error $e) {
//          $erreur = $e->getCode();
//      }
//      $this->assertEquals(12,$erreur,"ERREUR NON ATTRAPPEE 1");
//  }
    
    //* @test Test de update_from stock (etat:immo).
    public function test_update_from_select_all_avec_direction(){
        //nouveau stock
        $cadre_refreshed=array();        
        $new_stock = array(
                "ref" => "DFG12FDGFH"
                ,"libelle" => "TMS"
                ,"prix" => "230"
                ,"etat" => "immo"
                ,"serial"=>"EDFERDFER"
                ,"quantite"=>1
                ,"grouper"=>"non"
         );
        $result = $this->obj->insert($new_stock,$this->s,NULL,$cadre_refreshed);
        //données du stock inserer
        $stock = $this->obj->select($result);
        $result = $this->obj->update_from(array("formulaire"=>false,"prix"=>"550","etat"=>"stock","id_stock"=>$stock["id_stock"]),$this->s);
        $this->assertEquals(1,$result,"ERREUR NON ATTRAPPEE update_from stock 1");
    }
    
    //* @test Test de toStock stock. 
    public function test_toStock(){
        //les lignes de stock existantes
        $data = $this->obj->select_all();

        //mise à jours de 2 stock   
        ATF::stock_etat()->insert(array(
            "etat"=>"stock"
            ,"id_stock"=>$data[1]["stock.id_stock"]
        ));
        ATF::stock_etat()->insert(array(
            "etat"=>"stock"
            ,"desassocier"=>"oui"
            ,"id_stock"=>$data[2]["stock.id_stock"]
        ));
        $fields=array(
            "stock.id_stock_fk"
            ,"stock.ref"
            , "stock.libelle"
            , "stock.serial"
        );
        $this->obj
            ->q
            ->reset()
            ->orWhere("stock.id_stock",$data[1]["stock.id_stock"],'hop')
            ->orWhere("stock.id_stock",$data[2]["stock.id_stock"],'hop')
            ->setView(array("order"=>$fields));
        $les_lignes_2 = $this->obj->toStock();
        //aucun stock est en etat stock
        $this->assertEquals(2,count($les_lignes_2),"stock toStock assert 3");
    }
    
    //* @test Test delete stock 
    public function test_delete(){
        //nombre de ligne dans stock
        $this->assertEquals(15,count($this->_stock),"stock delete assert 1");        
        //Sauvegarde du querier
        $q_save=clone $this->obj->q;
        $cadre_refreshed =array();
        $this->obj->delete(array("id"=>array($this->_stock[0]["id_stock"],$this->_stock[1]["id_stock"])),$this->s,NULL,$cadre_refreshed);
        $this->requete_valide("stock"); 
        //utiliser le quriere Sauvegarder
        $this->obj->q=$q_save;
        //mise à jours de l'instance _stock
        $this->_stock = $this->obj->sa();
        //nombrede lignes dans stock apres le delete de 2 stock
        $this->assertEquals(13,count($this->_stock),"stock delete assert 2");
        
        $lesEtats= ATF::stock_etat()->select($this->_stock[0]["id_stock"]);
        $this->requete_valide("stock_etat");    
        //nombre de lignes dans stock_etat apres le delete de stock[0]
        $this->assertEquals(0,count($lesEtats),"stock delete assert 3");
        
        $lesEtats= ATF::stock_etat()->select($this->_stock[1]["id_stock"]);
        $this->requete_valide("stock_etat");    
        //nombre de lignes dans stock_etat apres le delete de stock[1]
        $this->assertEquals(0,count($lesEtats),"stock delete assert 4");
    }
    
    
    //* @test Test delete stock sur le module stock
    public function test_delete_stock(){
        //nombre de ligne dans stock
        $this->assertEquals(15,count($this->_stock),"stock delete assert 1");
        //nouveau stock sans affaire        
        $new_stock = array(
                    "ref" => "efrgte"
                    ,"libelle" => "Imprimante TMSTR"
                    ,"date_achat" => "1999-12-12"
                    ,"prix" => "230"
                    ,"etat" => "livraison"
                    ,"serial" => "FRT2GHHJ45HJ"
                    ,"adresse_mac" => "FGHRGHFDFDG4"
                    ,"date_fin_immo" => "2012-12-12"
                    ,"grouper"=>"non"
             );
        //erreur 1: quantite
        try {
            $result = $this->obj->insert_stock($new_stock,$this->s);
        } catch (error $e) {
            $erreur = $e->getCode();
        }
        $this->assertEquals(907,$erreur,"ERREUR NON ATTRAPPEE 1");
        $new_stock["quantite"] = "1";
        $result = $this->obj->insert_stock($new_stock,$this->s);
        $this->obj->delete(array("id"=>array($result)),$this->s);
        $this->requete_valide("stock");
    }
    
    
    //* @test Test delete stock sur le module stock
//  public function test_annule(){
//      $cadre_refreshed = array();
//      $result = $this->obj->annule($this->_stock[0],$this->s,NULL,$cadre_refreshed);
//      $this->requete_valide("stock");
//  }
//  
//  
    //* @test Test delete stock sur le module stock
    public function test_annule_select_all(){
        $result = $this->obj->annule($this->_stock[4],$this->s);
        $this->requete_valide("stock");
    }

    //* @test Test de setSerial 
    public function test_setInfos(){
        $erreur = false;
        try {
            $this->obj->setInfos();
        } catch (errorStock $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Pas d'erreur remontée 89.");
        $this->assertEquals(900,$e->getCode(),"Mauvais code d'erreur.");
        
        $stock_1 = $this->_stock[0];
        //le serial n'existe pas
        if(!$stock_1['serial']){
            $this->obj->setInfos(array(
                "id_stock"=>$stock_1["id_stock"]
                ,"serial"=>"MOUAD006"
                ,"serialAT"=>"MOUAD005"
                ,"adresse_mac"=>"MOUAD004"
                ,"ref"=>"MOUAD003"
                ,"libelle"=>"MOUAD002"
                ,"prix"=>"69"
                ,"prix_achat"=>"666"
            ));
        }
        $this->obj->q->reset();
        $this->_stock = $this->obj->sa();
        //test du serial si il est bien inseré
        $this->assertEquals("MOUAD006",$this->_stock[0]["serial"],"stock setInfos assert 1");
        $this->assertEquals("MOUAD005",$this->_stock[0]["serialAT"],"stock setInfos assert 2");
        $this->assertEquals("MOUAD004",$this->_stock[0]["adresse_mac"],"stock setInfos assert 3");
        $this->assertEquals("MOUAD003",$this->_stock[0]["ref"],"stock setInfos assert 4");
        $this->assertEquals("MOUAD002",$this->_stock[0]["libelle"],"stock setInfos assert 5");
        $this->assertEquals("69",$this->_stock[0]["prix"],"stock setInfos assert prix");
        $this->assertEquals("666",$this->_stock[0]["prix_achat"],"stock setInfos assert prix_achat");
        
        
        ATF::$usr->set('id_profil',11);
        $erreur = false;
        try {
            $this->obj->setInfos(array(
                "id_stock"=>$stock_1["id_stock"]
                ,"prix"=>"69"
            ));
        } catch (errorStock $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Pas d'erreur remontée modif prix.");
        $this->assertEquals(904,$e->getCode(),"Mauvais code d'erreur.");
        
        $erreur = false;
        try {
            $this->obj->setInfos(array(
                "id_stock"=>$stock_1["id_stock"]
                ,"prix_achat"=>"666"
            ));
        } catch (errorStock $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Pas d'erreur remontée modif prix_achat.");
        $this->assertEquals(905,$e->getCode(),"Mauvais code d'erreur.");
    }   
    
    // @author Yann GAUTEHRON <ygautheron@absystech.fr>
    public function test_getRefEnStock(){
        $a = array("ref"=>"REF DE TEST DE REF","libelle"=>"testor","quantite"=>1);
        $this->obj->insert($a);
        $id_stock = $this->obj->getRefEnStock($a["ref"]);

        //aucun stock est en etat stock
        $this->assertGreaterThan(1,$id_stock,"Stock non retrouvé");
    }
    
    // @author Quentin JANON <qjanon@absystech.fr>
    public function test_autocomplete(){
        ATF::stock()->i(array("libelle"=>"stock test TU","serial"=>"XCVBNMLKJHGDFSQ","serialAT"=>"ATXCVBNMLKJHGDFSQ"));
        
        $infos = array("limit"=>5,"query"=>"X");
        $r = $this->obj->autocomplete($infos);
        $this->assertEquals(1, count($r),"Erreur de nombre de retour");
        $this->assertEquals("XCVBNMLKJHGDFSQ", $r[0]['serial'],"Erreur de serial de retour");
        
    }
   

        // @author Quentin JANON <qjanon@absystech.fr>
    public function test_updateForMagentoError902() {
        
        $params = array("id_stock"=>31468435121453322165126);
        $this->obj->updateForMagento();
        
        $erreur = false;
        try {
            $this->obj->updateForMagento($params);
        } catch (error $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Pas de remonté d'erreur 902");
        $this->assertEquals(902, $e->getCode(), "Le code d'erreur n'est pas le bon : 902");
        
    }
    
    // @author Quentin JANON <qjanon@absystech.fr>
    public function test_updateForMagentoError904() {

        $infos = array("libelle" => "TU", "ref" => "BONANA", 'quantite' => 5);
        $id = $this->obj->insert($infos);
        
        $params = array("id_stock"=>$id);
        $p = $this->obj->select($id);
        $this->assertFalse($this->obj->updateForMagento(),"Erreur : pas de stock, donc return FALSE");
        
        $erreur = false;
        try {
            $this->obj->updateForMagento($params);
        } catch (error $e) {
            $erreur = true;
        }
        $this->assertTrue($erreur,"Pas de remonté d'erreur 904");
        $this->assertEquals(904, $e->getCode(), "Le code d'erreur n'est pas le bon : 904");
        
    }
        
    // @author Quentin JANON <qjanon@absystech.fr>
    public function test_updateForMagentoSend() {

        $infos = array("libelle" => "HP TU", "ref" => "BONANA", 'quantite' => 2, "prix"=>115);
        $this->obj->insert($infos);
        
        // Séléction de l'un des stock insérer 
        $this->obj->q->reset()->where("ref","BONANA");
        $r = $this->obj->sa();

        $p1ID = $r[0]["id_stock"];
        $p2ID = $r[1]["id_stock"];
        
        $p1 = array(
            "id_stock"=>$p1ID,
            "description"=>"Description de TU",
            "short_description"=>"Short_description de TU"
        );
        $this->obj->u($p1);
        
        $params = array("id_stock"=>$p1ID,"action"=>"send","icecatParsing"=>false,"prix"=>189,"categories_magento"=>"divers");
        
        $this->obj->updateForMagento($params);
        
        $p1After = $this->obj->select($p1ID);
        $p2After = $this->obj->select($p2ID);
        
        $this->assertEquals("oui",$p1After["to_magento"],"Le premier produit n'a pas le flag to_magento a oui");
        $this->assertEquals(189,$p1After["prix"],"Le premier produit de même ref n'as pas vu son prix mise a jour automatiquement");
        $this->assertEquals("divers",$p1After["categories_magento"],"Le premier produit de même ref n'as pas vu son prix mise a jour automatiquement");

        $this->assertEquals("oui",$p2After["to_magento"],"Le deuxième produit n'a pas le flag to_magento a oui");
        $this->assertEquals("Description de TU",$p2After["description"],"Le deuxième produit de même ref n'as pas vu sa description mise a jour automatiquement");
        $this->assertEquals("Short_description de TU",$p2After["short_description"],"Le deuxième produit de même ref n'as pas vu sa short_description mise a jour automatiquement");
        $this->assertEquals("HP",$p2After["marque"],"Le deuxième produit de même ref n'as pas vu sa marque mise a jour automatiquement");
        $this->assertEquals(189,$p2After["prix"],"Le deuxième produit de même ref n'as pas vu son prix mise a jour automatiquement");
        $this->assertEquals("divers",$p2After["categories_magento"],"Le deuxième produit de même ref n'as pas vu son prix mise a jour automatiquement");

    }    // @author Quentin JANON <qjanon@absystech.fr>

   
}
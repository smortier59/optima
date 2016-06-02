<?
/**
* @author Quentin JANON <qjanon@absystech.fr>
* @date 11-08-2010
*/ 
class pdf_absystech_test extends ATF_PHPUnit_Framework_TestCase {

	/**
	* Chemin où est stocké le fichier temporaire pour les tests
	* @access private
	* @var string
	*/
	private $dirSavedPDF = "/home/www/SauvegardePDFTU/";
	private $dateSave = "";
	private $tmpFile = "/tmp/TUPDFTEMPORAIRE_PDF_AT_CLASS.pdf";
	/**
	* Commande Ghost Script permettant la conversion du PDF en image
	* @access private
	* @var string
	*/
	private $GScmd = "";
	/**
	* Commande SHELL pour avoir la résultante d'un fichier en md5
	* @access private
	* @var string
	*/
	private $MD5cmd = "";
	
	/* Ne pas toucher cette fonction ! */
	public function __construct() {
		parent::__construct(); 
		
		$this->GScmd = "gs -dQUIET -dNOPAUSE -dBATCH -sDEVICE=jpeg -sOutputFile=".str_replace(".pdf",".jpg",$this->tmpFile)." ".$this->tmpFile." 2>&1";
		$this->MD5cmd = "md5sum ".str_replace(".pdf",".jpg",$this->tmpFile);
		$this->dateSave = date('Ymd');
	}
	
	/** 
	* SetUp : créer un user/societe/droit, une facture, un devis, une affaire et un ODM !
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-08-2010
	*/
	public function setUp(){
		$this->initUser();
		
		//Suppression des fichiers qui sont daté de plus de 7 jours
		$list = scandir($this->dirSavedPDF);
		$dateRef = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
		foreach ($list as $k=>$i) {
			if ($i=="." || $i=="..") continue;
			$a = preg_match("/[0-9]{8}/",$i,$m);
			$dateFile = strtotime($m[0]);
			if ($dateRef>$dateFile) {
				ob_start();
				system("rm ".$this->dirSavedPDF.$i." 2>&1");
				ob_end_clean();
			}
		}
		
		$this->dirSavedPDF .= "PDF_AT-";
		
		$this->assertFalse(file_exists($this->tmpFile),"Erreur : Les fichiers temporaires sont déjà présents :/");
	
		

		$s = array();
		
		//Contact
		$x = array(
			"id_societe"=>ATF::$usr->get("id_societe")
			,"nom"=>"NOMTU"
			,"prenom"=>"PRENOMTU"
		);
		$this->id_contact = ATF::contact()->insert($x);
		//Affaire
		$this->id_affaire = ATF::affaire()->insert(array(
			"id_societe"=>ATF::$usr->get("id_societe")
			,"date"=>"2050-08-01"
			,"affaire"=>"Affaire pour les TU"
		));
		//Devis
		for ($a=0; $a<3; $a++) {
			$this->ligneDevis['devis_ligne'][$a] = array(
				"devis_ligne.ref"=>"REF".$a
				,"devis_ligne.produit"=>"Produit TU n°".$a
				,"devis_ligne.quantite"=>2*$a+1
				,"devis_ligne.prix"=>10*$a+1
			);
		}
				
		$this->id_devis = ATF::devis()->insert(array(
			"devis"=>array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>ATF::$usr->get("id_societe")
				,"resume"=>"Devis pour les TU"
				,"tva"=>"1.2"
				,"date"=>"2050-08-01 09:00:00"
				,"validite"=>"2050-08-01"
				,"id_contact"=>$this->id_contact
			),
			"values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
			),ATF::_s()
		);
		
		//Facture
		$this->totalFacture = 0;
		for ($a=0; $a<3; $a++) {
            $this->ligneCommande['commande_ligne'][$a] = array(
                "commande_ligne.ref"=>"REF".$a
                ,"commande_ligne.produit"=>"Produit TU n°".$a
                ,"commande_ligne.quantite"=>2*$a+1
                ,"commande_ligne.prix"=>10*$a+1
            );
            $this->ligneFacture['facture_ligne'][$a] = array(
                "facture_ligne.ref"=>"REF".$a
                ,"facture_ligne.produit"=>"Produit TU n°".$a
                ,"facture_ligne.quantite"=>2*$a+1
                ,"facture_ligne.prix"=>10*$a+1
            );
			$this->totalFacture += (2*$a+1)*(10*$a+1);
		}
		$this->id_facture = ATF::facture()->insert(array(
			"facture"=>array(
				"id_affaire"=>$this->id_affaire
				,"id_societe"=>ATF::$usr->get("id_societe")
				,"tva"=>"1.2"
				,"frais_de_port"=>"30"
				,"date"=>"2050-08-01 09:00:00"
				,"prix"=>$this->totalFacture
				,"infosSup"=>"Informations supplémentaire !"
			),
			"values_facture"=>array("produits"=>json_encode($this->ligneFacture['facture_ligne']))
			),ATF::_s()
		);
		ATF::hotline()->insertWithId = true;
		$this->id_hotline = ATF::hotline()->i(
			array(
				"id_hotline"=>2
				,"id_societe"=>ATF::$usr->get("id_societe")
				,"hotline"=>"HOTLINE TU 1"
				,"id_contact"=>$this->id_contact
				,"date"=>"2050-08-01"
				,"id_user"=>ATF::$usr->getID()
			)
		);
		

		
		//SUppression du fichier devis généré automatiquement et stocké
		util::rm(__ABSOLUTE_PATH__."../data/absystech/devis/".$this->id_devis.".fichier_joint");
	}

	/** 
	* Méthode qui supprime les fichiers générés a la fin du TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-08-2010
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		ob_start();
		system("rm ".str_replace(".pdf","",$this->tmpFile).".* 2>&1");
		ob_end_clean();
	}
	   

    /* @author Quentin JANON <qjanon@absystech.fr> 
    */  
    public function test_code_barreATT() {
        $this->obj->generic("code_barreATT","2050-08-01",$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."code_barreATT-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a402b6a9418cafb62cf167b44997b872",$md5,"Erreur de génération de code barre ATT");
    }
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */  
    public function test_code_barre() {
        $this->obj->generic("code_barre","2050-08-01",$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."codeBarre-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2ff77d3d05cee89d08668789536fbc6c",$md5,"Erreur de génération de code barre");
    }
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */  
    public function test_code_barreSerial() {
        $param = array(
            "serial"=>array("IYUGHBKH","FHCTESRJ","JYGJTFHTCF")
        );
        $this->obj->generic("code_barre",$param,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."codeBarreSerial-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("8e62dcf39c071e0ede8347835318bc2b",$md5,"Erreur de génération de code barre avec Serial");
    }
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_etiquette_logo() {
        $this->obj->generic("etiquette_logo",array(),$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."etiquetteLogo-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("73eafc252ef4795a57ccd7b2569d0609",$md5,"Erreur de génération des étiquettes");
    }
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_factureInteret1() {
        //Montant
        ATF::facture()->u(array("id_facture"=>$this->id_facture,"prix"=>1000,"date_previsionnelle"=>"2050-01-01"));

        $this->id_facture_paiement = ATF::facture_paiement()->insert(array(
            "facture_paiement"=>array(
                "id_facture"=>$this->id_facture
                ,"montant"=>500
                ,"date"=>"2054-08-01 09:00:00"
            ))
        );

        $this->obj->generic("factureInteret",$this->id_facture_paiement,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."factureInteret1-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("7a6b4d80a99c991739b9b514418b1ccb",$md5,"Erreur de génération de la facture d'intérêt n°1");
    }   
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_factureInteret2() {
        ATF::facture()->u(array("id_facture"=>$this->id_facture,"prix"=>1000,"date_previsionnelle"=>"2050-01-01"));

        $this->id_facture_paiement = ATF::facture_paiement()->insert(array(
            "facture_paiement"=>array(
                "id_facture"=>$this->id_facture
                ,"montant"=>500
                ,"date"=>"2054-08-01 09:00:00"
            ))
        );

        //Montant
        //Contact de facturation
        // Adresse de facturation
        ATF::societe()->u(array(
            "id_societe"=>ATF::$usr->get("id_societe")
            ,"id_contact_facturation"=>$this->id_contact
            ,"facturation_adresse"=>"Adresse de Facturation"
            ,"facturation_adresse_2"=>"Adresse de Facturation 2"
            ,"facturation_adresse_3"=>"Adresse de Facturation 3"
            ,"facturation_cp"=>"CP123"
            ,"facturation_ville"=>"Ville de Facturation"
            ,"facturation_id_pays"=>"FR"
        ));
        // Date effective
        ATF::facture()->u(array(
            "id_facture"=>$this->id_facture
            ,"date_effective"=>"2011-01-01"
        ));
        
        $this->obj->generic("factureInteret",$this->id_facture_paiement,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."factureInteret2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ac6c2b68646be9af99ad10f71e165836",$md5,"Erreur de génération de la facture d'intérêt n°2");
    }   
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_factureInteret3() {
        ATF::facture()->u(array("id_facture"=>$this->id_facture,"prix"=>1000,"date_previsionnelle"=>"2050-01-01"));

        $this->id_facture_paiement = ATF::facture_paiement()->insert(array(
            "facture_paiement"=>array(
                "id_facture"=>$this->id_facture
                ,"montant"=>500
                ,"date"=>"2054-08-01 09:00:00"
            ))
        );

        //Montant
        // Adresse 2 et 3
        ATF::societe()->u(array(
            "id_societe"=>ATF::$usr->get("id_societe")
            ,"adresse_2"=>"Adresse 2"
            ,"adresse_3"=>"Adresse 3"
        ));
        // Code commande client
        ATF::affaire()->u(array(
            "id_affaire"=>$this->id_affaire,
            "code_commande_client"=>"CODETU"
        ));

                    
        $this->obj->generic("factureInteret",$this->id_facture_paiement,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."factureInteret3-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("dd475258b6e37e2febe9d2ac46f39c80",$md5,"Erreur de génération de la facture d'intérêt n°3");
    }   
    
    /* @author Quentin JANON <qjanon@absystech.fr> */
    public function test_livraison() {
        
        $livraison = array(
            "ref"=>"REF"
            ,"livraison"=>"LIVRAISON"
            ,"date"=>"2050-01-01 00:00:00"
            ,"id_societe"=>ATF::$usr->get('id_societe')
        );
        $id = ATF::livraison()->i($livraison);
        ATF::stock()->truncate();
        $stock = array(
            array("libelle"=>"LIB1","serial"=>"SERIAL1")
            ,array("libelle"=>"LIB2","serial"=>"SERIAL2")
            ,array("libelle"=>"LIB3","serial"=>"SERIAL3")
            ,array("libelle"=>"LIB4","serial"=>"SERIAL4")
            ,array("libelle"=>"LIB5","serial"=>"SERIAL5")
            ,array("libelle"=>"LIB6","serial"=>"SERIAL6")
            ,array("ref"=>"2","libelle"=>"LIB7","serial"=>"SERIAL7")
            ,array("ref"=>"2","libelle"=>"LIB8","serial"=>"SERIAL8")
        );
        
        foreach ($stock as $k=>$i) {
            $stock[$k]['id_stock'] = ATF::stock()->i($i);   
        }
        
        $lignes = array(
            array("id_livraison"=>$id,"id_stock"=>$stock[0]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[1]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[2]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[3]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[4]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[5]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[6]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[7]['id_stock'])
        );
        foreach ($lignes as $k=>$i) {
            ATF::livraison_ligne()->i($i);  
        }
        
        $this->obj->generic("livraison",$id,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."livraison-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6fb2a939290e5e8340ecf03ac49c6cba",$md5,"Erreur de génération du bon de livraison");
    }   
    
    /* @author Quentin JANON <qjanon@absystech.fr> */
    public function test_livraison_no_serial() {
        
        $livraison = array(
            "ref"=>"REF"
            ,"livraison"=>"LIVRAISON"
            ,"date"=>"2050-01-01 00:00:00"
            ,"id_societe"=>ATF::$usr->get('id_societe')
        );
        $id = ATF::livraison()->i($livraison);
        ATF::stock()->truncate();
        $stock = array(
            array("libelle"=>"LIB1")
            ,array("libelle"=>"LIB2")
            ,array("libelle"=>"LIB3")
            ,array("libelle"=>"LIB4")
            ,array("libelle"=>"LIB5")
            ,array("libelle"=>"LIB6")
            ,array("ref"=>"2","libelle"=>"LIB7")
            ,array("ref"=>"2","libelle"=>"LIB8")
        );
        
        foreach ($stock as $k=>$i) {
            $stock[$k]['id_stock'] = ATF::stock()->i($i);   
        }
        
        $lignes = array(
            array("id_livraison"=>$id,"id_stock"=>$stock[0]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[1]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[2]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[3]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[4]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[5]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[6]['id_stock'])
            ,array("id_livraison"=>$id,"id_stock"=>$stock[7]['id_stock'])
        );
        foreach ($lignes as $k=>$i) {
            ATF::livraison_ligne()->i($i);  
        }
        
        $this->obj->generic("livraison",$id,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."livraison-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("9f4aa595e279178fa8075b6a9409e19f",$md5,"Erreur de génération du bon de livraison");
    }   
    
    /** 
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_relancePremiere() {
        ATF::_r('date',"30/05/2011");        
        
        ATF::facture()->q->reset()->addOrder('interet','desc')->setDimension('row')->setLimit(1);
        $f = ATF::facture()->select_all();
        
        
        $this->id_relance = ATF::relance()->insert(
            array(
                "id_facture"=>$f['facture.id_facture']
            )
        );

        $this->obj->interet = 30000;
        $this->obj->generic("relance",$f['facture.id_facture'],$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."relance1-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a7886cc70a4759318cb84d5f25575761",$md5,"Erreur de génération de la 1ere relance, probablement parce que la société avec le plus d'intérêt n'est plus Ergos Nord (30 000€).");
        
    }
    
    /** 
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_relanceDeuxieme() {
        ATF::_r('date',"30/05/2011");
        $this->id_relance = ATF::relance()->insert(
            array(
                "id_facture"=>$this->id_facture
                ,"date_1"=>"2050-08-01"
            )
        );
        
        $s = array("id_societe"=>$this->id_societe,"adresse_2"=>"adresse 2");
        ATF::societe()->u($s);

        $this->obj->interet = 2900;

        $this->obj->generic("relance",$this->id_facture,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."relance2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("3b38e7192374c6b856802a40f2ccdb3f",$md5,"Erreur de génération de la 2eme relance");
        
    }
    
    /** 
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_relanceTroisieme() {
        ATF::_r('date',"30/05/2011");
        $this->id_relance = ATF::relance()->insert(
            array(
                "id_facture"=>$this->id_facture
                ,"date_1"=>"2050-08-01"
                ,"date_2"=>"2050-08-01"
            )
        );
        
        $s = array("id_societe"=>$this->id_societe,"adresse_3"=>"adresse 3");
        ATF::societe()->u($s);

        $this->obj->interet = 3900;

        $this->obj->generic("relance",$this->id_facture,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."relance3-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("fbaff7f6b7633d5c5dc95c3ec54ccac1",$md5,"Erreur de génération de la 3eme relance");
        
    }

    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_facture_exoneration_tva() {
        $this->obj->open();
        $this->obj->addpage();
        $this->obj->facture_exoneration_tva(ATF::facture()->select($this->id_facture),$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."ExonerationTVAFacture-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("e7bfabe392e9e0c06517673f4fe2a8c2",$md5,"Erreur de génération de l'ExonerationTVAFacture");
    } 

    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_facture_exoneration_tvaAVOIR() {
        ATF::facture()->u(array("id_facture"=>$this->id_facture,"type_facture"=>"avoir"));
        
        $this->obj->open();
        $this->obj->addpage();
        $this->obj->facture_exoneration_tva(ATF::facture()->select($this->id_facture),$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."ExonerationTVAFactureAvoir-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("975bed1c4f049737ba97a1712e822b24",$md5,"Erreur de génération de l'ExonerationTVAFactureAvoir");
    } 
    
    /** 
    * @author Quentin JANON
    * @date 2012-04-12
    */
    public function test_bon_de_pret(){
        $this->insertSociete();
        $this->insertContact();
        $stock = array(
            array("libelle"=>"Stock 1","serial"=>"JGTFCJHB64853","serialAT"=>"klugLYG68546s5fgd6","ref"=>"REF1")
            ,array("libelle"=>"Stock 2","serial"=>"YUTIOHFD654","serialAT"=>"HFvjbvsjgVHgvj6545","ref"=>"REF2")
            ,array("libelle"=>"Stock 3","serial"=>"GFgjlvHFK54","serialAT"=>"gfkihGKGHV654JGFHG","ref"=>"REF3")
            ,array("libelle"=>"Stock 4","serial"=>"HFCDGJH24HV","serialAT"=>"jhghfdxSFX1354azdsH","ref"=>"REF4")
        );
        foreach ($stock as $k=>$i) {
            $stock[$k]['id_stock'] = ATF::stock()->i($i);   
        }
        
        $bp = array(
            "values_bon_de_pret"=>array(
                "produits"=>json_encode(array(
                    array("bon_de_pret_ligne__dot__id_stock"=>$stock[0]['id_stock'],"bon_de_pret_ligne__dot__ref"=>$stock[0]['ref'],"bon_de_pret_ligne__dot__serial"=>$stock[0]['serial'],"bon_de_pret_ligne__dot__serialAT"=>$stock[0]['serialAT'],"bon_de_pret_ligne__dot__stock"=>$stock[0]['libelle'])
                    ,array("bon_de_pret_ligne__dot__id_stock"=>$stock[1]['id_stock'],"bon_de_pret_ligne__dot__ref"=>$stock[1]['ref'],"bon_de_pret_ligne__dot__serial"=>$stock[1]['serial'],"bon_de_pret_ligne__dot__serialAT"=>$stock[1]['serialAT'],"bon_de_pret_ligne__dot__stock"=>$stock[1]['libelle'])
                    ,array("bon_de_pret_ligne__dot__id_stock"=>$stock[2]['id_stock'],"bon_de_pret_ligne__dot__ref"=>$stock[2]['ref'],"bon_de_pret_ligne__dot__serial"=>$stock[2]['serial'],"bon_de_pret_ligne__dot__serialAT"=>$stock[2]['serialAT'],"bon_de_pret_ligne__dot__stock"=>$stock[2]['libelle'])
                ))
            )
            ,"bon_de_pret"=>array(
                "bon_de_pret"=>"BP TU 1"
                ,"id_societe"=>$this->id_societe
                ,"id_contact"=>$this->id_contact
                ,"date_debut"=>"2012-01-01"
                ,"date_fin"=>"2012-02-01"
                ,"id_user"=>ATF::$usr->get('id_user')
                ,"filestoattach"=>array("pdf"=>"true")
            )
        );
        $r = ATF::bon_de_pret()->insert($bp);
        
        $this->obj->bon_de_pret(ATF::bon_de_pret()->decryptId($r),$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."bon_de_pret-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("953e4c243eba868c7fe77b9b1227d736",$md5,"Erreur de génération de l'bon_de_pret");
    }   
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function gen_facture_periodique($periodicite, $date, $date_fin = NULL) {
        //On modifie la société pour passer le complément d'infos
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"id_contact_facturation"=>$this->id_contact
                ,"facturation_adresse"=>"facturation_adresse"
                ,"facturation_adresse_2"=>"facturation_adresse_2"
                ,"facturation_adresse_3"=>"facturation_adresse_3"
                ,"facturation_cp"=>"59000"
                ,"facturation_ville"=>"facturation_ville"
                ,"facturation_id_pays"=>"FR"
            )
        );
        $facture = array(
                "facture"=>array(
                    "id_affaire"=>$this->id_affaire
                    ,"id_societe"=>ATF::$usr->get("id_societe")
                    ,"tva"=>"1.2"
                    ,"id_user"=>ATF::$usr->getID()
                    ,"date_debut_periode" => $date
                    ,"periodicite" => $periodicite
                    ,"date"=>"2050-08-01 09:00:00"
                    ,"ref"=>ATF::affaire()->getRef("2050-08-01","facture")
                    ,"date_previsionnelle"=>"2050-08-31 09:00:00"
                    ,"prix"=>$this->totalFacture
                    ,"acompte_pourcent"=>30
                ),
                "values_facture"=>array("produits"=>json_encode($this->ligneFacture['facture_ligne']))
                ,ATF::_s()
            );
        if($date_fin){
            $facture["facture"]["date_fin_periode"] = $date_fin;
        }


        $id_facture = ATF::facture()->insert($facture);

        
       return $id_facture;
    }
    
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function test_facture_Mensuelle(){
            
        $id_facture = $this->gen_facture_periodique("mensuelle", "05-01-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-mensuelle-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("555467f576a010a3f64cbd8a7b0e0f9c",$md5,"Erreur de génération d'une facture Mensuelle");
        
    }

    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 17-03-2014
    */
    public function test_facture_Mensuelle1(){
            
        $id_facture = $this->gen_facture_periodique("mensuelle", "05-01-2013", "11-01-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-mensuelle-PRORATA-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("c2e591dba0cb7d6bb5a9f0d5d4277723",$md5,"Erreur de génération d'une facture Mensuelle");
        
    }
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function test_facture_Annuelle(){
            
        $id_facture = $this->gen_facture_periodique("annuelle", "05-01-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-annuelle-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("675eb93b106e29f2a7b7f76ab11b5142",$md5,"Erreur de génération d'une facture Annuelle");
        
    }
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function test_facture_Trimestrielle1(){
            
        $id_facture = $this->gen_facture_periodique("trimestrielle", "05-01-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-Trimestrielle1-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("4f4046f593cd2c5a3d66bde68d53eb59",$md5,"Erreur de génération d'une facture trimestrielle 1");
        
    }
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function test_facture_Trimestrielle2(){
            
        $id_facture = $this->gen_facture_periodique("trimestrielle", "05-05-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-Trimestrielle2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("63d830d250e129ccf3a0a064258f1cfe",$md5,"Erreur de génération d'une facture trimestrielle 2");
        
    }
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function test_facture_Trimestrielle3(){
            
        $id_facture = $this->gen_facture_periodique("trimestrielle", "05-07-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-Trimestrielle3-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("28989996347314ba89255a3e0a028560",$md5,"Erreur de génération d'une facture trimestrielle 3");
        
    }
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 02-01-2013
    */
    public function test_facture_Trimestrielle4(){
            
        $id_facture = $this->gen_facture_periodique("trimestrielle", "05-10-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-Trimestrielle4-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("106581201a303f0757aaf04cda0555a3",$md5,"Erreur de génération d'une facture trimestrielle 4");
        
    }
    
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 12-03-2013
    */
    public function test_facture_Semestrielle1(){
            
        $id_facture = $this->gen_facture_periodique("semestrielle", "05-01-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-Semestrielle1-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("2219f26d93e9dc5d45b72d6e33cba67b",$md5,"Erreur de génération d'une facture trimestrielle 4");
        
    }
    
    
    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 12-03-2013
    */
    public function test_facture_Semestrielle2(){
            
        $id_facture = $this->gen_facture_periodique("semestrielle", "05-10-2013");
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-periodique-Semestrielle2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("2810204c20a0f670ea5ddf1b46e9515e",$md5,"Erreur de génération d'une facture trimestrielle 4");
        
    }
    
    public function test_lettre_de_change(){
        
        ATF::societe()->u(array("id_societe" => ATF::$usr->get("id_societe") , "rib"=> "30003011100002068845277", "banque"=> "Societe generale Lille nationale", "adresse_2" => "Bat A", "adresse_3"=> "Bureau N°8" ));
        
        ATF::facture_paiement()->insert(array("id_facture"=>ATF::facture()->decryptId($this->id_facture),"montant"=>20,"date"=>"2013-08-19"));
                        
        $infos = array("tu"=> true,"id_societe" => ATF::$usr->get("id_societe"), "factures"=> array($this->id_facture), "echeance" => "19/09/2013");
        $this->obj->lettre_de_change($infos,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."lettre_de_change-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("403f8cfad7802e8bcfbad46942bf0b32",$md5,"Erreur de génération de l'lettre_de_change");
        
    }

    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_ordre_de_mission() {

        $this->id_odm = ATF::ordre_de_mission()->insert(
            array(
                "ordre_de_mission"=>"ODM TU 1"
                ,"date"=>"2050-08-01"
                ,"id_user"=>ATF::$usr->getID()
                ,'id_hotline'=>$this->id_hotline
                ,"adresse"=>"adresse"
                ,"adresse_2"=>"adresse_2"
                ,"adresse_3"=>"adresse_3"
                ,"moyen_transport"=>"Vélo"
                ,"cp"=>"59000"
                ,"ville"=>"LILLE"
            )
        );
                
        $this->obj->generic("ordre_de_mission",$this->id_odm,$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."ODM-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d5f3d9250280c6a014ca144fc4d9188f",$md5,"Erreur de génération de l'odm 1");
    } 
    
    /** 
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_footerPrevisu() {
        $this->obj->unsetFooter();
        $this->obj->previsu = true;
        $this->obj->addpage();
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."Previsu-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("86ea4b2c1f5a5c47d43443655194529e",$md5,"Erreur de génération du HeFooterader PREVISU AT ?");
        
    }
    
    /** 
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_HeaderLivraison() {
        $this->obj->isLivraison = true;
        $this->obj->addpage();
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."HeaderLivraison-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0f3f83d02f74464c45742d0c747ccbd3",$md5,"Erreur de génération du HeaderLivraison");
        
    }
    
    /** 
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_Footer() {
        $this->obj->unsetFooter();
        $this->assertFalse($this->obj->Footer(),"Erreur, le footer desactivé doit renvoyé FALSE");
        $this->obj->setFooter();
        $s = $this->obj->societe;
        unset($this->obj->societe);
        $this->assertFalse($this->obj->Footer(),"Erreur, la société non transmise doit renvoyé FALSE");
        $this->obj->societe = ATF::societe()->select($this->id_societe);

        $this->obj->addpage();
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."Footer-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("da3944a56384f2f4ebc63eef61fd701b",$md5,"Erreur de génération du Footer AT");
        
    }
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 11-08-2010
    */
    public function test_cgv() {
        ATF::$usr->set("id_societe",1);
        $this->obj->generic("cgv",array(),$this->tmpFile);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."cgv-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("9717d6df6e490a15f4c114f2fc51e4d4",$md5,"Erreur de génération des CGV AT, auraient-elles été modifié ?");
    
    } 
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 18-03-2011
    */
    public function test_facture_classique() {
        $at = array("id_societe"=>1,"swift"=>"SWIFT !");
        ATF::societe()->u($at);
        $f = new factureTestfacture_exoneration_tva();
        $f->generic("facture",$this->id_facture,$this->tmpFile,$s);
        $f->Close();
        $f->Output($this->dirSavedPDF."facture-classique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("43a05ca286486c1380931afc5373fd13",$md5,"Erreur de génération d'une facture classique");
    }
    
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 31-08-2011
    */
    public function test_facture_accompte() {
        $id_cmd = ATF::commande()->insert(array(
            "commande"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"tva"=>"1.2"
                ,"id_user"=>ATF::$usr->getID()
                ,"date"=>"2050-08-01 09:00:00"
                ,"prix"=>$this->totalFacture
            ),
            "values_commande"=>array("produits"=>json_encode($this->ligneCommande['commande_ligne']))
            ),ATF::_s()
        );
        
        $id_facture = ATF::facture()->insert(array(
            "facture"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"tva"=>"1.2"
                ,"id_user"=>ATF::$usr->getID()
                ,"mode"=>"facture"
                ,"date"=>"2050-08-01 09:00:00"
                ,"date_previsionnelle"=>"2050-08-31 09:00:00"
                ,"prix"=>$this->totalFacture
                ,"acompte_pourcent"=>30
            ),
            "values_facture"=>array("produits"=>json_encode($this->ligneFacture['facture_ligne']))
            ),ATF::_s()
        );
        
        ATF::commande_facture()->i(array("id_commande"=>$id_cmd,"id_facture"=>$id_facture));
        
        $s = array();
        $this->obj->Open();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-accompte-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("ff611dbf3d585bc26a4349682d02239b",$md5,"Erreur de génération d'une facture accompte");
    }    
    
    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 31-08-2011
    */
    public function test_facture_exonerationTVA() {
        //On modifie la société pour passer le complément d'infos
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"id_contact_facturation"=>$this->id_contact
                ,"facturation_adresse"=>"facturation_adresse"
                ,"facturation_adresse_2"=>"facturation_adresse_2"
                ,"facturation_adresse_3"=>"facturation_adresse_3"
                ,"facturation_cp"=>"59000"
                ,"facturation_ville"=>"facturation_ville"
                ,"facturation_id_pays"=>"FR"
            )
        );
        $id_facture = ATF::facture()->insert(array(
            "facture"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"tva"=>"1"
                ,"id_user"=>ATF::$usr->getID()
                ,"date"=>"2050-08-01 09:00:00"
                ,"ref"=>ATF::affaire()->getRef("2050-08-01","facture")
                ,"date_previsionnelle"=>"2050-08-31 09:00:00"
                ,"prix"=>$this->totalFacture
                ,"acompte_pourcent"=>30
            ),
            "values_facture"=>array("produits"=>json_encode($this->ligneFacture['facture_ligne']))
            ),ATF::_s()
        );
        
        $s = array();
        $f = new factureTestfacture_exoneration_tva();
        $f->Open();
        $f->generic("facture",$id_facture,$this->tmpFile,$s);
        $f->Close();
        $f->Output($this->dirSavedPDF."facture-exonerationTVA-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean(); 
        $this->assertEquals("772597f6a3837b090066a141a59c1ffe",$md5,"Erreur de génération d'une facture exonerationTVA");
    }
    
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @date 31-08-2011
    */  
    public function test_facture_avoir() {
        ATF::societe()->u(array("id_societe"=>ATF::$usr->get("id_societe"),"swift"=>"CODESWIFT"));
        $id_facture = ATF::facture()->insert(array(
            "facture"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"tva"=>"1.2"
                ,"id_user"=>ATF::$usr->getID()
                ,"mode"=>"avoir"
                ,"date"=>"2050-08-01 09:00:00"
                ,"date_previsionnelle"=>"2050-08-31 09:00:00"
                ,"prix"=>$this->totalFacture
                ,"id_facture_parente" => 1
            ),
            "values_facture"=>array("produits"=>json_encode($this->ligneFacture['facture_ligne']))
            ),ATF::_s()
        );
        
        $s = array();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-avoir-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("21ccd29f3350eee82e6ba3a75f038941",$md5,"Erreur de génération d'une facture avoir");
        
    }
    
    
    /**
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 31-08-2011
    */
    public function test_facture_solde() {
        
        ATF::affaire()->u(array(
                                "id_affaire"=>$this->id_affaire,
                                "code_commande_client"=>"CODETU")
                            );

        ATF::facture()->d($id_facture);


        ATF::user()->update(array("id_user"=>ATF::$usr->getID(),"id_societe"=>ATF::$usr->get("id_societe")));
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
            )
        );
        $cmd = ATF::commande()->insert(array("commande"=>
            array(
                "id_affaire"=>$this->id_affaire
                ,"id_devis"=>$this->id_devis
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"tva"=>"1.2"
                ,"prix"=>139
                ,"date"=>"2050-08-01 09:00:00"
            ),
            "values_commande"=>array("produits"=>json_encode($this->ligneCommande['commande_ligne']))
        ));
        $id_facture = ATF::facture()->i(array(
            "id_affaire"=>$this->id_affaire
            ,"id_societe"=>ATF::$usr->get("id_societe")
            ,"tva"=>"1.2"
            ,"prix"=>100
            ,"id_user"=>ATF::$usr->getID()
            ,"date"=>"2050-08-01 09:00:00"
            ,"type_facture"=>"solde"
            ,"ref"=>ATF::affaire()->getRef("2050-08-01","facture")
            ,"date_previsionnelle"=>"2050-08-31 09:00:00"   
        ));
        $id_fc = ATF::commande_facture()->i(array("id_facture"=>$id_facture,"id_commande"=>$cmd));
        $s = array();
        $this->obj->generic("facture",$id_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."facture-solde-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6ebd089e3e72d749385ca7787fb62926",$md5,"Erreur de génération d'une facture solde");
        
    }
    
    /* @author Quentin JANON <qjanon@absystech.fr> 
    */
    public function test_devis() {
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
                ,"id_pays"=>"UK"
                ,"facturation_id_pays"=>"UK"
            )
        );
        
        $this->ligneDevis['devis_ligne'][3] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1.2
            ,"devis_ligne.prix"=>20.99
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"acompte"=>30
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devis-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("070ea2e7a2f4be410375354c480984be",$md5,"Erreur de génération du devis 1");
        

    }


     public function test_devis_location() {       
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
                ,"type_devis"=>"location"
                ,"duree_location"=>50
                ,"prix_location"=>300
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );
        

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devis_location-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("93a2c6a485317ed99129cdc51d0f2542",$md5,"Erreur de génération du devis 1");
        

    }


    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
     public function test_devisAvecFinancement() {
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
                ,"id_pays"=>"UK"
                ,"facturation_id_pays"=>"UK"
            )
        );
        
        $this->ligneDevis['devis_ligne'][3] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>2000
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
                ,"duree_financement" => 10
                ,"cout_total_financement" => 40000
                ,"maintenance_financement" => 40
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisAvecFinancement-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("273defbe999d442ca40ccef8a3289923",$md5,"Erreur de génération du devis devisAvecFinancement");
    }
    
    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
     public function test_devisAvecFinancement2000euros() {
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
                ,"id_pays"=>"UK"
                ,"facturation_id_pays"=>"UK"
            )
        );
        $this->ligneDevis['devis_ligne'] = array();
        
        
        $this->ligneDevis['devis_ligne'][0] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>2000
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
                
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisAvecFinancement2000euros-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("5a99388db956cae620a6ed991bec8318",$md5,"Erreur de génération du devis devisAvecFinancement");
    }
    
    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
     public function test_devisAvecFinancement9000euros() {
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
                ,"id_pays"=>"UK"
                ,"facturation_id_pays"=>"UK"
            )
        );
        $this->ligneDevis['devis_ligne'] = array();
        
        
        $this->ligneDevis['devis_ligne'][0] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>9000
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
                
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisAvecFinancement9000euros-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("b5650952b3d9ad52e770860494c0c7f4",$md5,"Erreur de génération du devis devisAvecFinancement");
    }

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
     public function test_devisAvecFinancement22000euros() {
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
                ,"id_pays"=>"UK"
                ,"facturation_id_pays"=>"UK"
            )
        );
        $this->ligneDevis['devis_ligne'] = array();
        
        
        $this->ligneDevis['devis_ligne'][0] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>22000
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
                
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisAvecFinancement22000euros-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("72709dc371376a47fccbec40b936b7ec",$md5,"Erreur de génération du devis devisAvecFinancement");
    }

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
     public function test_devisAvecFinancement40000euros() {
        ATF::societe()->update(
            array(
                "id_societe"=>ATF::$usr->get("id_societe")
                ,"reference_tva"=>"FR88444804066"
                ,"id_pays"=>"UK"
                ,"facturation_id_pays"=>"UK"
            )
        );
        $this->ligneDevis['devis_ligne'] = array();
        
        
        $this->ligneDevis['devis_ligne'][0] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>40000
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
                
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisAvecFinancement40000uros-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("741d7f6e3797f2d3fe9f14962ba8a058",$md5,"Erreur de génération du devis devisAvecFinancement");
    }    

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_devis_Periodique(){
        $this->ligneDevis['devis_ligne'][3] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>20.50
            ,"devis_ligne.periode"=>"mois"
        );
        $this->ligneDevis['devis_ligne'][4] = array(
            "devis_ligne.ref"=>"REF4"
            ,"devis_ligne.produit"=>"Produit TU n°4"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>60
            ,"devis_ligne.periode"=>"mois"
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisPeriodique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("06292c6cff470559ee301653b0e95160",$md5,"Erreur de génération du devis devisPeriodique");
    }
     /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
     public function test_devis_Periodique2(){
        $this->ligneDevis['devis_ligne'][3] = array(
            "devis_ligne.ref"=>"REF3"
            ,"devis_ligne.produit"=>"Produit TU n°3"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>20
            ,"devis_ligne.periode"=>"mois"
        );
        $this->ligneDevis['devis_ligne'][4] = array(
            "devis_ligne.ref"=>"REF4"
            ,"devis_ligne.produit"=>"Produit TU n°4"
            ,"devis_ligne.quantite"=>1
            ,"devis_ligne.prix"=>60
            ,"devis_ligne.periode"=>"mois"
        );
        $id_devis = ATF::devis()->insert(array(
            "devis"=>array(
                "id_affaire"=>$this->id_affaire
                ,"id_societe"=>ATF::$usr->get("id_societe")
                ,"resume"=>"Devis 2 pour les TU"
                ,"tva"=>"1.2"
                ,"validite"=>"2050-08-01"
                ,"id_contact"=>$this->id_contact
                ,"date"=>"2050-08-01 09:00:00"
                ,"id_delai_de_realisation" => 1
            ),
            "values_devis"=>array("produits"=>json_encode($this->ligneDevis['devis_ligne']))
            ),ATF::_s()
        );

        $this->obj->generic("devis",$id_devis,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."devisPeriodique2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1dadf4666e4e926cce36791c5759aeb9",$md5,"Erreur de génération du devis devisPeriodique");
    }
    
    
    
};

class factureTestfacture_exoneration_tva_ extends pdf_absystech { } // Inclusion du bon fichier qui contient aewd
class factureTestfacture_exoneration_tva extends pdf_aewd {
	function facture_exoneration_tva() {
		parent::facture_exoneration_tva(array("prix"=>"42"),array("reference_tva"=>"REF TVA A DEUX BALLES"));
	}
}
?>
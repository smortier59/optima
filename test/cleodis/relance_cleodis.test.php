<?
/**
* Classe de test sur le module societe_cleodis
*/
class relance_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUser();
        
        $this->id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));

        $this->id_devis=ATF::devis()->decryptId(ATF::devis()->i(array("devis"=>"Devis tu","ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_contact"=>$this->id_contact,"validite"=>"2050-01-01","type_contrat"=>"lld","id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$this->id_affaire)));
        
        $this->id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$this->id_affaire)));
        
        $this->id_demande_refi=ATF::demande_refi()->decryptId(ATF::demande_refi()->i(array(
            "date"=>date("Y-m-d"),
            "id_contact"=>$this->id_contact,
            "id_refinanceur"=>1,
            "id_affaire"=>$this->id_affaire,
            "id_societe"=>$this->id_societe,
            "description"=>"Tu description",
            "loyer_actualise"=>50,
            "etat"=>"valide"
        )));

        $this->id_facture=ATF::facture()->decryptId(ATF::facture()->i(array(
            "ref"=>"refTU",
            "type_facture"=>"facture",
            "id_societe"=>$this->id_societe,
            "prix"=>120,
            "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
            "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),
            "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
            "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 4 month")),
            "date_periode_debut"=>date("Y-m-01"),
            "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
            "tva"=>"1.196",
            "id_user"=>$this->id_user,
            "id_commande"=>$this->id_commande,
            "id_affaire"=>$this->id_affaire,
            "id_demande_refi"=>$this->id_demande_refi
        )));

        $this->id_facture2=ATF::facture()->decryptId(ATF::facture()->i(array(
            "ref"=>"refTU2",
            "type_facture"=>"facture",
            "id_societe"=>$this->id_societe,
            "prix"=>1200,
            "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
            "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),
            "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
            "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 4 month")),
            "date_periode_debut"=>date("Y-m-01"),
            "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
            "tva"=>"1.196",
            "id_user"=>$this->id_user,
            "id_commande"=>$this->id_commande,
            "id_affaire"=>$this->id_affaire,
            "id_demande_refi"=>$this->id_demande_refi
        )));

        $this->id_facture3=ATF::facture()->decryptId(ATF::facture()->i(array(
            "ref"=>"refTU3",
            "type_facture"=>"facture",
            "id_societe"=>$this->id_societe,
            "prix"=>12000,
            "date"=>date("Y-m-d",strtotime(date("Y-m-01")." 1 month")),
            "date_previsionnelle"=>date("Y-m-d",strtotime(date("Y-m-01")." 2 month")),
            "date_paiement"=>date("Y-m-d",strtotime(date("Y-m-01")." + 3 month")),
            "date_relance"=>date("Y-m-d",strtotime(date("Y-m-01")." + 4 month")),
            "date_periode_debut"=>date("Y-m-01"),
            "date_periode_fin"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month")),
            "tva"=>"1.196",
            "id_user"=>$this->id_user,
            "id_commande"=>$this->id_commande,
            "id_affaire"=>$this->id_affaire,
            "id_demande_refi"=>$this->id_demande_refi
        )));


	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
    
    public function creerRelance($preview=false) {
        $infos = array(
            "id_facture"=>$this->id_facture
            ,"texte"=>"Exemple de test de relance de TU"
            ,"autreFacture"=>$this->id_facture2
            
        );
        if ($preview) $infos["preview"]=true;
        $r = $this->obj->insert($infos);
        return $r;  
    }
        
    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_insertPremiereRelance() {
        $r = $this->creerRelance();
        $relance = $this->obj->select($r);
        
        $this->assertNotNull($r,"Erreur dans le retour... pas d'ID?");
        $this->assertEquals($this->id_societe,$relance['id_societe'],"Erreur dans le id_societe");
        $this->assertEquals($this->id_contact,$relance['id_contact'],"Erreur dans le id_contact");
        $this->assertEquals("premiere",$relance['type'],"Erreur dans le type");
        
        ATF::relance_facture()->q->reset()->setCount()->where("id_relance",$relance['id_relance']);
        $r = ATF::relance_facture()->sa();

        $this->assertEquals(2,$r['count'],"Erreur dans le compte de liaison relance_facture");
                
        $fp = $this->obj->filepath($relance['id_relance'],'relance1',true);
        $this->assertFileExists($fp,"Erreur de création du fichier PDF");
    }

    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_insertSecondeRelance() {
        $this->creerRelance();
        $r = $this->creerRelance();
        $relance = $this->obj->select($r);
        
        $this->assertNotNull($r,"Erreur dans le retour... pas d'ID?");
        $this->assertEquals($this->id_societe,$relance['id_societe'],"Erreur dans le id_societe");
        $this->assertEquals($this->id_contact,$relance['id_contact'],"Erreur dans le id_contact");
        $this->assertEquals("seconde",$relance['type'],"Erreur dans le type");
        
        ATF::relance_facture()->q->reset()->setCount()->where("id_relance",$relance['id_relance']);
        $r = ATF::relance_facture()->sa();
        $this->assertEquals(2,$r['count'],"Erreur dans le compte de liaison relance_facture");
                
        $fp = $this->obj->filepath($relance['id_relance'],'relance2',true);
        $this->assertFileExists($fp,"Erreur de création du fichier PDF");
        
    }

    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_insertMiseEnDemeure() {
        $this->creerRelance();
        $this->creerRelance();
        $r = $this->creerRelance();
        $relance = $this->obj->select($r);
        
        $this->assertNotNull($r,"Erreur dans le retour... pas d'ID?");
        $this->assertEquals($this->id_societe,$relance['id_societe'],"Erreur dans le id_societe");
        $this->assertEquals($this->id_contact,$relance['id_contact'],"Erreur dans le id_contact");
        $this->assertEquals("mise_en_demeure",$relance['type'],"Erreur dans le type");
        
        ATF::relance_facture()->q->reset()->setCount()->where("id_relance",$relance['id_relance']);
        $r = ATF::relance_facture()->sa();
        $this->assertEquals(2,$r['count'],"Erreur dans le compte de liaison relance_facture");
                
        $fp = $this->obj->filepath($relance['id_relance'],'relance3',true);
        $this->assertFileExists($fp,"Erreur de création du fichier PDF");
         
    }

    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_insertPreview() {
        $r = $this->creerRelance(true);
        $this->assertNotNull($r,"Erreur dans le retour... pas d'ID?");
    }
    
    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_getNumeroDeRelanceINT() {
        $this->assertEquals(0,$this->obj->getNumeroDeRelance($this->id_facture,true),"Erreur dans le nombre de relance 1");
        $this->creerRelance();
        $this->assertEquals(1,$this->obj->getNumeroDeRelance($this->id_facture,true),"Erreur dans le nombre de relance 2");
        $this->creerRelance();
        $this->assertEquals(2,$this->obj->getNumeroDeRelance($this->id_facture,true),"Erreur dans le nombre de relance 3");
        $this->creerRelance();
        $this->assertEquals(3,$this->obj->getNumeroDeRelance($this->id_facture,true),"Erreur dans le nombre de relance 4");
            }
    
    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_getNumeroDeRelance() {
        $this->assertEquals("premiere",$this->obj->getNumeroDeRelance($this->id_facture),"Erreur dans le nombre de relance 1");
        $this->creerRelance();
        $this->assertEquals("seconde",$this->obj->getNumeroDeRelance($this->id_facture),"Erreur dans le nombre de relance 2");
        $this->creerRelance();
        $this->assertEquals("mise_en_demeure",$this->obj->getNumeroDeRelance($this->id_facture),"Erreur dans le nombre de relance 3");
        $this->creerRelance();
        $this->assertFalse($this->obj->getNumeroDeRelance($this->id_facture),"Erreur dans le nombre de relance 4");
    }
        

    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_getIdRelance() {
        $this->assertFalse($this->obj->getIdRelance(),"Doit return FALSE");
        $a = ATF::relance()->decryptId($this->creerRelance());
        
        $r = $this->obj->getIdRelance($this->id_facture,"premiere");
        
        $this->assertEquals($a,$r,"Erreur dans le rappatriement de relance");
        
        $this->assertNull($this->obj->getIdRelance($this->id_facture,"seconde"));
    }
    
    /** @author Quentin JANON <qjanon@absystech.fr>*/
    public function test_getIdFactures() {
        $this->assertFalse($this->obj->getIdFactures(),"Doit return FALSE");
        $a = ATF::relance()->decryptId($this->creerRelance());
        
        $r = $this->obj->getIdFactures($a,$this->id_facture);
        

        $this->assertEquals($this->id_facture2,$r["id_autreFacture"],"Erreur de id_autreFacture");
        $this->assertEquals("refTU2",$r["ref_autreFacture"],"Erreur de ref_autreFacture");
    }
    
    
    
};

?>
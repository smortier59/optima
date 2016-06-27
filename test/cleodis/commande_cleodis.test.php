<?
/**
* Classe de test sur le module societe_cleodis
*/
class commande_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	protected static $devis = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';
	
	protected static $commande = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:10:"10-05-2011";s:10:"id_affaire";s:4:"6002";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"5929";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:194:"[{"loyer__dot__loyer":"233.00","loyer__dot__duree":"34","loyer__dot__assurance":"2.00","loyer__dot__frais_de_gestion":"1.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:15:"produits_repris";s:0:"";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"b0b7d2cb34ecebdb2b0016f2774297c3","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"a54f087ade4365f565b92d07124b2de3","commande_ligne__dot__id_commande_ligne":"044def0d691d5872f596e3a26813547f"}]";s:20:"produits_non_visible";s:0:"";}}';

//	* Méthode pré-test, exécute avant chaque test unitaire
//	* besoin d'un user pour les traduction
//	
	public function setUp() {
		$this->initUser();
//		$this->s = ATF::_s();
//		ATF::db()->begin_transaction(true);
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
        ATF::$msg->getNotices();
	}

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function test_defaultValues(){
        //Devis
        $devis = unserialize(self::$devis);
        $devis["values_devis"]["produits"]= '[{"devis_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
        $devis["devis"]["id_contact"]=  4753;
        $devis["devis"]["date"]=  date("Y-m-d");
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));

        ATF::_r('id_devis',$id_devis);
        $devis = ATF::devis()->select($id_devis);       
        $this->assertEquals($id_devis,ATF::commande()->default_value("id_devis"),'valeur id_devis');
        $this->assertEquals("5391",ATF::commande()->default_value("id_societe"),'valeur societe');
        $this->assertEquals($devis["ref"],ATF::commande()->default_value("ref"),'valeur ref');
        $this->assertEquals($devis["devis"],ATF::commande()->default_value("commande"),'valeur commande');
        $this->assertEquals("prelevement",ATF::commande()->default_value("type"),'valeur type');
        $this->assertEquals("non",ATF::commande()->default_value("clause_logicielle"),'valeur clause_logicielle');
        $this->assertEquals($devis["id_affaire"],ATF::commande()->default_value("id_affaire"),'valeur id_affaire');
        $this->assertEquals(date("Y-m-d"),ATF::commande()->default_value("date"),'valeur date');
        $this->assertEquals(ATF::contact()->select($devis["id_contact"],"email"),ATF::commande()->default_value("email"),'valeur email');
        $this->assertEquals(ATF::$usr->get("email"),ATF::commande()->default_value("emailCopie"),'valeur emailCopie');
        $this->assertEquals($devis["prix_achat"],ATF::commande()->default_value("prix_achat"),'valeur prix_achat');
        $this->assertEquals($devis["prix"],ATF::commande()->default_value("prix"),'valeur prix');
        $this->assertEquals($this->obj->majMail(5391),ATF::commande()->default_value("emailTexte"),'valeur emailCopie');

        ATF::_r('id_devis','');

        //Commande
        $commande = unserialize(self::$commande);
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));  
        ATF::$msg->getNotices();   

        ATF::_r('id_commande',$id_commande);
        $commande = ATF::commande()->select($id_commande);      
        $this->assertEquals($id_devis,ATF::commande()->default_value("id_devis"),'valeur id_devis');
        $this->assertEquals("5391",ATF::commande()->default_value("id_societe"),'valeur societe');
        $this->assertEquals($commande["ref"],ATF::commande()->default_value("ref"),'valeur ref');
        $this->assertEquals($commande["commande"],ATF::commande()->default_value("commande"),'valeur commande');
        $this->assertEquals($commande["type"],ATF::commande()->default_value("type"),'valeur type');
        $this->assertEquals($commande["clause_logicielle"],ATF::commande()->default_value("clause_logicielle"),'valeur clause_logicielle');
        $this->assertEquals($commande["id_affaire"],ATF::commande()->default_value("id_affaire"),'valeur id_affaire');
        $this->assertEquals($commande["date"],ATF::commande()->default_value("date"),'valeur date');
        $this->assertEquals(ATF::$usr->get("email"),ATF::commande()->default_value("emailCopie"),'valeur emailCopie');
        $this->assertEquals($commande["prix_achat"],ATF::commande()->default_value("prix_achat"),'valeur prix_achat');
        $this->assertEquals($commande["prix"],ATF::commande()->default_value("prix"),'valeur prix');
        $this->assertNull(ATF::commande()->default_value("email"),'valeur email');

    }

    /*@author Morgan FLEURQUIN <mfleurquin@absystech.fr>  */
    
    public function testgetDateResti(){
        $this->assertEquals("2016-02-29",$this->obj->getDateResti(array("id_commande"=>6968)),'Date Resti incorrect');
    }


    
    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    
    public function testCheckUpdateAR(){
        $fille=array("ref"=>"fille","id_societe"=>$this->id_societe,"affaire"=>"fille");
        $id_fille=ATF::affaire()->i($fille);
        $objFille = new affaire_cleodis($id_fille);
        $this->obj->checkUpdateAR($objFille);
        
        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"testCheckUpdateAR","id_fille"=>$id_fille);
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"avenant","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_fille);
        $id_commande=ATF::commande()->i($commande);
        $objAffaire = new affaire_cleodis($id_affaire);
        $this->obj->checkUpdateAR($objAffaire);
        
        ATF::commande()->u(array("id_commande"=>$id_commande,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d")));
        try {
            $this->obj->checkUpdateAR($objAffaire);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(877,$error,'On ne peut pas modifier/supprimer une commande qui est AnnulÃ©e et RemplacÃ©e par une autre affaire');
    
    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testCheckUpdateAVT(){
        $commande=array("ref"=>"commande","id_societe"=>$this->id_societe,"affaire"=>"commande");
        $id_commande=ATF::affaire()->i($commande);
        $selectCommande =ATF::affaire()->select($id_commande);
        $this->obj->checkUpdateAVT($selectCommande);

        $vente=array("ref"=>"vente","id_societe"=>$this->id_societe,"affaire"=>"vente","nature"=>"vente");
        $id_vente=ATF::affaire()->i($vente);
        $selectVente =ATF::affaire()->select($id_vente);
        try {
            $this->obj->checkUpdateAVT($selectVente);
        } catch (errorATF $e) {
            $error1 = $e->getCode();
        }
        $this->assertEquals(875,$error1,'On ne peut pas modifier/supprimer cette commande car ses produits sont vendus dans l affaire');
    
        $avenant=array("ref"=>"avenant","id_societe"=>$this->id_societe,"affaire"=>"avenant","nature"=>"avenant");
        $id_avenant=ATF::affaire()->i($avenant);
        $selectAvenant =ATF::affaire()->select($id_avenant);
        $this->obj->checkUpdateAVT($selectAvenant);
        
        $commande=array("ref"=>"avenant","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_avenant,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d"));
        ATF::commande()->i($commande);
        try {
            $this->obj->checkUpdateAVT($selectAvenant);
        } catch (errorATF $e) {
            $error2 = $e->getCode();
        }
        $this->assertEquals(876,$error2,'On ne peut pas modifier/supprimer une commande qui a un avenant, il faut d abord supprimer les dates de l avenant');
    }

    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>  */ 
    public function testCan_delete(){

        $devis = unserialize(self::$devis);
        $devis["values_devis"]["produits"]= '[{"devis_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        $devis_select = ATF::devis()->select($id_devis);
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();

        $commande = unserialize(self::$commande);
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $commande["commande"]["id_affaire"]=$devis_select["id_affaire"];
        $id_commande = $this->obj->insert($commande,$this->s,NULL,$refresh);        

        $this->assertTrue($this->obj->can_delete($id_commande),'Can_delete doit pouvoir laisser une commande en "non_loyer" être supprimée');
        
        $id_parc = ATF::parc()->i(array("id_societe"=> $this->id_societe , "id_produit"=>5, "id_affaire"=>$commande["commande"]["id_affaire"] , "ref"=>"Une ref", "libelle"=>"libelle", "serial"=>"toto", "etat"=>"loue" , "existence"=>"actif"));
        try {
             $this->obj->can_delete($id_commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(84513,$error,'can_delete ne doit pas supprimer une commande avec un parc actif');
        ATF::parc()->d(array("id_parc" =>$id_parc ));
        
        $this->obj->u(array("id_commande"=>$id_commande,"etat"=>"mis_loyer"));
        try {
             $this->obj->can_delete($id_commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(879,$error,'can_delete ne doit pas laisser supprimer une commande "mis_loyer" 1');

        $id_affaire2=ATF::affaire()->i(array("ref"=>"refTuEnfant","id_societe"=>$this->id_societe,"affaire"=>"AffaireTuEnfant","id_parent"=>$devis_select["id_affaire"]));
         
        try {
             $this->obj->can_delete($id_commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(879,$error,'can_delete ne doit pas laisser supprimer une commande "mis_loyer" 2');
        
        ATF::facture()->i(array("ref"=>"test_tu","id_societe"=>$this->id_societe,"prix"=>100,"date"=>date("Y-m-d"),"tva"=>"1.96","id_commande"=>$id_commande,"id_affaire"=>$commande["commande"]["id_affaire"]));
        try {
             $this->obj->can_delete($id_commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(879,$error,'can_delete ne doit pas laisser supprimer une commande avec une facture');
        
    }

    
    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testCan_update(){
        $devis = unserialize(self::$devis);
        $devis["values_devis"]["produits"]= '[{"devis_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();

        $commande = unserialize(self::$commande);
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $id_commande = $this->obj->insert($commande,$this->s,NULL,$refresh);        

        $this->assertFalse($this->obj->can_update($id_commande),'On ne doit pas pouvoir modifier une commande');
    }


/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_checkEtat(){

        /**************************VENTE***********************/
        $devis = unserialize(self::$devis);
        $devis["preview"]=true;
        $devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351"}]';
        $devis["devis"]["type_contrat"] = "vente";
        $devis["panel_vente-checkbox"] = "on";
        $devis["devis"]["prix_vente"]=3000;
        $devis["vente"] = "affaire_26";
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne = ATF::devis_ligne()->sa();

        $commande = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:10:"11-05-2011";s:10:"id_affaire";s:4:"6210";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:8:"3 000.00";s:10:"prix_achat";s:6:"103.00";s:5:"marge";s:5:"96.57";s:13:"marge_absolue";s:8:"2 897.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6137";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:190:"[{"loyer__dot__loyer":"3000.00","loyer__dot__duree":"1","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":3000}]";s:15:"produits_repris";s:572:"[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"e397ce41979133ecb54f810af7de6f25","commande_ligne__dot__prix_achat":"100.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__id_produit_fk":"22bc1812bc68989afcde8962b81b0882","commande_ligne__dot__serial":"5X7ZB2J","commande_ligne__dot__id_commande_ligne":"f010c6c164d0c6ea083a7fdedc4cea27"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"2af4569e9740cafd20b61f5bc957797b","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"f23a58cfc5353c62f5e261b25d791ea6","commande_ligne__dot__id_commande_ligne":"202dae48031c294619bd62aa26bd6936"}]";s:20:"produits_non_visible";s:0:"";}}';
        $commande = unserialize($commande);
        
        $commande["values_commande"]["produits_repris"] = '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"100.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__id_produit_fk":"5893","commande_ligne__dot__serial":"5X7ZB2J","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits"] = '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $commande["commande"]["date"]=date("Y-m-01");
        
        $id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));
        ATF::$msg->getNotices();
        ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"non_loyer"));
        $commande = new commande_cleodis($id_commande);
        $this->obj->checkEtat($commande);
        $this->assertEquals("non_loyer",$commande->get("etat"),'Une vente doit rester en non_loyer tant qu il n y a pas de facture');
        
        ATF::facture()->i(array("ref"=>"test_tu","id_societe"=>$this->id_societe,"prix"=>100,"date"=>date("Y-m-d"),"tva"=>"1.96","id_commande"=>$id_commande,"id_affaire"=>$commande->get("id_affaire")));

        ATF::commande()->u(array("id_commande"=>$id_commande,"etat"=>"non_loyer"));
        $commande = new commande_cleodis($id_commande);
        $this->obj->checkEtat($commande);
        $this->assertEquals("vente",$commande->get("etat"),'Une vente doit passer en vente quand il y a une facture');
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande '7001001' a changé de 'En attente' à 'vente'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 1 !");
                            

        /**************************CONTRAT SIMPLE***********************/

        $id_affaireParent=ATF::affaire()->i(array("ref"=>"refTuParent","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu","id_fille"=>$id_affaireFille));
        $id_commandeParent=ATF::commande()->i(array("ref"=>"Ref tuParent","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaireParent));

                                                                                
            /**************************mis_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 1 !");
        $this->assertEquals("mis_loyer",$commandeParent->get("etat"),"la commande doit être en en_cours !");
        
            /**************************prolongation***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'Prolong.'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 2 !");
        $this->assertEquals("prolongation",$commandeParent->get("etat"),"la commande doit être en prolongation !");
        
            /**************************non_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 2 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);
        $this->assertEquals("non_loyer",$commandeParent->get("etat"),"la commande doit être en non_loyer !");
        
        /**************************CONTRAT AR FILLE***********************/

        $id_affaireFille=ATF::affaire()->i(array("ref"=>"refTuFille","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
        $id_commandeFille=ATF::commande()->i(array("ref"=>"Ref tuFille","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaireFille,"etat"=>"mis_loyer"));
        $id_affaireParent=ATF::affaire()->u(array("id_affaire"=>$id_affaireParent,"id_fille"=>$id_affaireFille));

        $affaireFilles=ATF::affaire()->getFillesAR($commandeParent->get("id_affaire"));
            /**************************mis_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent,false,$affaireFilles);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'AR'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 4 !");
        $this->assertEquals("AR",$commandeParent->get("etat"),"la commandeParent doit être en AR !");
        
            /**************************prolongation***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent,false,$affaireFilles);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'AR'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 5 !");
        $this->assertEquals("AR",$commandeParent->get("etat"),"la commandeParent doit être en AR !");
        
            /**************************non_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 2 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent,false,$affaireFilles);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'AR'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 6 !");
        $this->assertEquals("AR",$commandeParent->get("etat"),"la commandeParent doit être en AR !");


        /**************************CONTRAT AR PARENT***********************/
        
        $commandeFille = new commande_cleodis($id_commandeFille);
        $affaireFilles = $commandeFille->getAffaire();

        if ($ap = $affaireFilles->getParentAR()) {
            // Parfois l'affaire a plusieurs parents car elle annule et remplace plusieurs autres affaires
            foreach ($ap as $a) {
                $affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);
            }
        }

            /**************************mis_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeFille,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"etat"=>"non_loyer"));
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"etat"=>"non_loyer"));
        $commandeFille = new commande_cleodis($id_commandeFille);
        $this->obj->checkEtat($commandeFille,$affaires_parentes);
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuFille' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'AR'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 7 !");
        $this->assertEquals("mis_loyer",$commandeFille->get("etat"),"la commandeFille doit être en mis_loyer !");
        $this->assertEquals("AR",$commandeParent->get("etat"),"la commandeParent doit être en AR !");

            /**************************prolongation***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeFille,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),"etat"=>"non_loyer"));
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"etat"=>"non_loyer"));
        $commandeFille = new commande_cleodis($id_commandeFille);
        $this->obj->checkEtat($commandeFille,$affaires_parentes);
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuFille' a changé de 'En attente' à 'Prolong.'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'AR'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 8 !");
        $this->assertEquals("prolongation",$commandeFille->get("etat"),"la commandeFille doit être en AR !");
        $this->assertEquals("AR",$commandeParent->get("etat"),"la commandeParent doit être en prolongation !");

            /**************************non_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeFille,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 2 day")),"etat"=>"non_loyer"));
                /**************************mis_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"etat"=>"non_loyer"));
        $commandeFille = new commande_cleodis($id_commandeFille);
        $this->obj->checkEtat($commandeFille,$affaires_parentes);
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 9 !");
        $this->assertEquals("non_loyer",$commandeFille->get("etat"),"la commandeFille doit être en non_loyer 9 !");
        $this->assertEquals("mis_loyer",$commandeParent->get("etat"),"la commandeParent doit être en mis_loyer 9 !");

                /**************************non_loyer***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeFille,"etat"=>"non_loyer"));
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 2 day")),"etat"=>"non_loyer"));
        $commandeFille = new commande_cleodis($id_commandeFille);
        $this->obj->checkEtat($commandeFille,$affaires_parentes);
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->assertEquals("non_loyer",$commandeFille->get("etat"),"la commandeFille doit être en non_loyer 10 !");
        $this->assertEquals("non_loyer",$commandeParent->get("etat"),"la commandeParent doit être en non_loyer 10 !");

                /**************************prolongation***********************/
        ATF::commande()->u(array("id_commande"=>$id_commandeFille,"etat"=>"non_loyer"));
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),"etat"=>"non_loyer"));
        $commandeFille = new commande_cleodis($id_commandeFille);
        $this->obj->checkEtat($commandeFille,$affaires_parentes);
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'Ref tuParent' a changé de 'En attente' à 'Prolong.'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 11 !");
        $this->assertEquals("non_loyer",$commandeFille->get("etat"),"la commandeFille doit être en non_loyer 11 !");
        $this->assertEquals("prolongation",$commandeParent->get("etat"),"la commandeParent doit être en prolongation 11 !");
        
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,
                                 "date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."- 4 day")),
                                 "date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")),
                                 "date_prevision_restitution" => date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),
                                 "etat"=>"non_loyer"));
        $commande = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commande);
        $this->assertEquals("restitution",$commande->get("etat"),"la commande doit être en restitution !");
        
        
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,
                                 "date_debut"=>date("Y-m-d",strtotime(date("Y-m-d")."- 3 day")),
                                 "date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")),
                                 "date_restitution_effective" => date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),
                                 "etat"=>"non_loyer"));
        $commande = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commande);
        $this->assertEquals("arreter",$commande->get("etat"),"la commande doit être en arreter !");

    }

    
    
    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_checkEtatContentieux(){
        $id_affaireParent=ATF::affaire()->i(array("ref"=>"refTuParent","nature"=>"avenant","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
        $id_commandeParent=ATF::commande()->i(array("ref"=>"Ref tuParent","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaireParent));
        
        $id_affaireParentParent = ATF::affaire()->i(array("ref"=>"ParentParent","id_societe"=>$this->id_societe,"affaire"=>"AffParentP","id_fille"=>$id_affaireParent));
        $id_commandeParentParent=ATF::commande()->i(array("ref"=>"Ref tuParentPa","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaireParentParent));
            
        
        //EN COURS
        ATF::commande()->u(array("id_commande"=>$id_commandeParent,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"etat"=>"non_loyer"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("mis_loyer",$commandeParent->get("etat"),"la commande doit être en en_cours !");
        
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat" => "prolongation_contentieux")); 
        $commandeParent = new commande_cleodis($id_commandeParent); 
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("mis_loyer_contentieux",$commandeParent->get("etat"),"la commande doit être en en_cours_contentieux !!!");
        
        
        //RESTITUTION d'un contrat en cours
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),  "etat" => "mis_loyer", "date_prevision_restitution"=> date("Y-m-d",strtotime(date("Y-m-d")."- 2 day"))));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("restitution",$commandeParent->get("etat"),"la commande doit être en restitution_contentieux !!!");

    
        ATF::affaire()->u(array("id_affaire"=> $id_affaireParent , "id_parent"=> $id_affaireParentParent));
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat" => "mis_loyer_contentieux"));
                    
        
        $commandeParent = new commande_cleodis($id_commandeParent);
        $res = $this->obj->checkEtat($commandeParent);      
        $this->assertEquals("restitution_contentieux",$commandeParent->get("etat"),"la commande doit être en restitution_contentieux !!!");
        $this->assertEquals($id_affaireParentParent,$res["commande.id_affaire_fk"],"Affaire Parente incorrecte");
        
                    
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat" => "mis_loyer_contentieux", "date_restitution_effective"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day"))));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $res = $this->obj->checkEtat($commandeParent);      
        $this->assertEquals("arreter",$commandeParent->get("etat"),"la commande doit être en arreter !!!");
        
                       
        //PROLONGATION
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat"=>"non_loyer" ,"date_prevision_restitution"=> NULL, "date_restitution_effective"=>NULL,"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day"))));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("prolongation",$commandeParent->get("etat"),"la commande doit être en prolongation !!!");
        
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat" => "mis_loyer_contentieux" ,"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day"))));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("prolongation_contentieux",$commandeParent->get("etat"),"la commande doit être en prolongation_contentieux !!!");
        
        //Restitution d'un contrat en prolongation      
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat" => "prolongation", "date_prevision_restitution"=> date("Y-m-d",strtotime(date("Y-m-d")."- 2 day"))));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("restitution",$commandeParent->get("etat"),"la commande doit être en restitution_contentieux !!!");

        
        ATF::commande()->u(array("id_commande"=>$id_commandeParent, "etat" => "prolongation_contentieux"));
        $commandeParent = new commande_cleodis($id_commandeParent);
        $this->obj->checkEtat($commandeParent);     
        $this->assertEquals("restitution_contentieux",$commandeParent->get("etat"),"la commande doit être en restitution_contentieux !!!");
    }

    
    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_checkAndUpdatesDates(){
                
        $id_affaire=ATF::affaire()->i(array("ref"=>"refTuParent","nature"=>"avenant","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu"));
        $id_commande=ATF::commande()->i(array("ref"=>"Ref tuParent","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire , "date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."+ 1 day")),"etat"=>"mis_loyer", date_demande_resiliation => date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")) ));
        
        $id_affaireParentParent = ATF::affaire()->i(array("ref"=>"ParentParent","id_societe"=>$this->id_societe,"affaire"=>"AffParentP"));
        $id_commandeParentParent=ATF::commande()->i(array("ref"=>"Ref tuParentPa","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaireParentParent));
        ATF::affaire()->u(array("id_affaire"=> $id_affaire , "id_parent"=> $id_affaireParentParent));
        ATF::commande()->u(array("id_commande"=>$id_commande,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d",strtotime(date("Y-m-d")."- 1 day")),"etat"=>"prolongation"));
        ATF::commande()->u(array("id_commande"=>$id_commande, "etat" => "mis_loyer", "date_prevision_restitution"=> date("Y-m-d",strtotime(date("Y-m-d")."- 2 day"))));
        

        $infos = array('id_commande' => $id_commande, 'field' => "date_prevision_restitution"  ,'date' =>  date("Y-m-d",strtotime(date("Y-m-d")."- 2 day")));
        $this->obj->checkAndUpdateDates($infos);
                                
        $infos = array('id_commande' => $id_commande, 'field' => "date_restitution_effective"  ,'date' => NULL );
        
        try {
            $this->obj->checkAndUpdateDates($infos);
        } catch (errorATF $e) {
            $error2 = $e->getMessage();
        }
        $this->assertEquals("Il est impossible d'inserer une date de restitution effective nulle",$error2,'On ne peux pas inserer date_restitution_effective NULL');
    }
    
    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenant(){


        $id_affaire =ATF::affaire()->i(
                                        array(
                                                "etat"=>"commande",
                                                "ref"=>"0610001AVT1",
                                                "id_societe"=>5391,
                                                "affaire"=>"TU",
                                                "id_parent"=>43,
                                                "date_garantie"=>"2009-11-01",
                                                "nature"=>"avenant"
                                            )
                                        );
        $this->id_affaire = $id_affaire;
        $id_loyer =ATF::loyer()->i(
                                        array(
                                                "id_affaire"=>$id_affaire,
                                                "loyer"=>"200",
                                                "duree"=>34,
                                                "assurance"=>"2",
                                                "frais_de_gestion"=>1,
                                                "frequence_loyer"=>"mois"
                                            )
                                        );


        $id_devis =ATF::devis()->i(
                                        array(
                                                "ref"=>"0610001AVT1",
                                                "id_user"=>$this->id_user,
                                                "id_societe"=>5391,
                                                "prix"=>500,
                                                "devis"=>"TU",
                                                "type_contrat"=>"lld",
                                                "id_affaire"=>$id_affaire,
                                                "tva"=>1.196,
                                                "prix_achat"=>100,
                                                "validite"=>date("Y-m-d",strtotime("+15 day")),
                                                "id_contact"=>$this->id_contact
                                            )
                                        );

        $id_commande =ATF::commande()->i(
                                        array(
                                                "ref"=>"0610001AVT1",
                                                "id_user"=>$this->id_user,
                                                "id_societe"=>5391,
                                                "prix"=>8024,
                                                "commande"=>"TU",
                                                "id_affaire"=>$id_affaire,
                                                "tva"=>1.196,
                                                "prix_achat"=>100,
                                                "prix"=>500,
                                                "id_devis"=>$id_devis
                                            )
                                        );

        return $id_commande;
    }

    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantContratDepasseProlongation(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDateAvenant();
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));

        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 5 year"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );

        $this->obj->updateDate($date_debut);

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $getNotices=ATF::$msg->getNotices();
        $this->assertEquals("prolongation",$this->obj->select($id_commande,"etat"),"L'état ne passe pas en prolongation sur test_updateDateAvenantContratDepasseProlongation 2");

        $commande = $this->obj->select($id_commande);
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');     
        $this->assertEquals($commande["date_debut"],$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  
        
        try {
            $this->obj->delete(48);
        } catch (errorATF $e) {
            ATF::db($this->db)->rollback_transaction(); 
            $error = $e->getCode();
        }
        $this->assertEquals(876,$error,'On ne doit pas pouvoir supprimer un avenant parent');

////////////////////////On arrête le contrat///////////////////////////////////////

        
        $id_prolongation=ATF::prolongation()->i(
                                array(
                                        "id_affaire"=>$affaire["id_affaire"],
                                        "ref"=>$affaire["ref"],
                                        "id_refinanceur"=>4,
                                        "date_debut"=>date("Y-m-d",strtotime($commande["date_evolution"]."+1 day")),
                                        "date_fin"=>date("Y-m-d",strtotime($commande["date_evolution"]."+2 month")),
                                        "id_societe"=>1349
                                    )
                                );

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 6 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->updateDate($date_debut);
        
        $getNotices=ATF::$msg->getNotices();
        $this->assertEquals("arreter",$this->obj->select($id_commande,"etat"),"L'état ne passe pas en terminee sur test_updateDateAvenantContratDepasseProlongation 1");

        $commande = $this->obj->select($id_commande);
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');     
        $this->assertEquals($commande["etat"],"arreter",'le contrat devrait etre en etat arreter');     
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 3 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");   

    }

    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantContratDepasse(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDateAvenant();
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));

        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 5 year"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 5 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $getNotices=ATF::$msg->getNotices();
        $this->assertEquals("prolongation",$this->obj->select($id_commande,"etat"),"L'état ne passe pas en prolongation sur test_updateDateAvenantContratDepasse 2");

        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $commande = $this->obj->select($id_commande);

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');     
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." - 5 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantContratEnCours(){
        /////////////////////////////////////////////////////Contrat encours//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDateAvenant();
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));

        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 1 year"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 1 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $getNotices=ATF::$msg->getNotices();
        $this->assertEquals("mis_loyer",$this->obj->select($id_commande,"etat"),"L'état ne passe pas en mis_loyer sur test_updateDateAvenantContratEnCours 2");


        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $commande = $this->obj->select($id_commande);

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente contrat en cours !');        
        $this->assertEquals($commande["etat"],"mis_loyer",'Date debut : le contrat devrait etre en etat contrat en cours');     
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." - 1 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente test_updateDateAvenantContratEnCours !"); 

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantContratNonDemarre(){
        /////////////////////////////////////////////////////Contrat non démarré//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDateAvenant();
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $getNotices=ATF::$msg->getNotices();

        $this->assertEquals("non_loyer",$this->obj->select($id_commande,"etat"),"L'état ne passe pas en non_loyer sur test_updateDateAvenantContratNonDemarre 2");

        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $commande = $this->obj->select($id_commande);

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');     
        $this->assertEquals($commande["etat"],"non_loyer",'Date debut : le contrat devrait etre en etat non_loyer non démarré');        
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  

    }

    public function insertAffaire (){

        $id_affaire =ATF::affaire()->i(
                                        array(
                                                "etat"=>"commande",
                                                "ref"=>"7001001",
                                                "id_societe"=>"5391",
                                                "affaire"=>"TU",
                                                "id_parent"=>"43",
                                                "date_garantie"=>"2009-11-01"
                                            )
                                        );

        $id_loyer =ATF::loyer()->i(
                                        array(
                                                "id_affaire"=>$id_affaire,
                                                "loyer"=>"200",
                                                "duree"=>34,
                                                "assurance"=>"2",
                                                "frais_de_gestion"=>1,
                                                "frequence_loyer"=>"mois"
                                            )
                                        );

        return $id_affaire;
    }

    public function insertDevis ($id_affaire){
        
        $id_devis =ATF::devis()->i(
                                        array(
                                                "ref"=>"0610001AVT1",
                                                "id_user"=>$this->id_user,
                                                "id_societe"=>5391,
                                                "prix"=>500,
                                                "devis"=>"TU",
                                                "type_contrat"=>"lld",
                                                "id_affaire"=>$id_affaire,
                                                "tva"=>1.196,
                                                "prix_achat"=>100,
                                                "validite"=>date("Y-m-d",strtotime("+15 day")),
                                                "id_contact"=>$this->id_contact
                                            )
                                        );
                
        $id_devis_ligne1 =ATF::devis_ligne()->i(
                                        array(
                                                "type"=>"fixe",
                                                "id_devis"=>$id_devis,
                                                "id_produit"=>1175,
                                                "ref"=>ATF::produit()->select(1175,"ref"),
                                                "produit"=>ATF::produit()->select(1175,"produit"),
                                                "quantite"=>3,
                                                "id_fournisseur"=>1583,
                                                "prix_achat"=>1
                                            )
                                        );
        $id_devis_ligne2 =ATF::devis_ligne()->i(
                                        array(
                                                "type"=>"fixe",
                                                "id_devis"=>$id_devis,
                                                "id_produit"=>1175,
                                                "ref"=>ATF::produit()->select(1175,"ref"),
                                                "produit"=>ATF::produit()->select(1175,"produit"),
                                                "quantite"=>3,
                                                "id_fournisseur"=>1583,
                                                "prix_achat"=>1
                                            )
                                        );
        return $id_devis;
    }

    
    public function insertCommande ($id_affaire,$id_devis){
        
        $id_commande =ATF::commande()->i(
                                        array(
                                                "ref"=>"0610001AVT1",
                                                "id_user"=>$this->id_user,
                                                "id_societe"=>5391,
                                                "prix"=>8024,
                                                "commande"=>"TU",
                                                "id_affaire"=>$id_affaire,
                                                "tva"=>1.196,
                                                "prix_achat"=>100,
                                                "prix"=>500,
                                                "id_devis"=>$id_devis
                                            )
                                        );

        return $id_commande;
    }



    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_insert(){

        $id_affaire=$this->insertAffaire();
        $id_devis=$this->insertDevis($id_affaire);

        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();

        $commande = unserialize(self::$commande);
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits_non_visible"]='[{"commande_ligne__dot__produit":"ZywallInvis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"}]';

        $produits = $commande["values_commande"]["produits"];
        unset($commande["values_commande"]["produits"]);
        $produits_non_visible = $commande["values_commande"]["produits_non_visible"];
        unset($commande["values_commande"]["produits_non_visible"]);
        unset($commande["preview"]);
        $commande["panel_courriel-checkbox"]="on";
        $commande["commande"]["email"]="tu@absystech.fr";
        $commande["commande"]["emailCopie"]="tucopie@absystech.fr";
        $commande["commande"]["emailTexte"]="texte tu mail";
        $commande["commande"]["id_devis"]=$id_devis;
        $commande["commande"]["date"]=date("Y-m-01");
        $refresh = array();

        //Sans produits
        try {
             $id_commande = $this->obj->insert($commande);
        } catch (errorATF $e) {
            $error = $e->getCode();

        }
        $this->assertEquals(877,$error,'Erreur commmande sans produits non declenchee');
        $commande["values_commande"]["produits"] = $produits;
        $commande["values_commande"]["produits_non_visible"] = $produits_non_visible;
        //Sans produits
        try {
             $id_commande = $this->obj->insert($commande);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(878,$error,'Erreur commmande avec ref dupliquée non declenchee');

        //Obligé de faire ça à cause des transactions (la commande s insère et il y a une unicité sur ref !)
        $this->obj->q->reset()->addCondition("ref",ATF::affaire()->select(ATF::devis()->select($id_devis,"id_affaire"),"ref"))->setDimension("row");
        $commandeRef=$this->obj->sa();
        $this->obj->d($commandeRef["id_commande"]);
        
        // Erreur de contact non present
        unset($commande["commande"]["email"]);
        try {
            $error = NULL;
            classes::decryptId(ATF::commande()->insert($commande,$this->s));
        } catch (errorATF $e) {
            $error = $e->getCode(); 
        }
        $this->assertEquals(350,$error,"Erreur d'email non declenchée");

        $commande["commande"]["email"]="tu@absystech.fr";
        //Obligé de faire ça à cause des transactions (la commande s insère et il y a une unicité sur ref !)
        $this->obj->q->reset()->addCondition("ref",ATF::affaire()->select(ATF::devis()->select($id_devis,"id_affaire"),"ref"))->setDimension("row");
        $commandeRef=$this->obj->sa();
        $this->obj->d($commandeRef["id_commande"]);

        $id_commande = $this->obj->insert($commande,$this->s,NULL,$refresh);

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratA3",true));
        $this->assertNotNull($file_exist,"Le fichier contratA3 ne c est pas créé");

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratA4",true));
        $this->assertNotNull($file_exist,"Le fichier contratA4 ne c est pas créé");

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratAP",true));
        $this->assertNotNull($file_exist,"Le fichier contratAP ne c est pas créé");

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratPV",true));
        $this->assertNotNull($file_exist,"Le fichier contratPV ne c est pas créé");

        $selectCommande = $this->obj->select($id_commande);
        $this->assertEquals(array(
                                "id_commande"=>$selectCommande["id_commande"],
                                "ref"=>"7001001",
                                "id_societe"=>"5391",
                                "commande"=>"TU",
                                "prix_achat"=>"3.00",
                                "prix"=>"8024.00",
                                "date"=>date("Y-m-01"),
                                "id_devis"=>(string)$id_devis,
                                "etat"=>"non_loyer",
                                "id_user"=>$this->id_user,
                                "id_affaire"=>ATF::devis()->select($id_devis,"id_affaire"),
                                "tva"=>"1.196",
                                "clause_logicielle"=>"non",
                                "date_debut"=>NULL,
                                "type"=>"prelevement",
                                "retour_prel"=>NULL,
                                "mise_en_place"=>NULL,
                                "retour_pv"=>NULL,
                                "retour_contrat"=>NULL,
                                "date_evolution"=>NULL,
                                "date_arret"=>NULL,
                                "date_demande_resiliation"=>NULL,
                                "date_prevision_restitution"=>NULL,
                                "date_restitution_effective"=>NULL
                            )
                            ,$selectCommande,'Erreur sur la commande');

        ATF::commande_ligne()->q->reset()->addCondition("id_commande",$selectCommande["id_commande"]);
        $commande_ligne=ATF::commande_ligne()->sa();
        $this->assertEquals(array(
                                array(
                                    "id_commande_ligne"=>$commande_ligne[0]["id_commande_ligne"],
                                    "id_commande"=>$selectCommande["id_commande"],
                                    "id_produit"=>1175,
                                    "ref"=>"ZYX-FW",
                                    "produit"=>"ZywallVis 5 - dispositif de sécurité",
                                    "quantite"=>"3",
                                    "id_fournisseur"=>1583,
                                    "prix_achat"=>"1.00",
                                    "code"=>NULL,
                                    "id_affaire_provenance"=>NULL,
                                    "serial"=>NULL,
                                    "visible"=>"oui",
                                    "neuf"=>"oui",
                                    'date_achat' => NULL,
                                    'commentaire' => NULL
                                ),
                                array(
                                    "id_commande_ligne"=>$commande_ligne[1]["id_commande_ligne"],
                                    "id_commande"=>$selectCommande["id_commande"],
                                    "id_produit"=>1175,
                                    "ref"=>"ZYX-FW",
                                    "produit"=>"ZywallInvis 5 - dispositif de sécurité",
                                    "quantite"=>"3",
                                    "id_fournisseur"=>1583,
                                    "prix_achat"=>"1.00",
                                    "code"=>NULL,
                                    "id_affaire_provenance"=>NULL,
                                    "serial"=>NULL,
                                    "visible"=>"non",
                                    "neuf"=>"oui",
                                    'date_achat' => NULL,
                                    'commentaire' => NULL
                                )                           
                            )
                            ,$commande_ligne,'Erreur sur les lignes de commande');
    
        $affaire=ATF::affaire()->select($selectCommande["id_affaire"]);
        $this->assertEquals($affaire["etat"],"commande",'Erreur sur la commande');
    
    }
    

    
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_delete(){
        $id_affaire=$this->insertAffaire();
        $id_devis=$this->insertDevis($id_affaire);
        $id_commande=$this->insertCommande($id_affaire,$id_devis);
        
        $commande_select = $this->obj->select($id_commande);

        $delete_commande=array("id"=>array(0=>$id_commande,1=>"aaa"));
        $this->obj->delete($delete_commande);
        
        $affaire=ATF::affaire()->select($commande_select["id_affaire"]);
        $this->assertEquals("devis",$affaire["etat"],'Lorsque la commande est supprimée l affaire doit être en devis');
    
        $devis=ATF::devis()->select($id_devis);
        $this->assertEquals("attente",$devis["etat"],'Lorsque la commande est supprimée le devis doit être en attente');
        $this->assertEquals(NULL,$devis["date_accord"],'Lorsque la commande est supprimée le devis doit être en date_accord NULL');
                
    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_insertVente(){
        $id_affaire=$this->insertAffaire();
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"vente","id_parent"=>26));
        $id_devis=$this->insertDevis($id_affaire);
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne = ATF::devis_ligne()->sa();

        ATF::devis_ligne()->u(array("id_devis_ligne"=>$devis_ligne[1]["id_devis_ligne"],"id_affaire_provenance"=>26));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne = ATF::devis_ligne()->sa();

        $commande = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:10:"11-05-2011";s:10:"id_affaire";s:4:"6210";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:8:"3 000.00";s:10:"prix_achat";s:6:"103.00";s:5:"marge";s:5:"96.57";s:13:"marge_absolue";s:8:"2 897.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6137";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:190:"[{"loyer__dot__loyer":"3000.00","loyer__dot__duree":"1","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":3000}]";s:15:"produits_repris";s:572:"[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"e397ce41979133ecb54f810af7de6f25","commande_ligne__dot__prix_achat":"100.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__id_produit_fk":"22bc1812bc68989afcde8962b81b0882","commande_ligne__dot__serial":"5X7ZB2J","commande_ligne__dot__id_commande_ligne":"f010c6c164d0c6ea083a7fdedc4cea27"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"2af4569e9740cafd20b61f5bc957797b","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"f23a58cfc5353c62f5e261b25d791ea6","commande_ligne__dot__id_commande_ligne":"202dae48031c294619bd62aa26bd6936"}]";s:20:"produits_non_visible";s:0:"";}}';
        $commande = unserialize($commande);
        
        $commande["values_commande"]["produits_repris"] = '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"100.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__id_produit_fk":"5893","commande_ligne__dot__serial":"5X7ZB2J","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits"] = '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $commande["commande"]["date"]=date("Y-m-01");
        
        $id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));
        ATF::$msg->getNotices();
        $selectCommande = $this->obj->select($id_commande);
        $this->assertEquals(array(
                                "id_commande"=>$selectCommande["id_commande"],
                                "ref"=>"7001001",
                                "id_societe"=>"5391",
                                "commande"=>"TU",
                                "prix_achat"=>"103.00",
                                "prix"=>"3000.00",
                                "date"=>date("Y-m-01"),
                                "id_devis"=>(string)$id_devis,
                                "etat"=>"non_loyer",
                                "id_user"=>$this->id_user,
                                "id_affaire"=>ATF::devis()->select($id_devis,"id_affaire"),
                                "tva"=>"1.196",
                                "clause_logicielle"=>"non",
                                "date_debut"=>NULL,
                                "type"=>"prelevement",
                                "retour_prel"=>NULL,
                                "mise_en_place"=>NULL,
                                "retour_pv"=>NULL,
                                "retour_contrat"=>NULL,
                                "date_evolution"=>NULL,
                                "date_arret"=>NULL,
                                "date_demande_resiliation"=>NULL,
                                "date_prevision_restitution"=>NULL,
                                "date_restitution_effective"=>NULL
                            )
                            ,$selectCommande,'Erreur sur la commande');

        ATF::commande_ligne()->q->reset()->addCondition("id_commande",$id_commande);
        $commande_ligne=ATF::commande_ligne()->sa();

        $this->assertEquals(array(
                                array(
                                    "id_commande_ligne"=>$commande_ligne[0]["id_commande_ligne"],
                                    "id_commande"=>$id_commande,
                                    "id_produit"=>1175,
                                    "ref"=>"ZYX-FW",
                                    "produit"=>"Zywall 5 - dispositif de sécurité",
                                    "quantite"=>"3",
                                    "id_fournisseur"=>1583,
                                    "prix_achat"=>"1.00",
                                    "code"=>NULL,
                                    "id_affaire_provenance"=>NULL,
                                    "serial"=>NULL,
                                    "visible"=>"oui",
                                    "neuf"=>"oui",
                                    'date_achat' => NULL,
                                    'commentaire' => NULL
                                ),
                                array(
                                    "id_commande_ligne"=>$commande_ligne[1]["id_commande_ligne"],
                                    "id_commande"=>$id_commande,
                                    "id_produit"=>5893,
                                    "ref"=>"OptiGX520 17 DVD 48X-1",
                                    "produit"=>"Optiplex GX520 TFT 17 DVD 48X",
                                    "quantite"=>"1",
                                    "id_fournisseur"=>1351,
                                    "prix_achat"=>"100.00",
                                    "code"=>NULL,
                                    "id_affaire_provenance"=>26,
                                    "serial"=>NULL,
                                    "visible"=>"oui",
                                    "neuf"=>"oui",
                                    'date_achat' => NULL,
                                    'commentaire' => NULL
                                )                           
                            )
                            ,$commande_ligne,'Erreur sur les lignes de commande');

        //Preview = false donc ...
        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratA3",true));
        $this->assertFalse($file_exist,"Le fichier contratA3 ne c est pas créé");

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratA4",true));
        $this->assertFalse($file_exist,"Le fichier contratA4 ne c est pas créé");

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratAP",true));
        $this->assertFalse($file_exist,"Le fichier contratAP ne c est pas créé");

        $file_exist = file_get_contents($this->obj->filepath($this->id_user,"contratPV",true));
        $this->assertFalse($file_exist,"Le fichier contratPV ne c est pas créé");

        ATF::commande()->q->reset()->addCondition("id_affaire",26)->setDimension("row");
        $commande_parent=ATF::commande()->sa();

        //Tester erreur sur modif vente
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $commande_parent["id_commande"]
        );

        try {
            $this->obj->updateDate($date_debut);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(875,$error,'Erreur on ne doit pas pouvoir modifier les dates d une affaire qui a un avenant non démarré');

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function testUpdateDate(){
        $this->obj = ATF::commande();
        $this->assertEquals(get_class($this->obj),'commande_cleodis','Classe dans $this->obj incorrecte');
        
        $id_affaire=$this->insertAffaire();
        $id_devis=$this->insertDevis($id_affaire);
        $id_commande=$this->insertCommande($id_affaire,$id_devis);
        
        // Tests de prédicats
        $c = new commande_cleodis($id_commande);
        $this->assertFalse($c->estEnCours(),'1 Cette commande ne devrait pas etre en cours');
        $this->assertFalse($c->dateDebutDepassee(),'2 Cette commande devraitpas  avoir sa date de debut depassee');
        $this->assertFalse($c->dateEvolutionDepassee(),'3 Cette commande ne devrait pas etre en cours');
        $this->assertFalse($c->isAR(),'4 isAR Cette commande ne devrait pas etre en cours');
        $this->assertFalse($c->estSigne(),'5 estSigne Cette commande ne devrait pas etre en cours');
        
        // Test update impossible
        try {
            $error = NULL;
            $this->obj->updateDate(array(
                "value" => "2009-01-01"
                ,"key" => "date_debut"
                ,"id_commande" => "aaaaaaaaaaa"
            ));
        } catch (errorATF $e) {
            $error = true;
        }
        $this->assertTrue($error,'Erreur de type update impossible non declenchee');


        // Test de date invalide
        try {
            $error = NULL;
            $this->obj->updateDate(array(
                "value" => "2009-01-01"
                ,"key" => "date_existe_pas"
                ,"id_commande" => $id_commande
            ));
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals($error,987,'Erreur de type date inexistante non declenchee');
        
        // Date de début
        $date_debut = array(
            "value" => date("Y-m-01")
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_debut);
        $c = new commande_cleodis($id_commande);
        $this->assertTrue($c->estEnCours(),'Cette commande devrait etre en cours 1');
        $this->assertEquals($c->get("etat"),"mis_loyer",'Date debut : le contrat devrait etre en etat mis_loyer');      

        // Retour contrat
        $retour_contrat = array(
            "value" => "2009-01-01"
            ,"key" => "retour_contrat"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($retour_contrat);
        $c = new commande_cleodis($id_commande);
        $this->assertEquals($c->get("retour_prel"),$retour_contrat['value'],'Retour contrat : retour_prel resultant incorrect');
        $this->assertEquals($c->get("retour_pv"),$retour_contrat['value'],'Retour contrat : retour_pv resultant incorrect');

        // Date évolution       
        $date_evolution = array(
            "value" => "2020-01-01"
            ,"key" => "date_evolution"
            ,"id_commande" => $id_commande
            ,"table" => "commande"
        );
        $this->obj->updateDate($date_evolution);
        $c = new commande_cleodis($id_commande);
        $this->assertEquals($c->get("etat"),"mis_loyer",'Date evolution : etat de la commande devrait etre mis_loyer');


        $date_evolution = array(
            "value" => "2009-01-01"
            ,"key" => "date_evolution"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_evolution);
        $c = new commande_cleodis($id_commande);
        $this->assertEquals($c->get("etat"),"prolongation",'Date evolution : etat de la commande devrait etre prolongation');

        $date_demande_resiliation = array(
            "value" => "2009-01-01"
            ,"key" => "date_demande_resiliation"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_demande_resiliation);

        // Date restitution
        $date_demande_resiliation = array(
            "value" => "2009-01-01"
            ,"key" => "date_prevision_restitution"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_demande_resiliation);
        $c = new commande_cleodis($id_commande);
        $this->assertEquals("restitution",$c->get("etat"),'Date restitution : etat de la commande devrait etre restitution');
        
        $date_prevision_restitution = array(
            "value" => date('Y-m-d', strtotime('-1 day'))
            ,"key" => "date_prevision_restitution"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_prevision_restitution);
        $c = new commande_cleodis($id_commande);
        $this->assertEquals(date('Y-m-d', strtotime('-1 day')),$c->get("date_prevision_restitution"),'Date restitution prévue incorrecte');
        
                
        //date_restitution_effective
        $date_restitution_effective = array(
            "value" => date('Y-m-d', strtotime('-1 day'))
            ,"key" => "date_restitution_effective"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_restitution_effective);
        $c = new commande_cleodis($id_commande);
        $this->assertEquals("arreter",$c->get("etat"),'Date restitution effective : etat de la commande devrait etre arreter');
        
        // Tests des notices
        $notices = ATF::$msg->getNotices();
        $this->assertEquals(count($notices),30,'Le nombre de notices est incorrect');
    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantLoyerUnique(){
        $devisSerialize = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';
        $devis = unserialize($devisSerialize);
        
        $devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"LATITUDE D520","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"DELLLAT-1","devis_ligne__dot__prix_achat":"50","devis_ligne__dot__id_produit":"LATITUDE D520","devis_ligne__dot__id_fournisseur":"CBASE","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"BBPZB2J","devis_ligne__dot__id_produit_fk":"6066","devis_ligne__dot__id_parc":"6995","devis_ligne__dot__id_affaire_provenance":"43","devis_ligne__dot__id_fournisseur_fk":"1349"}]';
        $devis["avenant"] = "affaire_43";
        $devis["devis"]["loyer_unique"]="oui";
        $devis["devis"]["loyers"]=1000;
        $devis["devis"]["frais_de_gestion_unique"]=10;
        $devis["devis"]["assurance_unique"]=10;
        $devis["panel_avenant_lignes-checkbox"] = "on";
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();
        
        $commandeSerialize = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:0:"";s:10:"id_affaire";s:4:"6429";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:9:"39 780.00";s:10:"prix_achat";s:5:"53.00";s:5:"marge";s:5:"99.87";s:13:"marge_absolue";s:9:"39 727.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6356";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:198:"[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"39","loyer__dot__assurance":"10.00","loyer__dot__frais_de_gestion":"10.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":39780}]";s:15:"produits_repris";s:527:"[{"commande_ligne__dot__produit":"LATITUDE D520","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DELLLAT-1","commande_ligne__dot__id_fournisseur":"CBASE","commande_ligne__dot__id_fournisseur_fk":"faf6cbeded2dea2b761ebe7905d9a1ea","commande_ligne__dot__prix_achat":"50.00","commande_ligne__dot__id_produit":"LATITUDE D520","commande_ligne__dot__id_produit_fk":"bf606b897aa6a431fd1bdadda788129d","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__id_commande_ligne":"5f9fb4146f2a2f610b23e492f89aa47a"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"c3fa67e42bb24869e490543b88af9df2","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"8892f086954296d8ec3a1fd4810bc9d2","commande_ligne__dot__id_commande_ligne":"87316a8c4ab84c44e2ef2b3170902366"}]";s:20:"produits_non_visible";s:0:"";}}';
        $commande = unserialize($commandeSerialize);
        $commande["commande"]["id_devis"] = $id_devis;
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits_repris"] = '[{"commande_ligne__dot__produit":"LATITUDE D520","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DELLLAT-1","commande_ligne__dot__id_fournisseur":"CBASE","commande_ligne__dot__id_fournisseur_fk":"1349","commande_ligne__dot__prix_achat":"50.00","commande_ligne__dot__id_produit":"LATITUDE D520","commande_ligne__dot__id_produit_fk":"6066","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__id_commande_ligne":"'.$$devis_ligne[1]["id_devis_ligne"].'"}]';
        $id_commande = ATF::commande()->insert($commande,$this->s);
        ATF::$msg->getNotices();
        $this->assertNotNull($id_commande,'Commande non créé');
        return $id_commande;
    }

    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantLoyerUniqueEnCours(){
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);

        //Le parc doit donc être actif
        ATF::parc()->q->reset()->addCondition("id_affaire",43);
        $parcAncien=ATF::parc()->sa();
        $this->assertEquals(
                                array(
                                        "id_parc" => $parcAncien[0]["id_parc"],
                                        "id_societe" => 1349,
                                        "id_produit" => 6066,
                                        "id_affaire" => 43,
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "loue",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => "2006-10-09 00:00:00",
                                        "date_inactif" =>NULL,
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>NULL,
                                        "existence" =>"inactif",
                                        'date_achat' => null),
                                    $parcAncien[0],
                                    "L'ancien parc n'est pas cohérent !");  


        
        /////////////////////////////////////////////////////Contrat démarré//////////////////////////////////////////////////////////////
        
        $id_commande=$this->test_updateDateAvenantLoyerUnique();
        
        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");
        
        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-01")
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_debut);
        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $a = new affaire_cleodis($affaire["id_affaire"]);
        $ap = $a->getParentAvenant(); // Méthode pour avenant
        $ap2 = new affaire_cleodis(43);

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente démarré !');     
        $this->assertTrue($c->estEnCours(),'Cette commande ne devrait pas etre en cours Avenant démarré');
        $this->assertEquals($c->get("etat"),"mis_loyer",'Date debut : le contrat devrait etre en etat mis_loyer démarré');      
        $this->assertEquals($ap->getCommande()->get("date_evolution"),$c->get("date_evolution"),"La date d'évolution n'est pas cohérente démarré !");   
        $this->assertEquals(date("Y-m-01"),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente démarré !");   

        $notices = ATF::$msg->getNotices();
        /*
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "L'état de la commande '0610001AVT1' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            $notices,
                            "Les notices ne sont pas cohérentes pas cohérente démarré !");  

        */
        //Check Prolongation
        ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $prolongation=ATF::prolongation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_prolongation" => $prolongation[0]["id_prolongation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "0610001AVT1",
                                        "id_refinanceur" => 4,
                                        "date_debut" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 year")),
                                        "date_fin" =>NULL,
                                        "id_societe" => 5391,
                                        "date_arret" => date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day"))
                                        )
                                    ),
                                    $prolongation,
                                    "La prolongation n'est pas cohérente démarré !");   
        

        //Check facturation
        ATF::facturation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $facturation=ATF::facturation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_facturation" => $facturation[0]["id_facturation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "id_societe" => 5391,
                                        "id_facture" => NULL,
                                        "montant" => "233.00",
                                        "frais_de_gestion" => "1.00",
                                        "assurance" => "2.00",
                                        "date_periode_debut" => $c->get("date_debut"),
                                        "type" => "contrat",
                                        "envoye" => "non",
                                        "date_periode_fin" =>$c->get("date_evolution")
                                        )
                                    ),
                                    $facturation,
                                    "La facturation n'est pas cohérente démarré !");    
        
        ATF::parc()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $parc=ATF::parc()->sa();

        ATF::parc()->q->reset()->addCondition("provenance",43);
        $parcProv=ATF::parc()->sa();

        $this->assertEquals($parcProv,
                                    $parc,
                                    "Probleme sur les parcs avenant loyer unique démarré !");   
        
        $this->assertEquals(array(
                                array(
                                        "id_parc" => $parc[0]["id_parc"],
                                        "id_societe" => 5391,
                                        "id_produit" => 6066,
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "broke",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => $parc[0]["date"],
                                        "date_inactif" =>NULL,
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>43,
                                        "existence" =>"actif",
                                        'date_achat' => null)
                                    ),
                                    $parc,
                                    "Le nouveau parc n'est pas cohérent démarré !");    

        ATF::parc()->q->reset()->addCondition("id_affaire",43);
        $parcAncien=ATF::parc()->sa();
        $this->assertEquals(
                                array(
                                        "id_parc" => $parcAncien[0]["id_parc"],
                                        "id_societe" => 1349,
                                        "id_produit" => 6066,
                                        "id_affaire" => 43,
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "loue",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => "2006-10-09 00:00:00",
                                        "date_inactif" =>date("Y-m-01"),
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>NULL,
                                        "existence" =>"inactif",
                                        'date_achat' => null),
                                    $parcAncien[0],
                                    "L'ancien parc n'est pas cohérent démarré !");  

        //Tester erreur sur modif avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );

        try {
            $this->obj->updateDate($date_debut);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(876,$error,'Erreur on ne doit pas pouvoir modifier les dates d une affaire qui a un avenant non démarré');


        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 1 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $c = new commande_cleodis($id_commande);    

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande '0610001AVT1' a changé de 'En cours' à 'En attente'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente démarré !");  


        //Check Prolongation
        ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $prolongation=ATF::prolongation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_prolongation" => $prolongation[0]["id_prolongation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "0610001AVT1",
                                        "id_refinanceur" => 4,
                                        "date_debut" => date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day")),
                                        "date_fin" =>NULL,
                                        "id_societe" => 5391,
                                        "date_arret"=>date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day"))
                                        )
                                    ),
                                    $prolongation,
                                    "La prolongation n'est pas cohérente démarré !");   



    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantLoyerUniqueEnCoursNonDemarre(){
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente démarré !");  
        //Le parc doit donc être actif
        ATF::parc()->q->reset()->addCondition("id_affaire",43);
        $parcAncien=ATF::parc()->sa();
        $this->assertEquals(
                                array(
                                        "id_parc" => $parcAncien[0]["id_parc"],
                                        "id_societe" => 1349,
                                        "id_produit" => 6066,
                                        "id_affaire" => 43,
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "loue",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => "2006-10-09 00:00:00",
                                        "date_inactif" =>NULL,
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>NULL,
                                        "existence" =>"inactif",
                                        'date_achat' => null),
                                    $parcAncien[0],
                                    "L'ancien parc n'est pas cohérent !");  


        
        /////////////////////////////////////////////////////Contrat démarré//////////////////////////////////////////////////////////////
        
        $id_commande=$this->test_updateDateAvenantLoyerUnique();
        
        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        ATF::affaire()->u(array("id_affaire"=>$c->get("id_affaire"),"date_installation_reel"=>NULL));
        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 6 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente démarré !");  

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $a = new affaire_cleodis($affaire["id_affaire"]);
        $ap = $a->getParentAvenant(); // Méthode pour avenant
        $ap2 = new affaire_cleodis(43);

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente démarré !');     
        $this->assertFalse($c->estEnCours(),'Cette commande ne devrait pas etre en cours Avenant démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en etat mis_loyer démarré');      
        $this->assertEquals($ap->getCommande()->get("date_evolution"),$c->get("date_evolution"),"La date d'évolution n'est pas cohérente démarré !");   
        $this->assertEquals($ap->getCommande()->get("date_evolution"),$c->get("date_debut"),"La date début n'est pas cohérente démarré !"); 
        $this->assertEquals($c->get("date_debut"),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente démarré !");    

        //Check Prolongation
        ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $prolongation=ATF::prolongation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_prolongation" => $prolongation[0]["id_prolongation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "0610001AVT1",
                                        "id_refinanceur" => 4,
                                        "date_debut" => date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day")),
                                        "date_fin" =>NULL,
                                        "id_societe" => 5391,
                                        "date_arret" => date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day"))
                                        )
                                    ),
                                    $prolongation,
                                    "La prolongation n'est pas cohérente démarré !");   
        

        //Check facturation
        ATF::facturation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $facturation=ATF::facturation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_facturation" => $facturation[0]["id_facturation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "id_societe" => 5391,
                                        "id_facture" => NULL,
                                        "montant" => "233.00",
                                        "frais_de_gestion" => "1.00",
                                        "assurance" => "2.00",
                                        "date_periode_debut" => $c->get("date_debut"),
                                        "type" => "contrat",
                                        "envoye" => "non",
                                        "date_periode_fin" =>$c->get("date_evolution")
                                        )
                                    ),
                                    $facturation,
                                    "La facturation n'est pas cohérente démarré !");    
        
        ATF::parc()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $parc=ATF::parc()->sa();

        ATF::parc()->q->reset()->addCondition("provenance",43);
        $parcProv=ATF::parc()->sa();

        $this->assertEquals($parcProv,
                                    $parc,
                                    "Probleme sur les parcs avenant loyer unique démarré !");   
        
        $this->assertEquals(array(
                                array(
                                        "id_parc" => $parc[0]["id_parc"],
                                        "id_societe" => 5391,
                                        "id_produit" => 6066,
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "broke",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => $parc[0]["date"],
                                        "date_inactif" =>date("Y-m-d"),
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>43,
                                        "existence" =>"inactif",
                                        'date_achat' => null)
                                    ),
                                    $parc,
                                    "Le nouveau parc n'est pas cohérent démarré !");    

        ATF::parc()->q->reset()->addCondition("id_affaire",43);
        $parcAncien=ATF::parc()->sa();
        $this->assertEquals(
                                array(
                                        "id_parc" => $parcAncien[0]["id_parc"],
                                        "id_societe" => 1349,
                                        "id_produit" => 6066,
                                        "id_affaire" => 43,
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "loue",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => "2006-10-09 00:00:00",
                                        "date_inactif" =>NULL,
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>NULL,
                                        "existence" =>"actif",
                                        'date_achat' => null),
                                    $parcAncien[0],
                                    "L'ancien parc n'est pas cohérent démarré !");  

        //Tester erreur sur modif avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );

        try {
            $this->obj->updateDate($date_debut);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(876,$error,'Erreur on ne doit pas pouvoir modifier les dates d une affaire qui a un avenant non démarré');
    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAvenantLoyerUniqueNonDemarre(){

        $id_commande=$this->test_updateDateAvenantLoyerUnique();
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        /////////////////////////////////////////////////////Contrat non démarré//////////////////////////////////////////////////////////////
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente non démarré 2 !");    


        // Suppression de date début
        $date_debut = array(
            "value" => NULL
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente non démarré !");  
        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $a = new affaire_cleodis($affaire["id_affaire"]);
        $ap = $a->getParentAvenant(); // Méthode pour avenant
        $ap2 = new affaire_cleodis(43);
        //Check Prolongation
        ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $prolongation=ATF::prolongation()->sa();
        $this->assertNull($prolongation,"La prolongation n'est pas cohérente quand elle est ré-initialisée non démarré !"); 

        //Check facturation
        ATF::facturation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $facturation=ATF::facturation()->sa();
        $this->assertNull($facturation," Il ne devrait pas y avoir de facturation quand c'est ré-initialisée non démarré !");   
        
        ATF::parc()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $parc=ATF::parc()->sa();
        
        $this->assertEquals(array(
                                array(
                                        "id_parc" => $parc[0]["id_parc"],
                                        "id_societe" => 5391,
                                        "id_produit" => 6066,
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "broke",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => $parc[0]["date"],
                                        "date_inactif" =>date("Y-m-d"),
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>43,
                                        "existence" =>"inactif",
                                        'date_achat' => null)
                                    ),
                                    $parc,
                                    "Le nouveau parc n'est pas cohérent quand c'est ré-initialisée non démarré !"); 

        ATF::parc()->q->reset()->addCondition("id_affaire",43);
        $parcAncien=ATF::parc()->sa();
        $this->assertEquals(
                                array(
                                        "id_parc" => $parcAncien[0]["id_parc"],
                                        "id_societe" => 1349,
                                        "id_produit" => 6066,
                                        "id_affaire" => 43,
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "loue",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => "2006-10-09 00:00:00",
                                        "date_inactif" =>NULL,
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>NULL,
                                        "existence" =>"actif",
                                        'date_achat' => null),
                                    $parcAncien[0],
                                    "L'ancien parc n'est pas cohérent quand c'est ré-initialisée non démarré !");   


        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-01")
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_debut);
        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals("2009-11-01",$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');     
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer Avenant non démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en etat non_loyer non démarré');      
        $this->assertEquals($ap->getCommande()->get("date_evolution"),$c->get("date_evolution"),"La date d'évolution n'est pas cohérente non démarré !");   
        $this->assertEquals($c->get("date_debut"),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");    

        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente non démarré !");  


        //Check Prolongation
        ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $prolongation=ATF::prolongation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_prolongation" => $prolongation[0]["id_prolongation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "0610001AVT1",
                                        "id_refinanceur" => 4,
                                        "date_debut" => date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day")),
                                        "date_fin" =>NULL,
                                        "id_societe" => 5391,
                                        "date_arret" => date("Y-m-d",strtotime($c->get("date_evolution")." + 1 day"))
                                        )
                                    ),
                                    $prolongation,
                                    "La prolongation n'est pas cohérente non démarré !");   
        

        //Check facturation
        ATF::facturation()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $facturation=ATF::facturation()->sa();
        $this->assertEquals(array(
                                array(
                                        "id_facturation" => $facturation[0]["id_facturation"],
                                        "id_affaire" => $affaire["id_affaire"],
                                        "id_societe" => 5391,
                                        "id_facture" => NULL,
                                        "montant" => "233.00",
                                        "frais_de_gestion" => "1.00",
                                        "assurance" => "2.00",
                                        "date_periode_debut" => $c->get("date_debut"),
                                        "type" => "contrat",
                                        "envoye" => "non",
                                        "date_periode_fin" =>$c->get("date_evolution")
                                        )
                                    ),
                                    $facturation,
                                    "La facturation n'est pas cohérente !");    
        
        ATF::parc()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"]);
        $parc=ATF::parc()->sa();

        ATF::parc()->q->reset()->addCondition("provenance",43);
        $parcProv=ATF::parc()->sa();

        $this->assertEquals($parcProv,
                                    $parc,
                                    "Probleme sur les parcs avenant loyer unique non démarré !");   
        
        $this->assertEquals(array(
                                array(
                                        "id_parc" => $parc[0]["id_parc"],
                                        "id_societe" => 5391,
                                        "id_produit" => 6066,
                                        "id_affaire" => $affaire["id_affaire"],
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "broke",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => $parc[0]["date"],
                                        "date_inactif" =>date("Y-m-d"),
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>43,
                                        "existence" =>"inactif",
                                        'date_achat' => null)
                                    ),
                                    $parc,
                                    "Le nouveau parc n'est pas cohérent non démarré !");    

        ATF::parc()->q->reset()->addCondition("id_affaire",43);
        $parcAncien=ATF::parc()->sa();
        $this->assertEquals(
                                array(
                                        "id_parc" => $parcAncien[0]["id_parc"],
                                        "id_societe" => 1349,
                                        "id_produit" => 6066,
                                        "id_affaire" => 43,
                                        "ref" => "DELLLAT",
                                        "libelle" => "LATITUDE D520",
                                        "divers" => NULL,
                                        "serial" => "BBPZB2J",
                                        "etat" => "loue",
                                        "code" => "23P007161202CDRW#XPP323T153#####|Microsoft Office 2003 Small",
                                        "date" => "2006-10-09 00:00:00",
                                        "date_inactif" =>NULL,
                                        "date_garantie" =>"2009-11-01",
                                        "provenance" =>NULL,
                                        "existence" =>"actif",
                                        'date_achat' => null),
                                    $parcAncien[0],
                                    "L'ancien parc n'est pas cohérent non démarré !");  
    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDate(){

        $devisSerialize = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';

        $devis = unserialize($devisSerialize);
        
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();
        
        $commandeSerialize = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:0:"";s:10:"id_affaire";s:4:"6429";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:9:"39 780.00";s:10:"prix_achat";s:5:"53.00";s:5:"marge";s:5:"99.87";s:13:"marge_absolue";s:9:"39 727.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6356";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:198:"[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"39","loyer__dot__assurance":"10.00","loyer__dot__frais_de_gestion":"10.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":39780}]";s:15:"produits_repris";s:527:"[{"commande_ligne__dot__produit":"LATITUDE D520","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DELLLAT-1","commande_ligne__dot__id_fournisseur":"CBASE","commande_ligne__dot__id_fournisseur_fk":"faf6cbeded2dea2b761ebe7905d9a1ea","commande_ligne__dot__prix_achat":"50.00","commande_ligne__dot__id_produit":"LATITUDE D520","commande_ligne__dot__id_produit_fk":"bf606b897aa6a431fd1bdadda788129d","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__id_commande_ligne":"5f9fb4146f2a2f610b23e492f89aa47a"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"c3fa67e42bb24869e490543b88af9df2","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"8892f086954296d8ec3a1fd4810bc9d2","commande_ligne__dot__id_commande_ligne":"87316a8c4ab84c44e2ef2b3170902366"}]";s:20:"produits_non_visible";s:0:"";}}';
        $commande = unserialize($commandeSerialize);
        $commande["commande"]["id_devis"] = $id_devis;
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        unset($commande["values_commande"]["produits_repris"]);
        unset($commande["values_commande"]["produits_non_visible"]);
        $id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));
        ATF::$msg->getNotices();
        $this->assertNotNull($id_commande,'Commande non créé');
        return $id_commande;
    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateContratDepasseProlongation(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDate();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                            array(
                                    "msg" => "L'état de la commande '7001001' a changé de 'En attente' à 'Prolong.'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                "msg" => "Email envoyé au(x) notifié(s)",
                                "title" => "",
                                "timer" => ""
                                    ),
                            array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé !");    


        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');        
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer non démarré');
        $this->assertEquals($c->get("etat"),"prolongation",'Date debut : le contrat devrait etre en etat non_loyer prolongation');      
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 3 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");   
        

////////////////////////On arrête le contrat///////////////////////////////////////
        
        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 6 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                        "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                        "title" => "",
                                        "timer" => ""
                                        ),
                                array(
                                        "msg" => "Date 'Début' modifiée",
                                        "title" => "",
                                        "timer" => ""
                                        )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé !");    

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en arrete non démarré');
        $this->assertEquals($c->get("etat"),"prolongation",'Date debut : le contrat devrait etre en etat arreter');     
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 3 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");   
        

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateContratDepasse(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDate();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        ATF::devis()->q->reset()->addCondition("id_affaire",$this->obj->select($id_commande,"id_affaire"))->setDimension("row");
        $devis=ATF::devis()->sa();
        ATF::devis()->u(array("id_devis"=>$devis["id_devis"],"type_contrat"=>"lrp"));

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 5 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                            array(
                                    "msg" => "L'état de la commande '7001001' a changé de 'En attente' à 'Prolong.'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                "msg" => "Email envoyé au(x) notifié(s)",
                                "title" => "",
                                "timer" => ""
                                    ),
                            array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 2 !");  


        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals(date("Y-m-d",strtotime($c->get("date_evolution")."-3 month")),$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');        
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer Avenant non démarré');
        $this->assertEquals($c->get("etat"),"prolongation",'Date debut : le contrat devrait etre en etat non_loyer prolongation');      
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 2 month - 2 year - 1 day")),$c->get("date_evolution"),"La date d'évolution n'est pas cohérente non démarré !");    
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." - 5 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  

    }
    
    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateContratEnCours(){
        /////////////////////////////////////////////////////Contrat encours//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDate();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 1 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                            array(
                                    "msg" => "L'état de la commande '7001001' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes contrat en cours !");   


        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente contrat en cours !');       
        $this->assertTrue($c->estEnCours(),'Cette commande devrait etre en mis_loyer Avenant non démarré');
        $this->assertEquals($c->get("etat"),"mis_loyer",'Date debut : le contrat devrait etre en etat contrat en cours');       
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 2 month + 2 year - 1 day")),$c->get("date_evolution"),"La date d'évolution n'est pas cohérente contrat en cours !");   
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")." - 1 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente contrat en cours !"); 

    }
    
    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateContratNonDemarre(){
        /////////////////////////////////////////////////////Contrat non démarré//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDate();

        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente non démarré 2 !");    


        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré !');        
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer Avenant non démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en etat non_loyer non démarré');      
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."+ 3 year + 1 month - 1 day")),date("Y-m-d",strtotime($c->get("date_evolution"))),"La date d'évolution n'est pas cohérente non démarré !");   
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  
    }
    

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateErreur(){
        /////////////////////////////////////////////////////Contrat non démarré//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDate();

        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-01-29")))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        try {
            $this->obj->updateDate($date_debut);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(880,$error,'Mauvaise date 29');

        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-01-30")))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        try {
            $this->obj->updateDate($date_debut);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(880,$error,'Mauvaise date 30');

        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-01-31")))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        try {
            $this->obj->updateDate($date_debut);
        } catch (errorATF $e) {
            $error = $e->getCode();
        }
        $this->assertEquals(880,$error,'Mauvaise date 31');
    }

    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAR(){

        $devisSerialize = 'a:9:{s:9:"extAction";s:5:"devis";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:11:"label_devis";a:5:{s:10:"id_filiale";s:7:"CLEODIS";s:14:"id_opportunite";s:8:"Aucun(e)";s:10:"id_societe";s:7:"FINORPA";s:10:"id_contact";s:16:"M Philippe MOONS";s:10:"AR_societe";s:7:"FINORPA";}s:5:"devis";a:21:{s:10:"id_filiale";s:3:"246";s:5:"devis";s:2:"TU";s:3:"tva";s:5:"1.196";s:11:"date_accord";s:10:"08-02-2011";s:14:"id_opportunite";s:0:"";s:10:"id_societe";i:5391;s:12:"type_contrat";s:3:"lld";s:8:"validite";s:10:"23-02-2011";s:10:"id_contact";s:4:"5753";s:6:"loyers";s:4:"0.00";s:23:"frais_de_gestion_unique";s:4:"0.00";s:16:"assurance_unique";s:4:"0.00";s:10:"AR_societe";s:0:"";s:5:"marge";s:5:"99.96";s:13:"marge_absolue";s:8:"8 021.00";s:4:"prix";s:8:"8 024.00";s:10:"prix_achat";s:4:"3.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:4:"<br>";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:13:"filestoattach";a:1:{s:13:"fichier_joint";s:0:"";}}s:7:"avenant";s:0:"";s:2:"AR";s:0:"";s:5:"loyer";a:1:{s:15:"frequence_loyer";s:1:"m";}s:12:"values_devis";a:2:{s:5:"loyer";s:185:"[{"loyer__dot__loyer":"233","loyer__dot__duree":"34","loyer__dot__assurance":"2","loyer__dot__frais_de_gestion":"1","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":8024}]";s:8:"produits";s:415:"[{"devis_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]";}}';

        $devis = unserialize($devisSerialize);
        
        $devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"LATITUDE D520","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"DELLLAT-1","devis_ligne__dot__prix_achat":"50","devis_ligne__dot__id_produit":"LATITUDE D520","devis_ligne__dot__id_fournisseur":"CBASE","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"BBPZB2J","devis_ligne__dot__id_produit_fk":"6066","devis_ligne__dot__id_parc":"6995","devis_ligne__dot__id_affaire_provenance":"43","devis_ligne__dot__id_fournisseur_fk":"1349"}]';
        $devis["AR"] = "affaire_43";
        $devis["panel_AR-checkbox"] = "on";
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();
        
        $commandeSerialize = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:0:"";s:10:"id_affaire";s:4:"6429";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:9:"39 780.00";s:10:"prix_achat";s:5:"53.00";s:5:"marge";s:5:"99.87";s:13:"marge_absolue";s:9:"39 727.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6356";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:198:"[{"loyer__dot__loyer":"1000.00","loyer__dot__duree":"39","loyer__dot__assurance":"10.00","loyer__dot__frais_de_gestion":"10.00","loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":39780}]";s:15:"produits_repris";s:527:"[{"commande_ligne__dot__produit":"LATITUDE D520","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DELLLAT-1","commande_ligne__dot__id_fournisseur":"CBASE","commande_ligne__dot__id_fournisseur_fk":"faf6cbeded2dea2b761ebe7905d9a1ea","commande_ligne__dot__prix_achat":"50.00","commande_ligne__dot__id_produit":"LATITUDE D520","commande_ligne__dot__id_produit_fk":"bf606b897aa6a431fd1bdadda788129d","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__id_commande_ligne":"5f9fb4146f2a2f610b23e492f89aa47a"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"c3fa67e42bb24869e490543b88af9df2","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"8892f086954296d8ec3a1fd4810bc9d2","commande_ligne__dot__id_commande_ligne":"87316a8c4ab84c44e2ef2b3170902366"}]";s:20:"produits_non_visible";s:0:"";}}';
        $commande = unserialize($commandeSerialize);
        $commande["commande"]["id_devis"] = $id_devis;
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits_repris"] = '[{"commande_ligne__dot__produit":"LATITUDE D520","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"DELLLAT-1","commande_ligne__dot__id_fournisseur":"CBASE","commande_ligne__dot__id_fournisseur_fk":"1349","commande_ligne__dot__prix_achat":"50.00","commande_ligne__dot__id_produit":"LATITUDE D520","commande_ligne__dot__id_produit_fk":"6066","commande_ligne__dot__serial":"BBPZB2J","commande_ligne__dot__id_commande_ligne":"'.$$devis_ligne[1]["id_devis_ligne"].'"}]';
        $id_commande = ATF::commande()->insert($commande,$this->s);
        ATF::$msg->getNotices();
        $this->assertNotNull($id_commande,'Commande non créé');
        return $id_commande;
    }
    
    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateARContratDepasseProlongation(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 year - 4 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente DepasseProlongation 1 !");    


        $id_commande = $this->test_updateDateAR();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");
        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 2 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                            array(
                                    "msg" => "L'état de la commande '7001001' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                    "msg" => "L'état de la commande '0610001' a changé de 'arreter' à 'AR'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                            array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",

                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 2 !");  

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente non démarré ! 1');      
        $this->assertTrue($c->estEnCours(),'Cette commande devrait etre en non_loyer Avenant non démarré');
        $this->assertEquals($c->get("etat"),"mis_loyer",'Date debut : le contrat devrait etre en mis_loyer');       
        $this->assertEquals(ATF::commande()->select(48,"etat"),"AR",'Date debut : le contrat de l affaire AR devrait etre en AR');      
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 2 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");   

////////////////////////On arrête le contrat///////////////////////////////////////
        
        // Date de début sur avenant
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 6 year"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                        "msg" => "L'état de la commande '7001001' a changé de 'En cours' à 'Prolong.'",
                                        "title" => "",
                                        "timer" => ""
                                        ),
                                array(
                                        "msg" => "Email envoyé au(x) notifié(s)",
                                        "title" => "",
                                        "timer" => ""
                                        ),
                                array(
                                        "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                        "title" => "",
                                        "timer" => ""
                                        ),
                                array(
                                        "msg" => "Date 'Début' modifiée",
                                        "title" => "",
                                        "timer" => ""
                                        )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé !");    

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en arreter non démarré');
        $this->assertEquals($c->get("etat"),"prolongation",'Date debut : le contrat devrait etre en arreter');      
        $this->assertEquals(ATF::commande()->select(48,"etat"),"AR",'Date debut : le contrat de l affaire AR devrait etre en AR');      
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."- 2 year")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");   

        /*Plus de date*/
        // Date de début sur avenant
        $date_debut = array(
            "value" => NULL
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->updateDate($date_debut);
        
        $this->assertEquals(array(
                                array(
                                        "msg" => "L'état de la commande '0610001' a changé de 'AR' à 'Prolong.'",
                                        "title" => "",
                                        "timer" => ""
                                        ),
                                array(
                                        "msg" => "L'état de la commande '7001001' a changé de 'Prolong.' à 'En attente'",
                                        "title" => "",
                                        "timer" => ""
                                        ),
                                array(
                                        "msg" => "Date 'Début' modifiée",
                                        "title" => "",
                                        "timer" => ""
                                        )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé !");    

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en arreter non démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en arreter');     
        $this->assertEquals(ATF::commande()->select(48,"etat"),"prolongation",'Date debut : le contrat de l affaire AR devrait etre en prolongation');      
    }

/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateAREnCours(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente en cours !"); 


        $id_commande = $this->test_updateDateAR();

        // Test sans date d'install réeelle
        $this->obj->u(array("id_commande"=>48,"etat"=>"non_loyer"));
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        $date_debut = array(
            "value" => NULL
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande '0610001' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes NonDemarre !"); 

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en arreter non démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en non_loyer');       
        $this->assertEquals(ATF::commande()->select(48,"etat"),"mis_loyer",'Date debut : le contrat de l affaire AR devrait etre en non_loyer');        
        $this->assertEquals(NULL,$c->get("date_evolution"),"La date d'évolution n'est pas cohérente non démarré !");    
        $this->assertEquals(NULL,NULL,"La date_installation_reel n'est pas cohérente non démarré !");   

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateARContratNonDemarre(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente NonDemarre !");   


        $id_commande = $this->test_updateDateAR();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande '0610001' a changé de 'arreter' à 'En attente'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes NonDemarre !"); 

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente NonDemarre !');     
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer NonDemarre');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en non_loyer NonDemarre');        
        $this->assertEquals(ATF::commande()->select(48,"etat"),"non_loyer",'Date debut : le contrat de l affaire AR devrait etre en AR NonDemarre');        
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  

        /*Plus de date*/
        // Date de début sur avenant
        $date_debut = array(
            "value" => NULL
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->updateDate($date_debut);
        
        $this->assertEquals(array(
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé !");    

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en arreter non démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en arreter');     
        $this->assertEquals(ATF::commande()->select(48,"etat"),"non_loyer",'Date debut : le contrat de l affaire AR devrait etre en non_loyer');        
        $this->assertEquals(NULL,$c->get("date_evolution"),"La date d'évolution n'est pas cohérente non démarré !");    
        $this->assertEquals(NULL,NULL,"La date_installation_reel n'est pas cohérente non démarré !");   

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateARContratNonDemarreARDemarre(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente NonDemarre 1 !"); 


        $id_commande = $this->test_updateDateAR();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes NonDemarre 2 !");   

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente NonDemarre !');     
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer NonDemarre');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en non_loyer NonDemarre');        
        $this->assertEquals(ATF::commande()->select(48,"etat"),"arreter",'Date debut : le contrat de l affaire AR devrait etre en arreter NonDemarre');     
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  

        /*Plus de date*/
        $date_debut = array(
            "value" => NULL
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->updateDate($date_debut);
        
        $this->assertEquals(array(
                                array(
                                        "msg" => "L'état de la commande '0610001' a changé de 'arreter' à 'En attente'",
                                        "title" => "",
                                        "timer" => ""
                                    ),
                                array(
                                        "msg" => "Date 'Début' modifiée",
                                        "title" => "",
                                        "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 3 !");  
        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));

        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en arreter non démarré');
        $this->assertEquals($c->get("etat"),"non_loyer",'Date debut : le contrat devrait etre en non_loyer');       
        $this->assertEquals(ATF::commande()->select(48,"etat"),"non_loyer",'Date debut : le contrat de l affaire AR devrait etre en mis_loyer');        
        $this->assertEquals(NULL,$c->get("date_evolution"),"La date d'évolution n'est pas cohérente non démarré !");    
        $this->assertEquals(NULL,NULL,"La date_installation_reel n'est pas cohérente non démarré !");   

    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_updateDateARContratNonDemarreARDepasse(){
        /////////////////////////////////////////////////////Contrat dépassé//////////////////////////////////////////////////////////////
        $this->obj->u(array("id_commande"=>48,"etat"=>"arreter"));
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 6 year"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente NonDemarre !");   


        $id_commande = $this->test_updateDateAR();

        // Test sans date d'install réeelle
        $c = new commande_cleodis($id_commande);    
        $c->getAffaire()->set("date_installation_reel","");

        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month"))
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );

        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande '0610001' a changé de 'arreter' à 'Prolong.'",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes NonDemarre !"); 

        $c = new commande_cleodis($id_commande);    
        $affaire=ATF::affaire()->select($this->obj->select($id_commande,"id_affaire"));
        $this->assertEquals($c->get("date_evolution"),$affaire["date_garantie"],'date_garantie  n est pas cohérente NonDemarre !');     
        $this->assertFalse($c->estEnCours(),'Cette commande devrait etre en non_loyer NonDemarre');
        $this->assertEquals($c->get("etat"),"non_loyer",'etat : le contrat devrait etre en non_loyer NonDemarre');      
        $this->assertEquals(ATF::commande()->select(48,"etat"),"prolongation",'etat : le contrat de l affaire AR devrait etre en mis_loyer NonDemarre');        
        $this->assertEquals(date("Y-m-d",strtotime(date("Y-m-01")."+ 3 month")),$affaire["date_installation_reel"],"La date_installation_reel n'est pas cohérente non démarré !");  

    }
    
///*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */   
//  public function test_updateDateARError(){
//
//      $id_commande = $this->test_updateDateAR();
//
//      //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
//      $date_debut = array(
//          "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 month"))
//          ,"key" => "date_debut"
//          ,"id_commande" => 48
//      );
//
//      try {
//          $this->obj->updateDate($date_debut);
//      } catch (errorATF $e) {
//          $error = $e->getCode();
//
//      }
//      $this->assertEquals(877,$error,'Erreur modification date commande AR non attrapée');
//
//  }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_getCommande_ligne(){
        $devis = unserialize(self::$devis);
        $devis["values_devis"]["produits"]= '[{"devis_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
        $devis["values_devis"]["produits_non_visible"]='[{"devis_ligne__dot__produit":"ZywallInvis 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne=ATF::devis_ligne()->sa();

        $commande = unserialize(self::$commande);
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits_non_visible"]='[{"commande_ligne__dot__produit":"ZywallInvis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"}]';

        $commande["commande"]["id_devis"]=$id_devis;
        $commande["commande"]["date"]=date("Y-m-01");
        $refresh = array();

        $id_commande = $this->obj->insert($commande,$this->s,NULL,$refresh);
        $commande_select = $this->obj->select($id_commande);
        
        $infos["id_commande"]=$id_commande;

        $this->assertFalse($this->obj->getCommande_ligne($infos),'getcommande_ligne ne doit rien renvoyer si il n y a pas un id_fourniseur ET un id_commande');

        $infos["id_fournisseur"]=1583;
        $this->obj->getCommande_ligne($infos);  
        
        $this->assertTrue($infos["display"],'getcommande_ligne ne renvoie pas le bon display');
        $this->assertEquals(1583,$infos["id_fournisseur"],'getcommande_ligne ne renvoie pas le bon display');
        $this->assertEquals($id_commande,$infos["id_commande"],'getcommande_ligne ne renvoie pas le bon display');
    }
    
/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */ 
    public function test_getLignes(){
        $devis = unserialize(self::$devis);
        $devis["preview"]=true;
        $devis["values_devis"]["produits_repris"] = '[{"devis_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__quantite":1,"devis_ligne__dot__type":"sans_objet","devis_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","devis_ligne__dot__prix_achat":"100","devis_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","devis_ligne__dot__id_fournisseur":"DELL","devis_ligne__dot__visibilite_prix":"invisible","devis_ligne__dot__serial":"5X7ZB2J","devis_ligne__dot__id_produit_fk":"5893","devis_ligne__dot__id_parc":"17","devis_ligne__dot__id_affaire_provenance":"26","devis_ligne__dot__id_fournisseur_fk":"1351"}]';
        $devis["devis"]["type_contrat"] = "vente";
        $devis["panel_vente-checkbox"] = "on";
        $devis["devis"]["prix_vente"]=3000;
        $devis["vente"] = "affaire_26";
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));
        ATF::devis_ligne()->q->reset()->addCondition("id_devis",$id_devis);
        $devis_ligne = ATF::devis_ligne()->sa();

        $commande = 'a:5:{s:9:"extAction";s:8:"commande";s:9:"extMethod";s:6:"insert";s:7:"preview";s:4:"true";s:8:"commande";a:15:{s:8:"commande";s:2:"TU";s:4:"type";s:11:"prelevement";s:10:"id_societe";s:4:"5391";s:4:"date";s:10:"11-05-2011";s:10:"id_affaire";s:4:"6210";s:17:"clause_logicielle";s:3:"non";s:4:"prix";s:8:"3 000.00";s:10:"prix_achat";s:6:"103.00";s:5:"marge";s:5:"96.57";s:13:"marge_absolue";s:8:"2 897.00";s:5:"email";s:17:"pmoons@finorpa.fr";s:10:"emailTexte";s:0:"";s:10:"emailCopie";s:24:"jerome.loison@cleodis.fr";s:8:"id_devis";s:4:"6137";s:10:"__redirect";s:5:"devis";}s:15:"values_commande";a:4:{s:5:"loyer";s:190:"[{"loyer__dot__loyer":"3000.00","loyer__dot__duree":"1","loyer__dot__assurance":null,"loyer__dot__frais_de_gestion":null,"loyer__dot__frequence_loyer":"mois","loyer__dot__loyer_total":3000}]";s:15:"produits_repris";s:572:"[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"e397ce41979133ecb54f810af7de6f25","commande_ligne__dot__prix_achat":"100.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__id_produit_fk":"22bc1812bc68989afcde8962b81b0882","commande_ligne__dot__serial":"5X7ZB2J","commande_ligne__dot__id_commande_ligne":"f010c6c164d0c6ea083a7fdedc4cea27"}]";s:8:"produits";s:533:"[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"2af4569e9740cafd20b61f5bc957797b","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"f23a58cfc5353c62f5e261b25d791ea6","commande_ligne__dot__id_commande_ligne":"202dae48031c294619bd62aa26bd6936"}]";s:20:"produits_non_visible";s:0:"";}}';
        $commande = unserialize($commande);
        
        
        $commande["values_commande"]["produits_repris"] = '[{"commande_ligne__dot__produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__quantite":"1","commande_ligne__dot__ref":"OptiGX520 17 DVD 48X-1","commande_ligne__dot__id_fournisseur":"DELL","commande_ligne__dot__id_fournisseur_fk":"1351","commande_ligne__dot__prix_achat":"100.00","commande_ligne__dot__id_produit":"Optiplex GX520 TFT 17 DVD 48X","commande_ligne__dot__id_produit_fk":"5893","commande_ligne__dot__serial":"5X7ZB2J","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[1]["id_devis_ligne"].'"}]';
        $commande["values_commande"]["produits"] = '[{"commande_ligne__dot__produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"Zywall 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $commande["commande"]["date"]=date("Y-m-01");
        
        $id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));
        ATF::$msg->getNotices();
        $commande_select = $this->obj->select($id_commande);
        
        ATF::commande_ligne()->q->reset()->addCondition("id_commande",$id_commande);
        $commande_ligne=ATF::commande_ligne()->sa();
        
        $affaire = new affaire_cleodis($commande_select["id_affaire"]);
        $commande = $affaire->getCommande();
        
        // Lignes
        if ($commande) {
            // Si on a une commande, on utilise les lignes du contrat
            $lignesDataVisibles = $commande->getLignes("visible");
            $lignesDataNonVisibles = $commande->getLignes("invisible");
            $lignesDataReprises = $commande->getLignes("reprise");
        }

        $this->assertEquals(array("ref"=>"ZYX-FW","id_fournisseur"=>"DJP SERVICE INFORMATIQUE","quantite"=>3,"prix_achat"=>"1.00","id_fournisseur_fk"=>1583,"id_commande_ligne"=>$commande_ligne[0]["id_commande_ligne"]),$lignesDataVisibles[0],'lignesDataVisibles ne renvoie pas les bonnes lignes');
        $this->assertEquals(array("ref"=>"OptiGX520 17 DVD 48X-1","id_fournisseur"=>"DELL","quantite"=>1,"prix_achat"=>"100.00","id_fournisseur_fk"=>1351,"id_commande_ligne"=>$commande_ligne[1]["id_commande_ligne"]),$lignesDataReprises[0],'lignesDataReprises ne renvoie pas les bonnes lignes');
        $this->assertNull($lignesDataNonVisibles,'lignesDataNonVisibles ne renvoie pas les bonnes lignes');

    }   


    //  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all_checkEnvoiContratEtBilanExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"envoiContratEtBilan");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['envoiContratEtBilanExists'],"Erreur, le fichier envoiContratEtBilan n'est pas reconnu comme présent");
        
        unlink($f);
    }

    //  @author Morgan FLEURQUIN <mfleurquin@absystech.fr>  
    public function test_select_all_checkEnvoiCourrierClassiqueExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"envoiCourrierClassique");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['envoiCourrierClassiqueExists'],"Erreur, le fichier envoiCourrierClassique n'est pas reconnu comme présent");
        
        unlink($f);
    }
    
    //  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all_checkenvoiContratSsBilanExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"envoiContratSsBilan");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['envoiContratSsBilanExists'],"Erreur, le fichier envoiContratSsBilanExists n'est pas reconnu comme présent");
        
        unlink($f);
    }
    
    //  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all_checkenvoiAvenantExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"envoiAvenant");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['envoiAvenantExists'],"Erreur, le fichier envoiAvenant n'est pas reconnu comme présent");
        
        unlink($f);
    }
    
        //  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all_checkcontratTransfertExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"contratTransfert");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['contratTransfertExists'],"Erreur, le fichier contratTransfert n'est pas reconnu comme présent");
        
        unlink($f);
    }
    
    //  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all_checkEnvoictSigneExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"ctSigne");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['ctSigneExists'],"Erreur, le fichier ctSigne n'est pas reconnu comme présent");
        
        unlink($f);
    }


    //  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all_checkCourrierRestitutionExists() {
        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        
        $f = $this->obj->filepath($id_commande,"CourrierRestitution");

        file_put_contents($f,"toto");
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $r = $r['data'];

        $this->assertTrue($r[0]['CourrierRestitutionExists'],"Erreur, le fichier CourrierRestitution n'est pas reconnu comme présent");
        
        unlink($f);
    }
    
//  @author Quentin JANON <qjanon@absystech.fr>  
    public function test_select_all() {

        $id_affaire=ATF::affaire()->decryptId(ATF::affaire()->i(array("ref"=>"refTu","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu")));
        
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        $id_commande_ligne1=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS","produit"=>"Ordinateur TOM TOM GO500","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
        $id_commande_ligne2=ATF::commande_ligne()->i(array("id_commande"=>$id_commande,"id_produit"=>6,"ref"=>"GPAC-GPS1","produit"=>"Ordinateur TOM TOM GO5001","quantite"=>2,"id_fournisseur"=>5385,"prix_achat"=>"0","visible"=>"oui"));
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();

        //Normal
            $this->assertEquals(array(
                                        "commande.id_affaire"=>"refTu",
                                        "commande.date_debut"=>NULL,
                                        "commande.date_evolution"=>NULL,
                                        "commande.retour_contrat"=>NULL,
                                        "commande.retour_prel"=>NULL,
                                        "commande.retour_pv"=>NULL,
                                        "commande.date_demande_resiliation"=>NULL,
                                        "commande.date_prevision_restitution"=>NULL,
                                        "commande.date_restitution_effective"=>NULL,
                                        "commande.id_affaire_fk"=>$id_affaire,
                                        "commande.id_commande"=>$id_commande,
                                        "vente"=>false,
                                        "prolongationAllow"=>false,
                                        "bdcExist"=>false,
                                        "demandeRefiExist"=>false,
                                        "factureAllow"=>false,
                                        "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                        //,'ctSigneExists' => true
                                        )
                                        ,$r["data"][0]
                                        ,'select_all pb 1');        


        //Vente
        ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"vente"));
            $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
            $r = $this->obj->select_all();
            $this->assertEquals(array(
                                        "commande.id_affaire"=>"refTu",
                                        "commande.date_debut"=>NULL,
                                        "commande.date_evolution"=>NULL,
                                        "commande.retour_contrat"=>NULL,
                                        "commande.retour_prel"=>NULL,
                                        "commande.retour_pv"=>NULL,
                                        "commande.date_demande_resiliation"=>NULL,
                                        "commande.date_prevision_restitution"=>NULL,
                                        "commande.date_restitution_effective"=>NULL,
                                        "commande.id_affaire_fk"=>$id_affaire,
                                        "commande.id_commande"=>$id_commande,
                                        "vente"=>true,
                                        "prolongationAllow"=>false,
                                        "bdcExist"=>false,
                                        "demandeRefiExist"=>true,
                                        "factureAllow"=>false,
                                        "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                        )
                                        ,$r["data"][0]
                                        ,'select_all pb Vente');        
            ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));

        //Prolongation
        ATF::commande()->u(array("id_commande"=>$id_commande,"date_evolution"=>date("Y-m-d")));
            $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
            $r = $this->obj->select_all();
            $this->assertEquals(array(
                                        "commande.id_affaire"=>"refTu",
                                        "commande.date_debut"=>NULL,
                                        "commande.date_evolution"=>date("Y-m-d"),
                                        "commande.retour_contrat"=>NULL,
                                        "commande.retour_prel"=>NULL,
                                        "commande.retour_pv"=>NULL,
                                        "commande.date_demande_resiliation"=>NULL,
                                        "commande.date_prevision_restitution"=>NULL,
                                        "commande.date_restitution_effective"=>NULL,
                                        "commande.id_affaire_fk"=>$id_affaire,
                                        "commande.id_commande"=>$id_commande,
                                        "vente"=>false,
                                        "prolongationAllow"=>true,
                                        "bdcExist"=>false,
                                        "demandeRefiExist"=>false,
                                        "factureAllow"=>false,
                                        "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                        )
                                        ,$r["data"][0]
                                        ,'select_all pb prolongationAllow');        


            //Une vente ne doit pas avoir de prolongation
            ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"vente"));
            $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
            $r = $this->obj->select_all();
            $this->assertEquals(array(
                                        "commande.id_affaire"=>"refTu",
                                        "commande.date_debut"=>NULL,
                                        "commande.date_evolution"=>date("Y-m-d"),
                                        "commande.retour_contrat"=>NULL,
                                        "commande.retour_prel"=>NULL,
                                        "commande.retour_pv"=>NULL,
                                        "commande.date_demande_resiliation"=>NULL,
                                        "commande.date_prevision_restitution"=>NULL,
                                        "commande.date_restitution_effective"=>NULL,
                                        "commande.id_affaire_fk"=>$id_affaire,
                                        "commande.id_commande"=>$id_commande,
                                        "vente"=>true,
                                        "prolongationAllow"=>false,
                                        "bdcExist"=>false,
                                        "demandeRefiExist"=>true,
                                        "factureAllow"=>false,
                                        "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                        )
                                        ,$r["data"][0]
                                        ,'select_all pb prolongationAllow vente');      
        
            ATF::affaire()->u(array("id_affaire"=>$id_affaire,"nature"=>"affaire"));

        ATF::commande()->u(array("id_commande"=>$id_commande,"date_evolution"=>NULL));


        //BDC
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"ref" => "aaa"
                                ,"id_commande" => $id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $id_affaire
                                ,"bon_de_commande" => "AffaireTu"
                                ,"tva" =>"1.196"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_user" => $this->id_user
        );
        
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->i($bon_de_commande));
        
        $bon_de_commande_ligne1=array(
                                        "id_bon_de_commande"=>$id_bon_de_commande,
                                        "produit"=>"produit a",
                                        "id_commande_ligne"=>$id_commande_ligne1,
                                        "quantite"=>1
                                    );
        
        $id_bon_de_commande_ligne1=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne1);

        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $this->assertEquals(array(
                                    "commande.id_affaire"=>"refTu",
                                    "commande.date_debut"=>NULL,
                                    "commande.date_evolution"=>NULL,
                                    "commande.retour_contrat"=>NULL,
                                    "commande.retour_prel"=>NULL,
                                    "commande.retour_pv"=>NULL,
                                    "commande.date_demande_resiliation"=>NULL,
                                    "commande.date_prevision_restitution"=>NULL,
                                    "commande.date_restitution_effective"=>NULL,
                                    "commande.id_affaire_fk"=>$id_affaire,
                                    "commande.id_commande"=>$id_commande,
                                    "vente"=>false,
                                    "prolongationAllow"=>false,
                                    "bdcExist"=>false,
                                    "demandeRefiExist"=>false,
                                    "factureAllow"=>false,
                                    "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                    )
                                    ,$r["data"][0]
                                    ,'select_all pb prolongationAllow bdc 1');      

        $bon_de_commande_ligne2=array(
                                        "id_bon_de_commande"=>$id_bon_de_commande,
                                        "produit"=>"produit b",
                                        "id_commande_ligne"=>$id_commande_ligne2,
                                        "quantite"=>1
                                    );
        
        $id_bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne2);
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $this->assertEquals(array(
                                    "commande.id_affaire"=>"refTu",
                                    "commande.date_debut"=>NULL,
                                    "commande.date_evolution"=>NULL,
                                    "commande.retour_contrat"=>NULL,
                                    "commande.retour_prel"=>NULL,
                                    "commande.retour_pv"=>NULL,
                                    "commande.date_demande_resiliation"=>NULL,
                                    "commande.date_prevision_restitution"=>NULL,
                                    "commande.date_restitution_effective"=>NULL,
                                    "commande.id_affaire_fk"=>$id_affaire,
                                    "commande.id_commande"=>$id_commande,
                                    "vente"=>false,
                                    "prolongationAllow"=>false,
                                    "bdcExist"=>true,
                                    "demandeRefiExist"=>false,
                                    "factureAllow"=>false,
                                    "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                    )
                                    ,$r["data"][0]
                                    ,'select_all pb prolongationAllow bdc 2');      

        ATF::bon_de_commande()->d($id_bon_de_commande);

        
        //Demande refi
        $id_demande_refi=ATF::demande_refi()->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description","etat"=>"valide"));

        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $this->assertEquals(array(
                                    "commande.id_affaire"=>"refTu",
                                    "commande.date_debut"=>NULL,
                                    "commande.date_evolution"=>NULL,
                                    "commande.retour_contrat"=>NULL,
                                    "commande.retour_prel"=>NULL,
                                    "commande.retour_pv"=>NULL,
                                    "commande.date_demande_resiliation"=>NULL,
                                    "commande.date_prevision_restitution"=>NULL,
                                    "commande.date_restitution_effective"=>NULL,
                                    "commande.id_affaire_fk"=>$id_affaire,
                                    "commande.id_commande"=>$id_commande,
                                    "vente"=>false,
                                    "prolongationAllow"=>false,
                                    "bdcExist"=>false,
                                    "demandeRefiExist"=>true,
                                    "factureAllow"=>false,
                                    "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                    )
                                    ,$r["data"][0]
                                    ,'select_all pb Demande refi');         

        ATF::demande_refi()->u(array("id_demande_refi"=>$id_demande_refi,"etat"=>"accepte"));
        ATF::demande_refi()->d($id_demande_refi);



        //facture
        ATF::commande()->u(array("id_commande"=>$id_commande,"date_debut"=>date("Y-m-d")));
        $id_demande_refi=ATF::demande_refi()->i(array("date"=>date("Y-m-d"),"id_contact"=>$this->id_contact,"id_refinanceur"=>1,"id_affaire"=>$id_affaire,"id_societe"=>$this->id_societe,"description"=>"Tu description","etat"=>"valide"));
        $bon_de_commande["bon_de_commande"]=array(
                                 "id_societe" => $this->id_societe
                                ,"ref" => "aaa"
                                ,"id_commande" => $id_commande
                                ,"id_fournisseur" => 1351
                                ,"id_affaire" => $id_affaire
                                ,"bon_de_commande" => "AffaireTu"
                                ,"tva" =>"1.196"
                                ,"date" => date("Y-m-d")
                                ,"destinataire" => "AXXES"
                                ,"adresse" => "26 rue de La Vilette - Part Dieu"
                                ,"cp" => "69003"
                                ,"ville" => "LYON"
                                ,"id_user" => $this->id_user
        );
        $id_bon_de_commande=classes::decryptId(ATF::bon_de_commande()->i($bon_de_commande));
        $bon_de_commande_ligne1=array(
                                        "id_bon_de_commande"=>$id_bon_de_commande,
                                        "produit"=>"produit a",
                                        "id_commande_ligne"=>$id_commande_ligne1,
                                        "quantite"=>1
                                    );
        
        $id_bon_de_commande_ligne1=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne1);
        $bon_de_commande_ligne2=array(
                                        "id_bon_de_commande"=>$id_bon_de_commande,
                                        "produit"=>"produit b",
                                        "id_commande_ligne"=>$id_commande_ligne2,
                                        "quantite"=>1
                                    );
        
        $id_bon_de_commande_ligne2=ATF::bon_de_commande_ligne()->i($bon_de_commande_ligne2);            
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande)->setCount();
        $r = $this->obj->select_all();
        $this->assertEquals(array(
                                    "commande.id_affaire"=>"refTu",
                                    "commande.date_debut"=>date("Y-m-d"),
                                    "commande.date_evolution"=>NULL,
                                    "commande.retour_contrat"=>NULL,
                                    "commande.retour_prel"=>NULL,
                                    "commande.retour_pv"=>NULL,
                                    "commande.date_demande_resiliation"=>NULL,
                                    "commande.date_prevision_restitution"=>NULL,
                                    "commande.date_restitution_effective"=>NULL,
                                    "commande.id_affaire_fk"=>$id_affaire,
                                    "commande.id_commande"=>$id_commande,
                                    "vente"=>false,
                                    "prolongationAllow"=>false,
                                    "bdcExist"=>true,
                                    "demandeRefiExist"=>true,
                                    "factureAllow"=>true,
                                    "id_affaireCrypt"=>ATF::affaire()->cryptId($id_affaire)
                                    )
                                    ,$r["data"][0]
                                    ,'select_all pb Facture');      
        

    }

    public function test_cleodisStatut() {
        $this->obj = ATF::commande();
        
        //Devis
        $devis = unserialize(self::$devis);
        $devis["values_devis"]["produits"]= '[{"devis_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","devis_ligne__dot__quantite":"3","devis_ligne__dot__type":"fixe","devis_ligne__dot__ref":"ZYX-FW","devis_ligne__dot__prix_achat":"1","devis_ligne__dot__id_produit":"","devis_ligne__dot__id_fournisseur":"<span class=\"searchSelectionFound\">D</span>JP SERVICE","devis_ligne__dot__id_produit_fk":"1175","devis_ligne__dot__id_fournisseur_fk":"1583"}]';
        $devis["devis"]["id_contact"]=  4753;
        $id_devis = classes::decryptId(ATF::devis()->insert($devis,$this->s));

        //Commande
        $commande = unserialize(self::$commande);
        $commande["values_commande"]["produits"]='[{"commande_ligne__dot__produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__quantite":"3","commande_ligne__dot__ref":"ZYX-FW","commande_ligne__dot__id_fournisseur":"DJP SERVICE","commande_ligne__dot__id_fournisseur_fk":"1583","commande_ligne__dot__prix_achat":"1.00","commande_ligne__dot__id_produit":"ZywallVis 5 - dispositif de sécurité","commande_ligne__dot__id_produit_fk":"1175","commande_ligne__dot__id_commande_ligne":"'.$devis_ligne[0]["id_devis_ligne"].'"}]';
        $commande["commande"]["id_devis"]=$id_devis;
        $id_commande = classes::decryptId(ATF::commande()->insert($commande,$this->s));   
        ATF::$msg->getNotices();  

        ATF::affaire()->u(array("id_affaire"=>$this->obj->select($id_commande,"id_affaire"),"id_parent"=>43));
        
        // Date de début
        $date_debut = array(
            "value" => date("Y-m-01")
            ,"key" => "date_debut"
            ,"id_commande" => $id_commande
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande '7001001' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes dépassé 1 !");  

        $c = new commande_cleodis($id_commande);
        $this->assertEquals($c->get("etat"),"mis_loyer",'Date debut : le contrat devrait etre en etat mis_loyer');      

        $this->obj->u(array("id_commande"=>$id_commande,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month"))));

        $this->obj->q->reset()->addCondition("id_commande",$id_commande);
        $this->obj->cleodisStatut();
        
        $c = new commande_cleodis($id_commande);
        $this->assertEquals($c->get("etat"),"non_loyer",'cleodisStatut : le contrat devrait etre en etat non_loyer 1');         
        
        $this->assertEquals(array(
                        array(
                            "msg" => "L'état de la commande '7001001' a changé de 'En cours' à 'En attente'",
                            "title" => "",
                            "timer" => ""
                            )
                    ),
                    ATF::$msg->getNotices(),
                    "Les notices ne sont pas cohérentes dépassé 1 !");  
                    
                    
                    
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_debut = array(
            "value" => date("Y-m-d",strtotime(date("Y-m-01")."- 3 year - 4 month"))
            ,"key" => "date_debut"
            ,"id_commande" => 48
        );
        $this->obj->updateDate($date_debut);
        $this->assertEquals(array(
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Un e-mail de suivi a été envoyé au responsable",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "Date 'Début' modifiée",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente DepasseProlongation !");  


        $id_commande2 = classes::decryptId($this->test_updateDateAR());

        $this->obj->u(array("id_commande"=>$id_commande2,"date_debut"=>date("Y-m-d",strtotime(date("Y-m-01")." + 1 month"))));
        
        $this->obj->q->reset()->addCondition("id_commande",$id_commande2);
        $this->obj->cleodisStatut();
        
        $c = new commande_cleodis($id_commande2);
        $this->assertEquals($c->get("etat"),"non_loyer",'cleodisStatut : le contrat devrait etre en etat non_loyer 2');         

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testStopCommande(){
        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande");
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire);
        $commande["id_commande"]=ATF::commande()->i($commande);
        $this->obj->stopCommande($commande);
        $selectCommande =$this->obj->select($commande["id_commande"]);
        $this->assertEquals("arreter",$this->obj->select($commande["id_commande"],"etat"),'Problème sur StopCommande');         

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'affaire' a changé de 'En attente' à 'arreter'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "L'état de l'affaire 'affaire' a changé de 'Devis' à 'Terminee'",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => null,
                                    "timer" => null
                                        )

                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente StopCommande !"); 

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testStopCommandeAR(){
        $id_affaire1=ATF::affaire()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu1","nature"=>"AR"));
        $commande1=array("ref"=>"refTu1","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire1,"etat"=>"non_loyer");
        $commande1["id_commande"]=ATF::commande()->i($commande1);


        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande","id_fille"=>$id_affaire1);
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire);
        $commande["id_commande"]=ATF::commande()->i($commande);

        $this->obj->stopCommande($commande1);
        $selectCommande =$this->obj->select($commande1["id_commande"]);
        $this->assertEquals("arreter",$this->obj->select($commande1["id_commande"],"etat"),'Problème sur StopCommande AR');         

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'refTu1' a changé de 'En attente' à 'arreter'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "L'état de l'affaire 'refTu1' a changé de 'Devis' à 'Terminee'",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => null,
                                    "timer" => null
                                        )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente StopCommande AR !");  

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testStopCommandeAvt(){
        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande");
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire);
        $commande["id_commande"]=ATF::commande()->i($commande);


        $avenant=array("ref"=>"avenant","id_societe"=>$this->id_societe,"affaire"=>"avenant","nature"=>"avenant","id_parent"=>$id_affaire);
        $id_avenant=ATF::affaire()->i($avenant);
        
        $commande1=array("ref"=>"avenant","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_avenant,"date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d"));
        $commande1["id_commande"]=ATF::commande()->i($commande1);

        $this->obj->stopCommande($commande1);
        $this->assertEquals("arreter",$this->obj->select($commande1["id_commande"],"etat"),'Problème sur StopCommande AVT');        

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'avenant' a changé de 'En attente' à 'arreter'",
                                    "title" => "",
                                    "timer" => ""
                                    ),
                                array(
                                    "msg" => "L'état de l'affaire 'avenant' a changé de 'Devis' à 'Terminee'",
                                    "title" => "",
                                    "timer" => ""
                                        ),
                                array(
                                    "msg" => "Email envoyé au(x) notifié(s)",
                                    "title" => null,
                                    "timer" => null
                                        )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente StopCommande AVT !"); 

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testReactiveCommande(){
        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande");
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire,"etat"=>"arreter","date_arret"=>date("Y-m-d"));
        $commande["id_commande"]=ATF::commande()->i($commande);
        $this->obj->reactiveCommande($commande);
        $this->assertEquals("non_loyer",$this->obj->select($commande["id_commande"],"etat"),'Problème sur testReactiveCommande etat');      
        $this->assertFalse($this->obj->select($commande1["id_commande"],"date_arret"),'Problème sur testReactiveCommande date_arret');      

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'affaire' a changé de 'arreter' à 'En attente'",
                                    "title" => "",
                                    "timer" => ""
                                        )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente reactiveCommande !"); 

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testReactiveCommandeAR(){
        $id_affaire1=ATF::affaire()->i(array("ref"=>"refTu1","id_societe"=>$this->id_societe,"affaire"=>"AffaireTu1","nature"=>"AR"));
        $commande1=array("ref"=>"refTu1","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire1,"etat"=>"arreter","date_arret"=>date("Y-m-d"));
        $commande1["id_commande"]=ATF::commande()->i($commande1);

        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande","id_fille"=>$id_affaire1);
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire);
        $commande["id_commande"]=ATF::commande()->i($commande);

        $this->obj->reactiveCommande($commande1);
        $this->assertEquals("non_loyer",$this->obj->select($commande1["id_commande"],"etat"),'Problème sur testReactiveCommandeAR etat');       
        $this->assertNull($this->obj->select($commande1["id_commande"],"date_arret"),'Problème sur testReactiveCommandeAR date_arret');         

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'refTu1' a changé de 'arreter' à 'En attente'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente testReactiveCommandeAR !");   

    }

    /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
    public function testReactiveCommandeAvt(){
        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande");
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire);
        $commande["id_commande"]=ATF::commande()->i($commande);


        $avenant=array("ref"=>"avenant","id_societe"=>$this->id_societe,"affaire"=>"avenant","nature"=>"avenant","id_parent"=>$id_affaire);
        $id_avenant=ATF::affaire()->i($avenant);
        
        $commande1=array("ref"=>"avenant","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_avenant,"etat"=>"non_loyer","date_debut"=>date("Y-m-d"),"date_evolution"=>date("Y-m-d"));
        $commande1["id_commande"]=ATF::commande()->i($commande1);

        $this->obj->reactiveCommande($commande1);
        $this->assertEquals("mis_loyer",$this->obj->select($commande1["id_commande"],"etat"),'Problème sur testReactiveCommandeAvt AVT non_loyer');         
        $this->assertNull($this->obj->select($commande1["id_commande"],"date_arret"),'Problème sur testReactiveCommandeAvt AVT date_arret');        

        $this->assertEquals(array(
                                array(
                                    "msg" => "L'état de la commande 'avenant' a changé de 'En attente' à 'En cours'",
                                    "title" => "",
                                    "timer" => ""
                                    )
                            ),
                            ATF::$msg->getNotices(),
                            "Les notices ne sont pas cohérentes pas cohérente testReactiveCommandeAvt!");   

        ATF::$msg->getNotices();
    }
    
//  /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
//  public function test_updateDateResiliation(){
//      $id_affaire=$this->insertAffaire();
//      $id_commande=$this->insertCommande($id_affaire,$id_devis);
//      $infos["id_commande"]=$id_commande;
//      $infos["key"]="date_resiliation";
//      $infos["value"]=date("Y-m-d");
//      
//      $this->obj->updateDateResiliation($infos);
//      
//      $this->assertEquals(date("Y-m-d"),
//                          $this->obj->select($id_commande,"date_resiliation"),
//                          "La date de résiliation ne se met pas à jour"); 
//      
//      $this->obj->u(array("id_commande"=>$id_commande,"date_restitution"=>date("Y-m-d")));    
//      $infos["value"]="undefined";
//      
//      try {
//          $this->obj->updateDateResiliation($infos);
//      } catch (errorATF $e) {
//          $error = $e->getCode();
//      }
//      $this->assertEquals(881,$error,"Impossible de supprimer la date de résiliation si la date de restitution est renseignée");
//      ATF::$msg->getNotices();
//  }
//  
//  /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
//  public function test_updateDateRestitution(){
//      $id_affaire=$this->insertAffaire();
//      $id_commande=$this->insertCommande($id_affaire,$id_devis);
//      $infos["id_commande"]=$id_commande;
//      $infos["key"]="date_restitution";
//      $infos["value"]=date("Y-m-d");
//      
//      try {
//          $this->obj->updateDateRestitution($infos);
//      } catch (errorATF $e) {
//          $error = $e->getCode();
//      }
//      $this->assertEquals(882,$error,"Il faut une date de resiliation pour pouvoir renseigner la date de restitution");
//
//      $this->obj->u(array("id_commande"=>$id_commande,"date_resiliation"=>date("Y-m-d")));    
//
//      $this->obj->updateDateRestitution($infos);
//      
//      $this->assertEquals(date("Y-m-d"),
//                          $this->obj->select($id_commande,"date_restitution"),
//                          "La date de date_restitution ne se met pas à jour");    
//      
//      $this->obj->u(array("id_commande"=>$id_commande,"date_restitution_effective"=>date("Y-m-d")));  
//      $infos["value"]="undefined";
//      
//      try {
//          $this->obj->updateDateRestitution($infos);
//      } catch (errorATF $e) {
//          $error = $e->getCode();
//      }
//      $this->assertEquals(883,$error,"Impossible de supprimer la date de résiliation si la restitution est effective");
//      ATF::$msg->getNotices();
//  }
//
//  /*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>  */
//  public function test_updateDateRestitution_effective(){
//      $id_affaire=$this->insertAffaire();
//      $id_commande=$this->insertCommande($id_affaire,$id_devis);
//      $infos["id_commande"]=$id_commande;
//      $infos["key"]="date_restitution_effective";
//      $infos["value"]=date("Y-m-d");
//      
//      $this->obj->updateDateRestitution_effective($infos);
//      ATF::$msg->getNotices();
//  }
/*  
    public function test_updateDateAvenant2(){
        /////////////////////////////////////////////////////Contrat encours//////////////////////////////////////////////////////////////
        $id_commande = $this->test_updateDateAvenant();
        //Une fois que l'avenant n'a plus de date, on peut inialiser les dates de l'affaires parentes
        $date_restitution_prevision = array(
            "date" => date("Y-m-d",strtotime(date("Y-m-d")."- 1 day"))
            ,"field" => "date_prevision_restitution"
            ,"id_commande" => $id_commande
        );
        
        $this->obj->checkAndUpdateDates($date_restitution_prevision);
        $this->assertEquals("restitution", ATF::commande()->select($id_commande , "etat"), "Damned !");
    
        //On ajoute une facture refusé pour mettre le contrat en contentieux !
        $fact = array("type_facture"=> "libre" , "ref"=> "TU Facture", "id_societe" => 5391, "date"=> date("Y-m-d"), "tva"=> 1.19, "prix"=>200, "id_affaire"=>$this->id_affaire, "rejet"=> "contestation");
        
    }
*/  
    /* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
    public function test_commandeMidas(){
        $cm=new commande_midas();
        $this->assertEquals('a:9:{s:12:"commande.ref";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"16";s:7:"default";N;s:5:"width";i:70;}s:12:"specificDate";a:4:{s:6:"custom";b:1;s:6:"nosort";b:1;s:8:"renderer";s:15:"dateCleCommande";s:5:"width";i:300;}s:19:"commande.id_societe";a:4:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;}s:19:"commande.id_affaire";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}s:17:"commande.id_devis";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}s:13:"commande.etat";a:6:{s:4:"type";s:4:"enum";s:5:"xtype";s:5:"combo";s:4:"data";a:10:{i:0;s:9:"non_loyer";i:1;s:9:"mis_loyer";i:2;s:12:"prolongation";i:3;s:2:"AR";i:4;s:7:"arreter";i:5;s:5:"vente";i:6;s:11:"restitution";i:7;s:21:"mis_loyer_contentieux";i:8;s:24:"prolongation_contentieux";i:9;s:23:"restitution_contentieux";}s:7:"default";s:9:"non_loyer";s:5:"width";i:70;s:8:"renderer";s:4:"etat";}s:5:"files";a:4:{s:6:"custom";b:1;s:6:"nosort";b:1;s:8:"renderer";s:11:"pdfCommande";s:5:"width";i:120;}s:6:"retour";a:4:{s:6:"custom";b:1;s:6:"nosort";b:1;s:4:"type";s:4:"file";s:5:"width";i:70;}s:8:"retourPV";a:4:{s:6:"custom";b:1;s:6:"nosort";b:1;s:4:"type";s:4:"file";s:5:"width";i:70;}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");
        
    }
    
    public function test_generateCourrierType() {
        $param = array("id_commande"=>1);
        $this->assertFalse($this->obj->generateCourrierType($param),"Erreur, pas de pdf");  
        $param = array("pdf"=>1);
        $this->assertFalse($this->obj->generateCourrierType($param),"Erreur, pas de id_commande");  
        $param = array();
        $this->assertFalse($this->obj->generateCourrierType($param),"Erreur, pas de param");  
        
        $id_commande=ATF::commande()->decryptId(ATF::commande()->i(array("ref"=>"Ref tu","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1,196","id_affaire"=>$id_affaire)));
        $param = array(
            "id_commande"=>$id_commande,
            "pdf"=>"ctSigne"
        );
        $this->assertEquals($id_commande, $this->obj->generateCourrierType($param), "Erreur, le retour devrait être l'id commande");
        $this->assertArrayHasKey("fileToPrevisu", ATF::$json->get(), "Erreur, il n'y a pas l'entrée fileToPrevisu dans le JSON");
        $this->assertEquals("ctSigne", ATF::$json->get("fileToPrevisu"), "Erreur, il n'y a pas la bonne donnée dans le fileToPrevisu du JSON");

    }




    public function test_eexport_loyer_assurance(){
        ATF::commande()->q->reset()->setStrict()
                                    ->where("commande.date","2014-12-12","AND",false,">=")
                                    ->where("commande.date","2014-12-20","AND",false,"<=")
                                    ->from("commande","id_affaire","loyer","id_affaire")
                                    ->from("commande","id_affaire","affaire","id_affaire")
                                    ->addAllFields("commande")
                                    ->addAllFields("loyer")
                                    ->addAllFields("affaire");
        $infos = ATF::commande()->sa();

              
        ob_start();
        $this->obj->export_loyer_assurance($infos , true);
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
    }

     public function test_eexport_loyer_assurance_2(){
        $this->obj->q =ATF::_s("pager")->create("commande_gsa_commande_tu");

        $this->obj->q->reset()->setStrict()
                                    ->where("commande.date","2015-12-12","AND",false,">=")
                                    ->where("commande.date","2015-12-20","AND",false,"<=")
                                    ->from("commande","id_affaire","loyer","id_affaire")
                                    ->from("commande","id_affaire","affaire","id_affaire")
                                    ->addAllFields("commande")
                                    ->addAllFields("loyer")
                                    ->addAllFields("affaire");
                  
        ob_start();
        $this->obj->export_loyer_assurance(array("onglet"=>"commande_gsa_commande_tu") , "false", "false");
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();
        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
    }

    public function test_export_contrat_refinanceur_loyer(){
        ATF::commande()->q->reset()->setStrict()
                                    ->where("commande.date","2015-06-15","AND",false,">=")
                                    ->where("commande.date","2015-06-17","AND",false,"<=")
                                    ->from("commande","id_affaire","loyer","id_affaire")
                                    ->from("commande","id_affaire","affaire","id_affaire")
                                    ->addAllFields("commande")
                                    ->addAllFields("loyer")
                                    ->addAllFields("affaire")
                                    ->addField("commande.id_affaire","commande.id_affaire_fk");
        $infos = ATF::commande()->sa();

              
        ob_start();
        $this->obj->export_contrat_refinanceur_loyer($infos , true);
         //récupération des infos
        $fichier=ob_get_contents();
        ob_end_clean();

        $this->assertNotNull($fichier, "L'export ne s'est pas bien passé??");
    }

    public function test_export_contrat_refinanceur_loyer2(){
        $this->obj->q =ATF::_s("pager")->create("commande_gsa_commande_tu");

       $this->obj->q->reset()->setStrict()
                            ->where("commande.date","2015-06-15","AND",false,">=")
                            ->where("commande.date","2015-06-17","AND",false,"<=")
                            ->from("commande","id_affaire","loyer","id_affaire")
                            ->from("commande","id_affaire","affaire","id_affaire")
                            ->addAllFields("commande")
                            ->addAllFields("loyer")
                            ->addAllFields("affaire");

              
        ob_start();
        $this->obj->export_contrat_refinanceur_loyer(array("onglet"=>"commande_gsa_commande_tu") , "false", "false");
         //récupération des infos
        $fichier=ob_get_contents();     
        ob_end_clean();

        $this->assertNotNull(array(), "L'export ne s'est pas bien passé??");
    }

    public function test_commande_mep_stats(){
        ATF::stats()->liste_annees["commande"] = array("2015"=>1);

        ATF::stat_snap()->i(array("date"=>"2012-01-01", "nb"=>20,"stat_concerne"=>"mep-02m","id_agence"=>1));
        ATF::stat_snap()->i(array("date"=>"2012-01-01", "nb"=>30,"stat_concerne"=>"devis-02m","id_agence"=>1));
        ATF::stat_snap()->i(array("date"=>"2012-01-01", "nb"=>10,"stat_concerne"=>"mep-autre","id_agence"=>1));
        ATF::stat_snap()->i(array("date"=>"2012-01-01", "nb"=>15,"stat_concerne"=>"devis-autre","id_agence"=>1));

        ATF::stat_snap()->i(array("date"=>"2013-01-01", "nb"=>20,"stat_concerne"=>"mep-02m","id_agence"=>1));
        ATF::stat_snap()->i(array("date"=>"2013-01-01", "nb"=>30,"stat_concerne"=>"devis-02m","id_agence"=>1));
        ATF::stat_snap()->i(array("date"=>"2013-01-01", "nb"=>10,"stat_concerne"=>"mep-autre","id_agence"=>1));
        ATF::stat_snap()->i(array("date"=>"2013-01-01", "nb"=>15,"stat_concerne"=>"devis-autre","id_agence"=>1));
      

        $return = $this->obj->commande_mep_stats(NULL, "o2m", "true" , 2015,1);   
        $return = $this->obj->commande_mep_stats(NULL, "autre", "false" , 2015,1);
        $this->assertEquals(28.0, $return["dataset"]["objectif"]["01"]["value"] , "Retour incorrect 3");
        $this->assertEquals(12.0, $return["dataset"]["moyenne"]["01"]["value"] , "Retour incorrect 4");


        $return = $this->obj->commande_mep_stats(NULL, "autre", NULL , 2015,1);       
        $this->assertEquals("Janv", $return["categories"]["category"][0]["label"] , "Retour incorrect 5");

        $return = $this->obj->commande_mep_stats(NULL, "test", NULL , 2015,1);
        $this->assertEquals("Janv", $return["categories"]["category"][0]["label"] , "Retour incorrect 5");


        ATF::stats()->liste_annees["commande"] = array("2006"=>1);
        $return = $this->obj->commande_mep_stats(NULL, "o2m", "true" , 2006,1);


        $return = $this->obj->commande_mep_stats(NULL, "o2m", "true" , date('Y'),1);
    }


     /* @author NMorgan FLEURQUIN <mfleurquin@absystech.fr> 
    * @date 08/12/2015
    */
    public function test_uploadFileFromSA() {
        $affaire=array("ref"=>"affaire","id_societe"=>$this->id_societe,"affaire"=>"commande");
        $id_affaire=ATF::affaire()->i($affaire);
        $commande=array("ref"=>"affaire","id_societe"=>$this->id_societe,"id_user"=>$this->id_user,"tva"=>"1.196","id_affaire"=>$id_affaire);
        $commande["id_commande"]=ATF::commande()->i($commande);


        $infos = array(
            "extAction"=>"commande"
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas d'id en entrée, renvoi FALSE");        
        $infos = array(
            "id"=>$commande["id_commande"]
        );
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de class en entrée, renvoi FALSE");

        $infos['extAction'] = "commande";
        $infos['field'] = "tu";
        $this->assertFalse($this->obj->uploadFileFromSA($infos),"Erreur, pas de files en entrée, renvoi FALSE");
        
        $file = __ABSOLUTE_PATH__."test/cleodis/pdf_exemple.pdf";
        $files = array(
            "tu"=> array(
                "name"=>"pdf_exemple"
                ,"type"=>"application/pdf"
                ,"tmp_name"=>$file
                ,"error"=>0
                ,"size"=>filesize($file)
            )
        );
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/commande/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/commande/");
        if(!file_exists(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/"))util::mkdir(__ABSOLUTE_PATH__."../temp/testsuite/pdf_affaire/");
        
        $r = $this->obj->uploadFileFromSA($infos,ATF::_s(),$files);
        $this->assertEquals('{"success":true}',$r,"Erreur dans le retour de l'upload");
        $f = __ABSOLUTE_PATH__."../data/testsuite/commande/".$commande["id_commande"].".tu";
        $this->assertTrue(file_exists($f),"Erreur : le fichier n'est pas là !");
        unlink($f);

        ATF::pdf_affaire()->q->reset()->where("id_affaire",$id_affaire);
        $pdf_affaire = ATF::pdf_affaire()->select_all();

        $f = __ABSOLUTE_PATH__."../data/testsuite/pdf_affaire/".$pdf_affaire[0]["id_pdf_affaire"].".fichier_joint";
        $this->assertTrue(file_exists($f),"Erreur : le fichier pdf_affaire n'est pas là !");
        unlink($f);

    }

};

class objet_excel_commande { 
    public function __construct(){
        $this->sheet=new objet_sheet();
    }
    public function write($col,$valeur) {
        $this->$col=$valeur;
    }
}
class objet_sheet_commande {
    public function getColumnDimension($col){
        return $this;
    }
    public function setAutoSize($bool){
        $this->size=true;
    }
}
?>
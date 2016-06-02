<?
class bon_de_pret_test extends ATF_PHPUnit_Framework_TestCase {
	/** Méthode pré-test, exécutée avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		$this->initUserOnly();
 		$this->obj = ATF::bon_de_pret();
		ATF::bon_de_pret()->truncate();
	}
	
	/** Méthode post-test, exécutée après chaque test unitaire
	*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
		//Flush des notices
		ATF::$msg->getNotices();
	}

	
	/** Test du constructeur
	* @author Quentin JANON
	* @date 2012-04-12
	*/
	public function test_insert(){
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
		$r = $this->obj->insert($bp);
		
		$this->assertNotNull($r,"Pas d'id en retour");
		
		ATF::bon_de_pret_ligne()->q->reset()->where("id_bon_de_pret",$this->obj->decryptId($r))->setCount();
		$lignes = ATF::bon_de_pret_ligne()->sa();
		$this->assertEquals(3,$lignes['count'],"Pas le bon nombre de lignes");
		$this->assertFileExists($this->obj->filepath($this->obj->decryptId($r),"pdf",true),"Le PDF ne s'est pas généré");
	}	
	
	/** Test du constructeur
	* @author Quentin JANON
	* @date 2012-04-12
	*/
	public function test_preview(){
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
			"preview"=>true
			,"values_bon_de_pret"=>array(
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
		$r = $this->obj->insert($bp);
		
		$this->assertNotNull($r,"Pas d'id en retour");
		
		ATF::bon_de_pret_ligne()->q->reset()->where("id_bon_de_pret",$this->obj->decryptId($r))->setCount();
		$lignes = ATF::bon_de_pret_ligne()->sa();
		$this->assertEquals(3,$lignes['count'],"Pas le bon nombre de lignes");
		$this->assertFileExists($this->obj->filepath($this->obj->decryptId($r),"pdf",true),"Le PDF ne s'est pas généré");
	}	
	
	/** 
	* @author Quentin JANON
	* @date 2012-04-12
	*/
	public function test_getRef(){
		$this->insertSociete();
		$this->insertContact();
		$date = "2012-01-01";
		$ref = "BPSO1201001";
		
		$this->assertEquals($ref,$this->obj->getRef($date),"Erreur de référence 1");
		
		$bp = array(
			"bon_de_pret"=>"BP TU 1"
			,"date"=>$date
			,"ref"=>$ref
			,"id_societe"=>$this->id_societe
			,"id_contact"=>$this->id_contact
			,"date_debut"=>"2012-01-01"
			,"date_fin"=>"2012-02-01"
			,"id_user"=>ATF::$usr->get('id_user')
		);
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."2";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 2");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."3";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 3");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."4";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 4");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."5";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 5");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."6";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 6");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."7";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 7");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."8";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 8");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-1)."9";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 9");
		$r = $this->obj->i($bp);

		$bp['ref'] = substr($ref,0,-2)."10";
		$this->assertEquals($bp['ref'],$this->obj->getRef($date),"Erreur de référence 10");
	}
	
	/** 
	* @author Quentin JANON
	* @date 2012-04-12
	*/
	public function test_duree(){
		$this->insertSociete();
		$this->insertContact();
		$bp = array(
			"bon_de_pret"=>array(
				"bon_de_pret"=>"BP TU 1"
				,"ref"=>"BP0000001"
				,"id_societe"=>$this->id_societe
				,"id_contact"=>$this->id_contact
				,"date_debut"=>"2012-01-01"
				,"date_fin"=>"2012-02-01"
				,"id_user"=>ATF::$usr->get('id_user')
			)
		);
		$r = $this->obj->i($bp);
		
		$this->assertEquals("31 jours",$this->obj->duree($this->obj->decryptId($r)),"La durée n'est pas bonne");
		
	}
	
};
?>
<?
/**
* Tu module note de frais
* @author Quentin JANON <qjanon@absystech.fr>
* @date 23-03-2011
*/
class note_de_frais_ligne_test extends ATF_PHPUnit_Framework_TestCase {
		
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	protected function setUp() {
		$this->initUser();
 		$this->obj = ATF::note_de_frais_ligne();
		$i = array(
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
		$this->idNF = ATF::note_de_frais()->insert($i);
		
		$this->lignes = $this->obj->ss("id_note_de_frais",$this->idNF);
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
	* @date 22-08-2011
	*/ 
	public function test_select_all() {
		$this->obj->q->setCount();
		$r = $this->obj->select_all();
		$r = $r['data'];

		$this->assertArrayHasKey("canValid",$r[0],"Il n'y a pas l'entrée canValid");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-08-2011
	*/ 
	public function test_canValid() {
		$this->assertFalse($this->obj->canValid(987654321),"Doit retourner FALSE car ID user complètement pas crédible.");
		ATF::$usr->set('id_user',3);
		$this->assertTrue($this->obj->canValid(1),"Soit Sol-R n'a plus l'ID 3, soit quelque chose cloche...");
		
		ATF::$usr->set('id_user',1);
		$this->assertTrue($this->obj->canValid(12),"Le supérieur du user 12 doit être le user 1... ou alors ca débloque.");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_valid() {
		$r = $this->obj->valid($this->lignes[0]);
		$this->assertTrue($r,'Erreur, pas der eturn TRUE');
		$this->assertEquals("ok",$this->obj->select($this->lignes[0]['id_note_de_frais_ligne'],"etat"),"Erreur, la ligne n'est pas passé en OK");
		$this->assertNotNull($this->obj->select($this->lignes[0]['id_note_de_frais_ligne'],"raison"),"Erreur, la raison n'est pas modifié");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 23-03-2011
	*/ 
	public function test_refus() {
		$this->lignes[1]['raison'] = "Pas envie, un point c'est tout";
		$r = $this->obj->refus($this->lignes[1]);
		$this->assertTrue($r,'Erreur, pas der eturn TRUE');
		$this->assertEquals("nok",$this->obj->select($this->lignes[1]['id_note_de_frais_ligne'],"etat"),"Erreur, la ligne n'est pas passéen OK");
		$this->assertEquals("Pas envie, un point c'est tout",$this->obj->select($this->lignes[1]['id_note_de_frais_ligne'],"raison"),"Erreur, la raison n'est pas la bonne");
	}
	
	
	
};
?>
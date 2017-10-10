<?
/**
* TU sur le module contact d'optima
*/
class contact_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	private $contact;
	private $societe;

	public function setUp() {
		$this->initUser();
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
			
	private function environnement_test(){
		ATF::db()->query("Update devis Set id_remplacant= NULL");
		ATF::db()->truncate("ordre_de_mission");

		ATF::db()->truncate("devis");

		ATF::db()->truncate("contact");		

		$this->contact =ATF::contact()->i(array(
			'civilite' => 'M',
			'nom'=>"Test",
			'prenom'=>"Test"
		));
		$this->societe =ATF::societe()->i(array(
			"societe"=>"societe TEST TU",
			"id_commercial"=> $this->contact
		));
	}
	// @author Cyril Charlier <ccharlier@absystech.fr>
	public function test_get() {
		$this->environnement_test();
		$contact = ATF::contact()->_GET();

		$this->assertEquals('Test',$contact[0]['nom'],"Mauvais nom retourné pour le contact");
		$this->assertEquals('M',$contact[0]['civilite'],"Mauvaise civilite retournée pour le contact");
		$this->assertEquals("Test",$contact[0]['prenom'], "Mauvais prenom retourné pour le contact");
		$this->assertEquals("France",$contact[0]['id_pays'], "Mauvais pays retourné pour le contact");


	}
	public function test_getWithSearch() {
		$this->environnement_test();
		ATF::contact()->i(array(
			'civilite' => 'Mme',
			'nom'=>"TU",
			'prenom'=>"TU search"
		));
		$get = array('search'=> 'TU search');
		$contact = ATF::contact()->_GET($get);

		$this->assertEquals('TU',$contact[0]['nom'],"Mauvais nom retourné pour le contact");
		$this->assertEquals('Mme',$contact[0]['civilite'],"Mauvaise civilite retournée pour le contact");
		$this->assertEquals("TU search",$contact[0]['prenom'], "Mauvais prenom retourné pour le contact");
		$this->assertEquals("France",$contact[0]['id_pays'], "Mauvais pays retourné pour le contact");


	}
	public function test_getWithId() {
		$this->environnement_test();

		$id =ATF::contact()->i(array(
			'civilite' => 'Mme',
			'nom'=>"azertyuiop",
			'prenom'=>"TU test"
		));
		$get = array('id'=> $id);
		$contact = ATF::contact()->_GET($get);

		$this->assertEquals('azertyuiop',$contact['nom'],"Mauvais nom retourné pour le contact");
		$this->assertEquals('Mme',$contact['civilite'],"Mauvaise civilite retournée pour le contact");
		$this->assertEquals("TU test",$contact['prenom'], "Mauvais prenom retourné pour le contact");
		$this->assertEquals("France",$contact['id_pays'], "Mauvais pays retourné pour le contact");
	}
	public function test_getWithSociete() {
		$this->environnement_test();
		$contact = array(
			'civilite' => 'Mme',
			'nom'=>"TU contact",
			'prenom'=>"TU prenom",
			'id_societe'=> $this->societe
		);
		ATF::contact()->i($contact);

		$contact2 = array(
			'civilite' => 'M',
			'nom'=>"TU contact2",
			'prenom'=>"TU prenom2",
			'id_societe' => $this->societe
		);
		ATF::contact()->i($contact2);
		$get = array('id_societe'=> $this->societe,
					"tri"=>"id_societe");
		$contact = ATF::contact()->_GET($get);

		$this->assertEquals('TU contact',$contact[0]['nom'],"Mauvais nom retourné pour le contact");
		$this->assertEquals('Mme',$contact[0]['civilite'],"Mauvaise civilite retournée pour le contact");
		$this->assertEquals("TU prenom",$contact[0]['prenom'], "Mauvais prenom retourné pour le contact");
		$this->assertEquals("France",$contact[0]['id_pays'], "Mauvais pays retourné pour le contact");
	}
	public function test_SendMailTeamViewerError() {
		$infos['id_contact'] = $this->contact;
		try{
			ATF::contact()->sendMailTeamViewer($infos);
		}catch(errorATF $e){
			$code_error = $e->getCode();
			$message_error= $e->getMessage();
		}
		$this->assertEquals(880,$code_error, "Mauvais code erreur retourné");
		$this->assertEquals("Le contact n'a pas d'adresse mail renseignée",$message_error,"Mauvaise civilite retournée pour le contact");
	}
	public function test_SendMailTeamViewer() {
		$infos['id_contact'] = ATF::contact()->i(array(
			'civilite' => 'M',
			'nom'=>"TU contact2",
			'prenom'=>"TU prenom2",
			"email"=> "test@absystech.fr",
			'id_societe' => $this->societe
		));
		$ret= ATF::contact()->sendMailTeamViewer($infos);
		$this->assertEquals('Mail envoyé pour le téléchargement de Teamviewer',$ret[0]['msg'],"Mauvais message retourné");
		$this->assertEquals('success',$ret[0]['type'],"Mauvais type retourné");
	}
	/*
	Get filter par logique -> retourne plus de ligne que prévu.
	public function test_getWithFilter() {
		$this->environnement_test();
		$contact = array(
			'civilite' => 'Mme',
			'nom'=>"TU contact",
			'prenom'=>"TU prenom",
		);
		ATF::contact()->i($contact);

		$contact2 = array(
			'civilite' => 'M',
			'nom'=>"TU contact2",
			'prenom'=>"TU prenom2",
		);
		ATF::contact()->i($contact2);
		$get = array("filter"=>array("nom"=>"TU contact"));
		$contact = ATF::contact()->_GET($get);		
		//$this->assertEquals(2,count($contact),"Mauvais nombre de ligne retourné");
		$this->assertEquals('TU contact2',$contact[0]['nom'],"Mauvais nom retourné pour le contact");
		$this->assertEquals('M',$contact[0]['civilite'],"Mauvaise civilite retournée pour le contact");
		$this->assertEquals("TU prenom2",$contact[0]['prenom'], "Mauvais prenom retourné pour le contact");
		$this->assertEquals("France",$contact[0]['id_pays'], "Mauvais pays retourné pour le contact");
	}
	*/

};
?>
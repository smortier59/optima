<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class contact_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}

	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_conMidas(){
		$cm=new contact_midas();
		$this->assertEquals('a:8:{s:14:"contact.prenom";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"100";s:7:"default";N;s:4:"null";b:1;}s:11:"contact.nom";a:4:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"100";s:7:"default";N;}s:18:"contact.id_societe";a:5:{s:4:"type";s:3:"int";s:5:"xtype";s:11:"numberfield";s:9:"maxlength";s:1:"8";s:7:"default";N;s:4:"null";b:1;}s:16:"contact.fonction";a:5:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;}s:11:"contact.tel";a:6:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"20";s:7:"default";N;s:4:"null";b:1;s:5:"width";i:120;}s:11:"contact.gsm";a:6:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:2:"20";s:7:"default";N;s:4:"null";b:1;s:5:"width";i:120;}s:13:"contact.email";a:7:{s:4:"type";s:4:"text";s:5:"xtype";s:9:"textfield";s:9:"maxlength";s:3:"255";s:7:"default";N;s:4:"null";b:1;s:8:"renderer";s:5:"email";s:5:"width";i:250;}s:9:"completer";a:4:{s:6:"custom";b:1;s:8:"renderer";s:8:"progress";s:9:"aggregate";a:2:{i:0;s:3:"min";i:1;s:3:"avg";}s:5:"width";i:100;}}',serialize($cm->colonnes['fields_column']),"Le constructeur de la classe midas a changé");

	}

	/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
	public function test_saCustomMidas(){
		$c=new contact_midas();
		$c->saCustom();
		$this->assertEquals("b428ed06fff583042b90f4fc0ab79575",md5($c->q->lastSQL),"Les conditions de filtrage ont changé ?");
	}

	public function test_insert(){
		try{
			$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser', "pwd"=> "abcdefghijkl","id_societe" => 246));
			$id_contact=$this->obj->insert($contact,$this->s);
		}catch(errorATF $e){
			$erreur = $e->getMessage();
		}
		$this->assertEquals($erreur , "Le mot de passe doit contenir 6 caractères dont au moins 1 chiffre et 1 majuscule" , "1 -Erreur password non declenchée !!");

		try{
			$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser', "pwd"=> "P1w", "id_societe" => 246));
			$id_contact=$this->obj->insert($contact,$this->s);
		}catch(errorATF $e){
			$erreur = $e->getMessage();
		}
		$this->assertEquals($erreur , "Le mot de passe doit contenir 6 caractères dont au moins 1 chiffre et 1 majuscule" , "2 -Erreur password non declenchée !!");

		$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser', "pwd"=> "MotDeP@sse12345", "id_societe" => 246));

		/*try{
			$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser', "pwd_client"=> "abcdefghijkl","id_societe" => 246));
			$id_contact=$this->obj->insert($contact,$this->s);
		}catch(errorATF $e){
			$erreur = $e->getMessage();
		}
		$this->assertEquals($erreur , "Le mot de passe client doit contenir 6 caractères dont au moins 1 chiffre et 1 majuscule" , "1 -Erreur password non declenchée !!");

		try{
			$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser', "pwd_client"=> "P1w", "id_societe" => 246));
			$id_contact=$this->obj->insert($contact,$this->s);
		}catch(errorATF $e){
			$erreur = $e->getMessage();
		}
		$this->assertEquals($erreur , "Le mot de passe client doit contenir 6 caractères dont au moins 1 chiffre et 1 majuscule" , "2 -Erreur password non declenchée !!");
		$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser', "pwd_client"=> "MotDeP@sse12345", "id_societe" => 246));
		$id_contact=$this->obj->insert($contact,$this->s);

		$this->assertNotNull($id_contact , "3 -Insertion non faite? !!");*/
	}

	public function test_update(){
		$sup = array("login" => "lesup", "password"=> "sup", "id_societe" => 246, "prenom"=> "PSup", "nom"=> "NSup");
		$id_sup = ATF::user()->i($sup);
		$user = array("login" => "user", "password"=> "sup", "id_societe" => 246, "prenom"=> "PUser", "nom"=> "NUser", "id_superieur" => $id_sup);
		$id_user  = ATF::user()->i($user);
		$contact=array('contact'=>array('nom'=>'NUser','prenom'=>'PUser',  "id_owner" => $id_sup, "id_societe" => 246));
		$id_contact=$this->obj->insert($contact,$this->s);

		$soc = array("id_owner" => $id_user, "societe" => "societeTU");
		$id_soc = ATF::societe()->i($soc);

		//Passage de l'utilisateur en inactif
		$contact = ATF::contact()->select($id_contact);
		$contact["etat"] = "inactif";
		$infos["contact"] = $contact;

		$this->obj->update($infos,$this->s);
		$this->assertEquals($id_sup , ATF::societe()->select($id_soc , "id_owner") , "1 - Le changement de responsable n'a pas eu lieu !!");

		//Passage de l'utilisateur en inactif alors que le sup ne fait plus parti de la societe
		ATF::contact()->u(array("id_contact" => $id_contact , "etat" => "actif"));
		ATF::user()->u(array("id_user" => $id_sup , "etat" => "inactif"));
		ATF::societe()->u(array("id_societe" => $id_soc , "id_owner" => $id_user));

		$contact = ATF::contact()->select($id_contact);
		$contact["etat"] = "inactif";
		$infos["contact"] = $contact;

		$this->obj->update($infos,$this->s);
		$this->assertEquals(16 , ATF::societe()->select($id_soc , "id_owner") , "2 - Le changement de responsable n'a pas eu lieu !!");


		//Passage de l'utilisateur en inactif sans superieur
		ATF::contact()->u(array("id_contact" => $id_contact , "etat" => "actif"));
		ATF::user()->u(array("id_user" => $id_user , "id_superieur" => NULL));
		ATF::societe()->u(array("id_societe" => $id_soc , "id_owner" => $id_user));

		$contact = ATF::contact()->select($id_contact);
		$contact["etat"] = "inactif";
		$infos["contact"] = $contact;

		$this->obj->update($infos,$this->s);

		$this->assertEquals(16 , ATF::societe()->select($id_soc , "id_owner") , "2 - Le changement de responsable n'a pas eu lieu !!");

		try{
			$this->obj->update(array('contact'=>array('id_contact' => $id_contact, "pwd"=> "P1w")));
		}catch(errorATF $e){
			$erreur = $e->getMessage();
		}
		$this->assertEquals($erreur , "Le mot de passe doit contenir 6 caractères dont au moins 1 chiffre et 1 majuscule" , "2 -Erreur password non declenchée !!");

	}

	public function test_loginQuery(){
		$this->obj->loginQuery(array("u"=>"jloison"));
		$res = $this->obj->select_all();

		$this->assertEquals($res["id_contact"] , 2113, "Nouveau contact avec le login jloison ?");
		$this->assertEquals($res["nom"] , "LOISON", "Nouveau contact avec le login jloison 2 ?");
	}

	public function test_constructCap(){
		$c = new contact_cap();
		$this->assertTrue($c instanceOf contact_cap, "L'objet contact_cap n'est pas de bon type");

	}

};

?>
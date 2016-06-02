<?
/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
class suivi_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}

	//méthode pour créer un environnement de test
	public function creerSocCon(){
		//creation d'une societe
		$this->id_societe=ATF::societe()->i(array("societe"=>"soc lol"));
		$this->assertTrue(is_numeric($this->id_societe),"La société ne s'est pas créée");
		//création d'un contact
		$this->id_contact=ATF::contact()->i(array("nom"=>"tu lol","email"=>"clol@lol.fr",'id_societe'=>$this->id_societe));
		$this->assertTrue(is_numeric($this->id_contact),"Le contact ne s'est pas créé");
	}

	//méthode pour créer le suivi bidon avec tous les éléments nécessaire
	public function ajoutSuivi(){
		$this->initUserOnly(false);
		
		$this->creerSocCon();
		//création d'utilisateur
		$this->id_user=ATF::user()->i(array("login"=>"log lol","password"=>"lol","civilite"=>"M","prenom"=>"lol","nom"=>"lol","email"=>"lol@lol.fr"));
		$this->assertTrue(is_numeric($this->id_user),"Le user ne s'est pas créé");
		$this->id_user2=ATF::user()->i(array("login"=>"log lol2","password"=>"lol2","civilite"=>"M","prenom"=>"lol2","nom"=>"lol2","email"=>"lol2@lol.fr"));
		$this->assertTrue(is_numeric($this->id_user2),"Le user2 ne s'est pas créé");		
		
	}

	public function test_insert(){
		$this->ajoutSuivi();
		ATF::setSingleton("imap", new mockObjectCurlClassesSuivi());

		$infos=array('suivi'=>array('texte'=>'TU suivi'
									,'id_contact'=>1
									,'id_societe'=>$this->id_societe									
									,"suivi_contact"=>$this->id_contact
									,"suivi_societe"=>$this->id_user
									,"no_redirect"=>true
									,"suivi_from_mail"=>array(0=>"Nouveau message 2 sur le poste 1000 | Asterisk PBX <messagerie@absystech.net>")));
		$id = $this->obj->insert($infos);
		$texte = "TU suivi\n\nMail :\n\nCeci est le message du mail";
		$this->assertEquals($texte, ATF::suivi()->select($id, "texte"), "Le texte n'est pas bon");


		$infos=array('suivi'=>array('texte'=>'TU suivi'
									,'id_contact'=>1
									,'id_societe'=>$this->id_societe									
									,"suivi_contact"=>$this->id_contact
									,"suivi_societe"=>$this->id_user
									,"no_redirect"=>true
									,"suivi_from_mail"=>array(0=>"Nouveau message 3 sur le poste 1000 | Asterisk PBX <messagerie@absystech.net>")));
		$id = $this->obj->insert($infos);
		$texte = "TU suivi\n\nMail :\n\nCeci est le message du mail";
		$this->assertEquals($texte, ATF::suivi()->select($id, "texte"), "Le texte n'est pas bon");


		ATF::unsetSingleton("imap");
	}


}
//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
class mockObjectCurlClassesSuivi extends imap {
	public function init($host, $port, $mail, $password){
		$this->error = NULL;			
	}
		
    public function imap_fetch_overview($type){
        $user=array(
            "login"=>"altTutul"
            ,"password" => "toto"
            ,"id_societe" => 1
            ,"prenom" => "toto"
            ,"nom" => "tata"
            ,"email" => "scanner@cleodis.fr"         
        );
        	
        $mail1 = Array( "subject" => "Nouveau message 2 sur le poste 1000",
			            "from" => "Asterisk PBX <messagerie@absystech.net>",
			            "to" => "Optima AbsysTech SUIVI <optima.absystech.suivi@absystech.fr>",
			            "date" => "Tue, 18 Nov 2014 19:01:23 +0100",
			            "message_id" => "<Asterisk-2-1988800433-1000-15689@oxygene.absystech.net>",
			            "size" => 30934,
			            "uid" => 258,
			            "msgno" => 1,
			            "recent" => 0,
			            "flagged" => 0,
			            "answered" => 0,
			            "deleted" => 0,
			            "seen" => 0,
			            "draft" => 0,
			            "udate" => 1416333684);

        $mail2 = Array( "subject" => "Nouveau message 3 sur le poste 1000",
			            "from" => "Asterisk PBX <messagerie@absystech.net>",
			            "to" => "Optima AbsysTech SUIVI <optima.absystech.suivi@absystech.fr>",
			            "date" => "Tue, 18 Nov 2014 19:01:23 +0100",
			            "message_id" => "<Asterisk-2-1988800433-1000-15689@oxygene.absystech.net>",
			            "size" => 30934,
			            "uid" => 259,
			            "msgno" => 1,
			            "recent" => 0,
			            "flagged" => 0,
			            "answered" => 0,
			            "deleted" => 0,
			            "seen" => 0,
			            "draft" => 0,
			            "udate" => 1416333684);

        $tmp = Array(0 => json_decode(json_encode($mail1), FALSE) , 1 => json_decode(json_encode($mail2), FALSE));
        
        
        return $tmp;
    }

    public function returnBody($uid){
    	return "Ceci est le message du mail";
    }

    public function get_attachments($uid, $filename=NULL){
    	if($uid == 258){
    		if(!$filename){
    			return array(array("filename"=>"musique.WAV"));
    		}else{
    			return "qzdqzzqd";
    		}    		
    	}  
    	if($uid == 259){
    		if(!$filename){
    			return array(array("filename"=>"musique.pdf"));
    		}else{
    			return "qzdqzzqd";
    		}    		
    	}   	
    }
	public function imap_expunge() {}
};
?>
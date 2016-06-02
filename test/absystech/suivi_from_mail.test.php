<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class suivi_from_mail_test extends ATF_PHPUnit_Framework_TestCase {

	public function test_recup_mail_suivi(){
		ATF::setSingleton("imap", new mockObjectCurlClassesSuiviMail());
		$return = $this->obj->recup_mail_suivi();
		$data= array(array(0=>"259",1=>"Nouveau message 3 sur le poste 1000 | Asterisk PBX <messagerie@absystech.net>"));

		$this->assertEquals($data, $return, "Le retour est incorrect !");

		ATF::unsetSingleton("imap");
	}

}
//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
class mockObjectCurlClassesSuiviMail extends imap {
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
			            "seen" => 1,
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
    	return array(array("filename"=>"musique.wav"), array("filename"=>"fichier.pdf"));
    }
	public function imap_expunge() {}
};
?>	
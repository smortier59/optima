<?
/**
* Classe de test sur le module societe_cleodis
*/
class scanner_test extends ATF_PHPUnit_Framework_TestCase {
		
	/** Méthode pré-test, exécute avant chaque test unitaire
	* besoin d'un user pour les traduction
	*/
	public function setUp() {
		ATF::db()->begin_transaction(true);		
	}
	
	/** Méthode post-test, exécute après chaque test unitaire*/
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	public function test_transfert(){
		$id = $this->obj->insert(array("nbpages"=>2, "provenance"=>"toto@absystech.fr"));
		
		$infos["id_scanner"] = $id;
		$infos["comboDisplay"] = "devis.retourBPA";
		$infos["transfert"] = "Devis - Retour Bon Pour Accord";
		$infos["reference"] = "test";
		
		try{
			$this->obj->transfert($infos);
		}catch(errorATF $e){
			$error = $e->getMessage();
		}
		$this->assertEquals("Il n'y a pas de Devis  ayant la référence test", $error, "1 - Error Ref inconnue");		
	}
	
	public function test_checkMailBox(){
		$mail = 'scanner@cleodis.fr';
        $host = "lithium.absystech.net"; 
        $port = 143;
        $password = "sdfis8HDS";         
        ATF::setSingleton("imap", new mockObjectCurlClassesScanner());
        try{
            $this->obj->checkMailBox("toto", $host, $port, $password);
        }catch (errorATF $e) {
            $eMsg = $e->getMessage();
        }
        $this->assertEquals($eMsg, "error", "L'erreur catch n'est pas bonne !!");
		$this->assertEquals(true,$this->obj->checkMailBox($mail, $host, $port, $password), "Erreur lors de la lecture du mail !!");

		$this->obj->checkMailBox($mail, $host, $port, $password);

		$this->obj->checkMailBox($mail, $host, $port, $password,"facture_fournisseur");


    }

    public function test_getnotransfered(){
    	$this->obj->insert(array("date"=>date("Y-m-d"), "provenance"=> "test@absystech.fr"));
    	$retour = $this->obj->getNoTransfered();
    	$this->assertNotNull($retour , "Pas de fichier non transferé??");
    }
}



//@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
class mockObjectCurlClassesScanner extends imap {
	public function init($host, $port, $mail, $password){
		if ($mail !=="scanner@cleodis.fr") {
			$this->error = "error";
		} else {
			$this->error = NULL;	
		}
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
			            "seen" => 1,
			            "draft" => 0,
			            "udate" => 1416333684);

        $tmp = Array(0 => json_decode(json_encode($mail1), FALSE) , 1 => json_decode(json_encode($mail2), FALSE));
        
        
        return $tmp;
    }

    public function get_attachments($uid, $filename=NULL){
    	$return = array();
    	if(!$filename){
    		$return[0]["filename"]  = "test.pdf";    		
    		return $return;
    	}else{
    		return "% CANON_PFINF_TYPE0_TEXTOFF
					endstream
					endobj
					6 0 obj
					<< /Length 44 >> 
					stream
					q
					594.72 0 0 812.16 0.00 0.00 cm
					/Obj4 Do
					Q

					endstream
					endobj
					7 0 obj
					<< 
					/Type /Page 
					/MediaBox [ 0 0 594.72 812.16 ] 
					/Parent 3 0 R 
					/Resources << /XObject << /Obj4 4 0 R >> /ProcSet [ /PDF /Text /ImageB /ImageC /ImageI ] >> 
					/Contents [ 5 0 R 6 0 R ] 
					>> 
					endobj
					3 0 obj
					<< 
					/Kids [ 7 0 R ] 
					/Count 1 
					/Type /Pages 
					>> 
					endobj";
    	}
    }
	

	public function imap_delete($uid) {}
	public function imap_expunge() {}
};
?>	
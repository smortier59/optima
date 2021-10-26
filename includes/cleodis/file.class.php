<?php



require_once dirname(__FILE__)."/../../libs/ATF/includes/file.class.php";
class file_cleodis extends file {



	public function _POST($get, $post, $files) {
		log::logger($post , "mfleurquin");

		$return = parent::_POST($get,$post, $files);

		ATF::user()->q->reset()->where("login", "lhochart", "OR", "filles")
							   ->where("login", "mmysoet", "OR", "filles")
    						   ->where("login", "jvasut", "OR", "filles");
							   //->where("login", "smazars", "OR", "filles")
    						   //->where("login", "abowe", "OR", "filles");
    	$filles = ATF::user()->sa();

    	$dest = array();
		foreach ($filles as $key => $value) {
    		$dest[] = $value["id_user"];
    	}

		foreach ($files as $key => $value) {
			//ATF::getClass($post['mod'])

			if($post["mod"] == "affaire"){



				$affaire = ATF::affaire()->select(ATF::affaire()->decryptID($post["id"]));

				$tache = array("tache"=>array(
					"id_societe"=> $affaire["id_societe"],
					"id_user"=>ATF::$usr->getID(),
					"origine"=>"societe_commande",
					"tache"=>"Document envoyé par le partenaire. Merci de traiter.",
					"id_affaire"=>$affaire["id_affaire"],
					"type_tache"=>"creation_contrat",
					"horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
					"no_redirect"=>"true"
				),
				"dest"=>$dest
				);
				ATF::tache()->insert($tache);
			}

		}

		if($post["commentaire"]){
			$suivi = array(
				 "id_user"=>ATF::$usr->getID()
				,"id_societe"=>$affaire["id_societe"]
				,"id_affaire"=>$affaire["id_affaire"]
				,"type_suivi"=>'devis'
				,"texte"=>"Document envoyé par le partenaire avec un commentaire : <br /> ".$post['commentaire']
				,'public'=>'oui'
				,'id_contact'=>NULL
				,'suivi_notifie'=>$dest
			);
			$suivi["no_redirect"] = true;

			ATF::suivi()->insert($suivi);
		}


		return $return;

	}


}

class file_cleodisbe extends file_cleodis { }
class file_bdomplus extends file_cleodis { }
class file_boulanger extends file_cleodis { }
class file_assets extends file_cleodis { }

class file_go_abonnement extends file_cleodis { }
?>
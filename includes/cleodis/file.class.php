<?php



require_once dirname(__FILE__)."/../../libs/ATF/includes/file.class.php";
class file_cleodis extends file {



	public function _POST($get, $post, $files) {

		$return = parent::_POST($get,$post, $files);


		foreach ($files as $key => $value) {
			//ATF::getClass($post['mod'])

			if($post["mod"] == "affaire"){
				ATF::user()->q->reset()->where("login", "smazars", "OR", "filles")
            						   ->where("login", "jvasut", "OR", "filles")
            						   ->where("login", "abowe", "OR", "filles");
            	$filles = ATF::user()->sa();
				$dest = array();
				foreach ($filles as $key => $value) {
            		$dest[] = $value["id_user"];
            	}

				$affaire = ATF::affaire()->select(ATF::affaire()->decryptID($post["id"]));

				$tache = array("tache"=>array(
					"id_societe"=> $affaire["id_societe"],
					"id_user"=>ATF::$usr->getID(),
					"origine"=>"societe_commande",
					"tache"=>"Document envoyé par le partenaire. merci de traiter.",
					"id_affaire"=>$affaire["id_affaire"],
					"type_tache"=>"creation_contrat",
					"horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
					"no_redirect"=>"true"
				),
				"dest"=>$dest
				);
				ATF::tache()->insert($tache);


				/*$suivi = array(
					 "id_user"=>ATF::$usr->getID()
					,"id_societe"=>$affaire["id_societe"]
					,"id_affaire"=>$affaire["id_affaire"]
					,"type_suivi"=>'devis'
					,"texte"=>"Document envoyé par le partenaire. merci de traiter "
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_notifie'=>$dest
				);
				$suivi["no_redirect"] = true;

				ATF::suivi()->insert($suivi);*/
			}

		}


		return $return;

	}


}

class file_cleodisbe extends file_cleodis { }
class file_bdomplus extends file_cleodis { }
class file_boulanger extends file_cleodis { }
class file_assets extends file_cleodis { }
?>
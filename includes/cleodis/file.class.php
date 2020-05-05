<?php



require_once dirname(__FILE__)."/../../libs/ATF/includes/file.class.php";
class file_cleodis extends file {



	public function _POST($get, $post, $files) {

		$return = parent::_POST($get,$post, $files);


		foreach ($files as $key => $value) {
			//ATF::getClass($post['mod'])

			if($post["mod"] == "affaire"){
				$dest = array("21","112", "103");  //Allison, Severine, Emily

				$affaire = ATF::affaire()->select(ATF::affaire()->decryptID($post["id"]));

				$suivi = array(
					 "id_user"=>ATF::$usr->getID()
					,"id_societe"=>$affaire["id_societe"]
					,"id_affaire"=>$affaire["id_affaire"]
					,"type_suivi"=>'devis'
					,"texte"=>"Nouveau fichier envoyé par le partenaire : ".ATF::$usr->trans($key, "affaire")
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_notifie'=>$dest
				);
				$suivi["no_redirect"] = true;

				ATF::suivi()->insert($suivi);
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
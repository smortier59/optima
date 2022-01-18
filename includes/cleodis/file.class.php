<?php



require_once dirname(__FILE__)."/../../libs/ATF/includes/file.class.php";
class file_cleodis extends file {



	public function _POST($get, $post, $files) {

		$return = parent::_POST($get,$post, $files);

		if($post["mod"] == "affaire"){

			$affaire = ATF::affaire()->select(ATF::affaire()->decryptID($post["id"]));

			if ($files["facture_partenaire"]['size'] || $files["facture_partenaire_2"]['size'] || $files["facture_partenaire_3"]['size'])  {
				$dest_fact = ATF::user()->getDestinataireFromConstante("__SUIVI_NOTIFIE_UPLOAD_FACTURE_PARTENAIRE__");

				if ($dest_fact) {
					$suivi = array(
						"id_user"=>ATF::$usr->getID()
						,"id_societe"=>$affaire["id_societe"]
						,"id_affaire"=>$affaire["id_affaire"]
						,"type_suivi"=>'Comptabilité'
						,"texte"=>"Nouvelle facture envoyée par le partenaire avec un commentaire"
						,'public'=>'oui'
						,'id_contact'=>NULL
						,'suivi_notifie'=>$dest_fact
					);
					$suivi["no_redirect"] = true;
					ATF::suivi()->insert($suivi);
				}

			}

			$dest = ATF::user()->getDestinataireFromConstante("__NOTIFIE_UPLOAD_PJ_PARTENAIRE__");
			if ($dest) {
				foreach ($files as $key => $value) {

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
			}


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
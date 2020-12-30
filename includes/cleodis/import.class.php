<?
/**
* @package Optima
*/
class import extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->fieldstructure();
	}


	public function _POST($get,$post, $files){
		log::logger($get , "mfleurquin");

		log::logger($post , "mfleurquin");
		log::logger($files , "mfleurquin");

		if ($content_file = file_get_contents($files['devis_file']['tmp_name'])) {
			$this->store(ATF::_s(),$devis["id_affaire"],'devis_partenaire',$content_file);
		}

		return true;
	}


	/**
	 * Mise à jour des infos produits selon une ref et un fournisseur
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  file $file  Fichier CSV contenant les données
	 * @return array        Retourne un tableau avec succes, error, resume
	 */
	public function maj_infos_produit($file){
		$resume = $success = $error = null;

		$resume[] = "========= DEBUT DE SCRIPT =========";
		ATF::db()->begin_transaction();

		$fileProduit = $file;
		$fpr = fopen($fileProduit, 'rb');
		$entete = fgetcsv($fpr);

		$entetes = explode(";", $entete[0]);


		$lines_count = 0;
		$processed_lines = 0;


		if($entetes !== array("ref", "fournisseur", "site_associe", "designation", "Prix achat dont ecotaxe", "loyer", "description", "url_image")){
			$success = false;
			$error["alertes"][] = "Entetes de fichier incorrecte, entetes attendues : ref, fournisseur, site_associe, designation, Prix achat dont ecotaxe, loyer, description, url_image";
		}else{

			foreach ($entetes as $key => $value) {
				$entetes_key[$value] = $key;
			}

			while ($ligne = fgetcsv($fpr, 0, ';')) {
				if (!$ligne[1]) continue; // pas d'ID pas de chocolat

				$lines_count ++;

				$erreur_get = false;
				if($this->get_fournisseur($ligne[$entetes_key["fournisseur"]]) == null){
					$erreur_get = true;
					$error["data"][] = "Fournisseur inconnu ".$ligne[$entetes_key["fournisseur"]];
				}

				if($this->get_site_associe($ligne[$entetes_key["site_associe"]]) == null){
					$error["data"][] = "Site associe inconnu ".$ligne[$entetes_key["site_associe"]];
					$erreur_get = true;
				}


				if(!$erreur_get){
					ATF::produit()->q->reset()->where("ref", $ligne[$entetes_key["ref"]], "AND")
											  ->where("id_fournisseur", $this->get_fournisseur($ligne[$entetes_key["fournisseur"]]));
					$p = ATF::produit()->select_row();
					if($p){
						$prod = array(
							"id_produit" => $p["id_produit"],
							"site_associe" => $this->get_site_associe($ligne[$entetes_key["site_associe"]]),
							"id_fournisseur" => $this->get_fournisseur($ligne[$entetes_key["fournisseur"]]),
							"produit" => $ligne[$entetes_key["designation"]],
							"prix_achat" => $ligne[$entetes_key["Prix achat dont ecotaxe"]],
							"loyer" => $ligne[$entetes_key["loyer"]],
							"description" => $ligne[$entetes_key["description"]],
							"url_image" => $ligne[$entetes_key["url_image"]]
						);

						try{
							ATF::produit()->u($prod);
							$processed_lines++;
						} catch (errorATF $e) {
							$error["data"][] = "Erreur lors de la mise à jour du produit (".$ligne[$entetes_key["ref"]]."/".$ligne[$entetes_key["fournisseur"]]." Erreur : ".$e->getMessage();
						}
					}else{
						$error["data"][] = "Produit ref/fournisseur (".$ligne[$entetes_key["ref"]]."/".$ligne[$entetes_key["fournisseur"]]." non trouvé";
					}
				}

			}
		}

		$resume[] = "##### Resume du parcours du fichier";
		$resume[] = "total ligne: $lines_count";
		$resume[] = "Ligne mise à jour: $processed_lines";

		ATF::db()->commit_transaction();
		$resume[] ="========= FIN DE SCRIPT =========";

		return array("success"=>$success, "error"=> $error, "resume" => $resume);
	}






	/**
	 * Récupère le fournisseur depuis un nom
	 * @param  String $fournisseur Nom du fournisseur
	 * @return Integer|String              ID du fournisseur si existant où un message d'information
	 */
	private function get_fournisseur($fournisseur){
		ATF::societe()->q->reset()->where("societe", ATF::db()->real_escape_string($fournisseur), "AND", false, "LIKE");
		$f = ATF::societe()->select_row();

		if($f){
			return $f["id_societe"];
		}else{
			return null;
		}
	}

	/**
	 * Récupère le site_associe depuis un nom
	 * @param  String $fournisseur Nom du site associe
	 * @return Integer|String              ID du site associe si existant où un message d'information
	 */
	private function get_site_assoce($site_associe){
		ATF::site_associe()->q->reset()->where("site_associe", ATF::db()->real_escape_string($site_associe), "AND", false, "LIKE");
		$f = ATF::site_associe()->select_row();

		if($f){
			return $f["id_site_associe"];
		}else{
			return null;
		}
	}

	/**
	 * Récupère le fabriquant depuis un nom
	 * @param  String $fabriquant Nom du fabriquant
	 * @return Integer|String              ID du fabriquant si existant où un message d'information
	 */

	private function get_fabriquant($fabriquant){
		ATF::fabriquant()->q->reset()->where("fabriquant", ATF::db()->real_escape_string($fabriquant), "AND", false, "LIKE");
		$f = ATF::fabriquant()->select_row();

		if($f){
			return $f["id_fabriquant"];
		}else{
			return ATF::fabriquant()->i(array("fabriquant"=>$fabriquant));
		}
	}

	/**
	 * Récupère le categorie depuis un nom
	 * @param  String $categorie Nom du categorie
	 * @return Integer|String              ID du categorie si existant où un message d'information
	 */
	private function get_categorie($categorie){
		ATF::categorie()->q->reset()->where("categorie", ATF::db()->real_escape_string($categorie), "AND", false, "LIKE");
		$f = ATF::categorie()->select_row();

		if($f){
			return $f["id_categorie"];
		}else{
			return ATF::categorie()->i(array("categorie"=>ATF::db()->real_escape_string($categorie)));
		}
	}

	/**
	 * Récupère le sous catégorie depuis un nom
	 * @param  String $sous catégorie Nom du sous catégorie
	 * @return Integer|String              ID du sous catégorie si existant où un message d'information
	 */
	private function get_sous_categorie($sous_categorie, $categorie){
		ATF::sous_categorie()->q->reset()->where("sous_categorie", ATF::db()->real_escape_string($sous_categorie), "AND", false, "LIKE")
										 ->where("id_categorie", ATF::db()->real_escape_string($categorie), "AND", false);
		$f = ATF::sous_categorie()->select_row();

		if($f){
			return $f["id_sous_categorie"];
		}else{
			print_r(array("sous_categorie"=>$sous_categorie, "id_categorie"=>$categorie));
			return ATF::sous_categorie()->i(array("sous_categorie"=>ATF::db()->real_escape_string($sous_categorie), "id_categorie"=>$categorie));
		}
	}
}

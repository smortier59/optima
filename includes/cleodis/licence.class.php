<?
class licence extends classes_optima {

	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'licence.licence' =>array("custom"=>true,"nosort"=>true,"align"=>"left"),
			//'licence.part_1',
			//'licence.part_2',
			'licence.id_licence_type',
			'licence.id_commande_ligne',
			'licence.deja_pris' => array("custom"=> true, "renderer"=>"licence_prise")
		);
		$this->fieldstructure();

		$this->foreign_key['id_licence_type'] =  "licence_type";
		$this->foreign_key['id_commande_ligne'] =  "commande_ligne";

		$this->colonnes['bloquees']['select'] = array("part_1", "part_2");
	}


	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){

		$this->q->addField("UPPER(CONCAT('****************************',`licence`.`part_2`))","licence.licence");
				//->addField("UPPER(`licence`.`part_1`)","licence.part_1")
				//->addField("UPPER(`licence`.`part_2`)","licence.part_2")
				//->from("licence","id_commande_ligne", "commande_ligne", "id_commande_ligne")
				//->from("commande_ligne","id_commande", "commande", "id_commande");

		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return["data"] as $key => $value) {
			if($value["licence.id_commande_ligne"]){
				$return["data"][$key]["deja_pris"] = true;
			}else{
				$return["data"][$key]["deja_pris"] = false;
			}
		}
		return $return;
	}


};
class licence_midas extends licence { };
class licence_cleodisbe extends licence { };
class licence_cap extends licence { };
class licence_bdomplus extends licence {


	/**
	 * Fonction appelée par la crontab controle_stock_licence, afin d'alerter si un seuil de licence limite est atteint (2000, 500, 100)
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 */
	public function controle_stock(){

		log::logger("============================================" , "controle_licence_bdomplus");
		log::logger("Controle du stock de licence" , "controle_licence_bdomplus");
		log::logger("============================================" , "controle_licence_bdomplus");

		ATF::licence_type()->q->reset();
		$licence_types = ATF::licence_type()->select_all();


		foreach ($licence_types as $key_type => $value_type) {
			log::logger("--------------------------------------------------" , "controle_licence_bdomplus");
			log::logger("Controle du nombre de licence pour ".$value_type["licence_type"] , "controle_licence_bdomplus");

			ATF::licence()->q->reset()->whereIsNull("id_commande_ligne")
									  ->where("id_licence_type", $value_type["id_licence_type"])
									  ->setCountOnly();
			$nb_licences = ATF::licence()->select_row();
			log::logger("-- Nombre de licences restantes --> ".$nb_licences , "controle_licence_bdomplus");

			$seuil = 0;
			if($nb_licences <= 100){
				$seuil = 100;
			}elseif($nb_licences <= 500){
				$seuil = 500;
			}elseif($nb_licences <= 2000){
				$seuil = 2000;
			}

			if($seuil){
				log::logger("-- Seuil atteint il reste moins de ".$seuil." licences dispos" , "controle_licence_bdomplus");

				$info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
			    $info_mail["recipient"] = __EMAIL_STOCK_LICENCE__;

			    $info_mail["html"] = true;

			    $info_mail["template"] = "alerte_stock_licence";

			    $info_mail["objet"] = "Abonnement ZEN – Alerte de seuil - ".$seuil." clés – Type de licence ".$value_type["licence_type"];

			    $info_mail["seuil"] = $seuil;
			    $info_mail["type_licence"] = $value_type["licence_type"];

			    $mail = new mail($info_mail);


			    if($mail->send()){
			    	log::logger("-- Envoi d'un mail pour alerter à ".__EMAIL_STOCK_LICENCE__ , "controle_licence_bdomplus");
			    }else{
			    	log::logger("-- Une erreur est survenue lors de l'envoi du mail pour alerter à ".__EMAIL_STOCK_LICENCE__ , "controle_licence_bdomplus");
			    }
			}

		}

		log::logger("============================================" , "controle_licence_bdomplus");
		log::logger("Fin du controle du stock" , "controle_licence_bdomplus");
		log::logger("============================================" , "controle_licence_bdomplus");
	}
};

class licence_boulanger extends licence { };

class licence_assets extends licence { };
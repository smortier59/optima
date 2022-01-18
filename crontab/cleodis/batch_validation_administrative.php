<?php
define("__BYPASS__",true);

include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT *
	 FROM `commande`, affaire
	 WHERE commande.id_affaire = affaire.id_affaire
	 AND affaire.pieces IS NULL
	 AND affaire.etat != 'terminee'
	 AND affaire.etat != 'perdue'
	 AND commande.etat != 'arreter'
	 AND commande.etat != 'AR'
	 AND commande.etat != 'non_loyer'
	 AND commande.etat != 'vente'";
$data = ATF::db()->sql2array($q);




foreach ($data as $key => $value) {
	try{

		$id_contact = ATF::societe()->select($value['id_societe'], 'id_contact_signataire');
		if(!$id_contact){
			ATF::contact()->q->reset()->where("id_societe",$value['id_societe']);
			$contacts = ATF::contact()->select_all();

			$id_contact = $contacts[0]['id_contact'];
		}

		ATF::db()->begin_transaction();


		ATF::affaire()->u(array('id_affaire'=>$value['id_affaire'], "pieces"=>'OK', 'date_verification'=>'2018-01-01'));

		$comite = array  (
		          "id_societe" => $value["id_societe"],
		          "id_affaire" => $value["id_affaire"],
		          "id_contact" => $id_contact,
		          "id_refinanceur" => 4,
		          "date" => date("01-01-Y"),
		          "description" => "Comité CLEODIS",
		          "etat" => "accepte",
		          "reponse" => NULL,
		          "validite_accord" => "2018-01-01",
		          "suivi_notifie"=>array(0=>"")
		      );

	    ATF::comite()->insert(array("comite"=>$comite));


	    ATF::affaire_etat()->insert(array(
		      "id_affaire"=>$value['id_affaire'],
		      "etat"=> 'valide_administratif',
		      "id_user"=>16
		  ));


	    ATF::db()->commit_transaction();
	}catch(errorATF $e){
		echo $e->getMessage();
		ATF::db()->rollback_transaction();
	}

	echo '.';
}
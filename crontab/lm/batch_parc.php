<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";

error_reporting(E_ALL);

include(dirname(__FILE__)."/../../global.inc.php");


ATF::affaire()->q->reset()->where("affaire.etat", "commande", "OR")
						  ->where("affaire.etat", "facture", "OR");

//Création des parcs pour toute les affaires ayant des BDC
try{
	ATF::begin_transaction();
	foreach(ATF::affaire()->sa() as $key=>$value){

		ATF::bon_de_commande()->q->reset()->where("id_affaire", $value["id_affaire"]);
		$bdc = ATF::bon_de_commande()->sa();

		if($bdc){
			foreach ($bdc as $kbdc => $vbdc) {
				ATF::bon_de_commande_ligne()->q->reset()->where("id_bon_de_commande", $vbdc["id_bon_de_commande"]);
				$lignes = ATF::bon_de_commande_ligne()->sa();

				if($lignes){
					foreach ($lignes as $kl => $vl) {
						$produit = ATF::produit()->select(ATF::commande_ligne()->select($vl["id_commande_ligne"], "id_produit"));

						//On insere uniquement des parcs dont le produit est de nature produit
						if($produit["nature"] == "produit"){

							$serial = $value['ref']."-".$vbdc["id_fournisseur"].'-';
							$serial = ATF::parc()->getMaxSerial($serial.$produit["id_produit"]."-");

							$parc = array(
											"id_societe"=> $value["id_societe"],
											"id_produit"=> $produit["id_produit"],
											"id_affaire"=> $value["id_affaire"],
											"ref"=> $vl["ref"],
											"libelle"=> $produit["produit"],
											"etat"=>"loue",
											"date_achat"=>$value["date"],
											"serial"=>$serial
										 );

							ATF::parc()->i($parc);

							$commande_ligne=ATF::commande_ligne()->select($vl["id_commande_ligne"]);
							$commande_ligne["serial"].=" ".$serial;
							ATF::commande_ligne()->u($commande_ligne);

						}

					}
				}
			}
		}

	}
	ATF::commit_transaction();
}catch(errorATF $e){
	echo $e->getMessage();
	ATF::rollback_transaction();
}

?>
<?
class facture_ligne_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	/*besoin d'un user pour les traduction*/
	function setUp() {
		$this->initUser();
//		ATF::pdf("absystech"); 

		$contact["nom"]="Tu_devis";
		$this->id_contact=ATF::contact()->insert($contact);
		$this->devis["devis"]["id_contact"]=$this->id_contact;
		$this->devis["devis"]['resume']='Tu_devis';
		$this->devis["devis"]['id_societe']=1;
		$this->devis["devis"]['validite']=date('Y-m-d');
		$this->devis["devis"]['prix']="200";
		$this->devis["devis"]['frais_de_port']="50";
		$this->devis["devis"]['prix_achat']="50";
		$this->devis["devis"]["date"]=date('Y-m-d');
		$this->devis["values_devis"]=array("produits"=>'[{"devis_ligne__dot__ref":"TU","devis_ligne__dot__produit":"Tu_devis","devis_ligne__dot__quantite":"15","devis_ligne__dot__poids":"10","devis_ligne__dot__prix":"10","devis_ligne__dot__prix_achat":"10","devis_ligne__dot__id_fournisseur":"1","devis_ligne__dot__id_compte_absystech":"1","devis_ligne__dot__marge":97.14,"devis_ligne__dot__id_fournisseur_fk":"1"}]');

		$this->id_devis = ATF::devis()->insert($this->devis,$this->s);
		$this->id_affaire = ATF::devis()->select($this->id_devis,"id_affaire");
	
		//Commande
		$commande["commande"]=$this->devis["devis"];
		$commande["commande"]["id_affaire"]=$this->id_affaire;
		$commande["commande"]["id_devis"]=$this->id_devis;
		
		//Commande_ligne
		$commande["values_commande"]=array("produits"=>'[{"commande_ligne__dot__ref":"TU","commande_ligne__dot__produit":"Tu_commande","commande_ligne__dot__quantite":"15","commande_ligne__dot__prix":"10","commande_ligne__dot__prix_achat":"10","commande_ligne__dot__id_fournisseur":"1","commande_ligne__dot__id_compte_absystech":"1","commande_ligne__dot__marge":97.14,"commande_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($commande["commande"]["id_contact"]);
		unset($commande["commande"]["validite"]);
		$this->id_commande = ATF::commande()->insert($commande,$this->s);
		$this->commande=$commande;
		$this->commande_ligne = ATF::commande_ligne()->select_special("id_commande",classes::decryptId($this->id_commande));

		//Facture
		$facture["facture"]=$this->commande["commande"];
		$facture["facture"]["id_affaire"]=$this->id_affaire;
		$facture["facture"]["tva"]=1.2;
		$facture["facture"]["id_termes"]=2;
		
		//Facture_ligne
		$facture["values_facture"]=array("produits"=>'[{"facture_ligne__dot__ref":"TU","facture_ligne__dot__produit":"Tu_facture","facture_ligne__dot__quantite":"15","facture_ligne__dot__prix":"10","facture_ligne__dot__prix_achat":"10","facture_ligne__dot__id_fournisseur":"1","facture_ligne__dot__serial":"777","facture_ligne__dot__id_compte_absystech":"1","facture_ligne__dot__marge":97.14,"facture_ligne__dot__id_fournisseur_fk":"1"}]');

		//Insertion
		unset($facture["facture"]["resume"]);
		unset($facture["facture"]["prix_achat"]);
		unset($facture["facture"]["id_devis"]);
		$this->facture=$facture;
		$this->id_facture = ATF::facture()->insert($facture,$this->s);

	}

	/*@author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr> */ 
	function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	function test_can_update(){
		try{
			$this->obj->can_update();
		}catch (error $e) {
			$error = $e->getMessage();
		}			
		$this->assertEquals($error , "Pour modifier une ligne de facture, il faut modifier dans la facture !!!" , "Pas de message d'erreur?? On ne peux pas update une ligne facture sans passer dans la facture normalement !!");
	}
	
	/**
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
	*/
	function test_can_insert(){
		try{
			$this->obj->can_insert();
		}catch (error $e) {
			$error = $e->getMessage();
		}			
		$this->assertEquals($error , "Pour inserer une ligne de facture, il faut modifier dans la facture !!!" , "Pas de message d'erreur?? On ne peux pas update une ligne facture sans passer dans la facture normalement !!");
	}	
}	
?>	
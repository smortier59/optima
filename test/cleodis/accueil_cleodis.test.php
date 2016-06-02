<?
class accueil_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_widgets(){

		ATF::$usr->set("id_user",94);

		$this->assertEquals(
			'a:4:{i:0;a:3:{s:6:"module";s:5:"devis";s:4:"type";s:3:"o2m";s:9:"id_agence";s:1:"1";}i:1;a:3:{s:6:"module";s:8:"commande";s:4:"type";s:3:"o2m";s:9:"id_agence";s:1:"1";}i:2;a:3:{s:6:"module";s:8:"commande";s:4:"type";s:5:"autre";s:9:"id_agence";s:1:"1";}i:3;a:3:{s:6:"module";s:5:"devis";s:4:"type";s:5:"autre";s:9:"id_agence";s:1:"1";}}'
			,serialize(ATF::accueil()->getWidgets())
			,"Les widgets ont change"
		);
	}

	/* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
	public function test_widgets_cleodisbe(){
		ATF::db()->select_db("extranet_v3_cleodisbe");
		$c = new accueil_cleodisbe();	
		
		$this->assertEquals(
			'a:0:{}'
			,serialize($c->getWidgets())
			,"Les widgets ont change"
		);


		ATF::db()->select_db("extranet_v3_cleodis");		
	}

	public function test_type_agence(){
		$this->assertEquals(array('type'=>'o2m','id_agence'=>'1'),$this->obj->type_agence("o2m,id_agence=1"), "Retour incorrect");	
	}
};
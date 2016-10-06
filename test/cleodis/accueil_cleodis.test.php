<?
class accueil_cleodis_test extends ATF_PHPUnit_Framework_TestCase {
	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_widgets(){

		ATF::$usr->set("id_user",16);

		$this->assertEquals(
			'a:0:{}'
			,serialize(ATF::accueil()->getWidgets())
			,"Les widgets ont change"
		);



	}

	/* @author Yann GAUTHERON <ygautheron@absystech.fr> */
	public function test_get_agence(){

		ATF::$usr->set("id_user",16);

		$this->assertEquals(
			array(1 , 3)
			,ATF::accueil()->getAgence()
			,"Erreur get_agence"
		);

		ATF::$usr->set("id_user",21);

		$this->assertEquals(
			array(1)
			,ATF::accueil()->getAgence()
			,"Erreur get_agence"
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
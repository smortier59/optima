<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class stats_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	public function testConstructEtInitilisation(){
		$c_stats=new stats();

		$this->assertTrue(is_array($c_stats->liste_annees),"Les années ne sont pas retournées");	
		$this->assertEquals(count($c_stats->stats),count($c_stats->liste_annees),"Il n'y a pas le même nombre de données");
		//vérification du contenu
		$this->assertEquals(1,$c_stats->liste_annees['tache'][date('Y')],"La liste des années est incorrecte");
	}
	
	public function testModif_liste_annee(){
		$c_stats_modif=new stats();
		$this->assertEquals(1,count($c_stats_modif->liste_annees['tache']),"Tache a déjà plusieurs années");
		$c_stats_modif->modif_liste_annee("ajout","tache",date("Y",strtotime("-1 year")));
		$this->assertEquals(2,count($c_stats_modif->liste_annees['tache']),"Tache a toujours une seule année");
		$this->assertEquals(1,$c_stats_modif->liste_annees['tache'][date('Y')],"L'année ".date('Y')." n'est pas prise en compte");
		$this->assertEquals(1,$c_stats_modif->liste_annees['tache'][date("Y",strtotime("-1 year"))],"L'année ".date("Y",strtotime("-1 year"))." n'est pas prise en compte");
	
		//si on supprime l'année courante
		$c_stats_modif->modif_liste_annee("autre","tache",date("Y"));
		$this->assertEquals(2,count($c_stats_modif->liste_annees['tache']),"Tache n'a pas le bon nombre d'année");
		$this->assertEquals(0,$c_stats_modif->liste_annees['tache'][date('Y')],"L'année ".date('Y')." est toujours prise en compte");
		$this->assertEquals(1,$c_stats_modif->liste_annees['tache'][date("Y",strtotime("-1 year"))],"L'année ".date("Y",strtotime("-1 year"))." n'est plus prise en compte");
	}
	
	public function testCreateMenu(){
		$this->initUserOnly(false);
		
		//creation d'element pour faire les tests
		$this->obj->stats=array("hotline_interaction"=>array(
														"multigraphe"=>"true"
														,'couleur'=>"bleu"
														,'taille'=>"100px"
														,"graphes"=>array("graph1"=>array("numero"=>1,"taille"=>"100px","couleur"=>"bleu")
																		,"graph2"=>array("numero"=>2,"taille"=>"150px","couleur"=>"bleu"))
													)
								,"hotline"=>array("taille"=>"600px","couleur"=>"bleu"));
		
		$menu=$this->obj->createMenu();
		
		$this->assertEquals(2,count($menu),"Le menu ne contient pas les bonnes données");
		$this->assertEquals("hotline_interaction",$menu[0]['module'],"Le module retourné n'est pas correcte");
		$this->assertTrue(isset($menu[0]['enfants']),"L'option multigraphe n'a pas été prise en compte");
		$this->assertEquals("hotline",$menu[1]['graphe'],"Le graphe retourné n'est pas correcte");
		$this->assertFalse(isset($menu[1]['enfants']),"Hotline ne devrait pas avoir d'enfant");
	}
	
		
	public function testConditionYearSimple(){
		ATF::tache()->q->reset();
		$this->obj->conditionYearSimple(ATF::tache()->q,"horaire_fin","2011");
		$tabyear=ATF::tache()->q->getWhere();
		$this->assertEquals("YEAR(horaire_fin) = '2011'",$tabyear["YEAR(horaire_fin)"],"La condition n'est pas correcte");
	}
	
	public function testConditionYear(){
		ATF::tache()->q->reset();
		$this->obj->conditionYear(array("2009"=>1,"2010"=>0,"2011"=>1),ATF::tache()->q,"horaire_fin","autre");
		$tabyear=ATF::tache()->q->getWhere();
		$this->assertEquals("YEAR(horaire_fin) = '2009' OR YEAR(horaire_fin) = '2011'",$tabyear["YEAR(horaire_fin)"],"La condition n'est pas correcte");
	}

	public function testIntitule(){
		$tab=$this->obj->intitule("6",2011,NULL);
		$this->assertTrue(($tab['int']==$tab['lib']?($tab['lib']==$tab['lab']?true:false):false),"Le label n'est pas utilisé pour chaque élément");
	}

	public function testRecupMois(){
		$liste_mois=$this->obj->recupMois("users");
		$liste_cle_mois=array_keys($liste_mois);
		$this->assertEquals(12,count($liste_mois),"Le nombre de mois est incorrecte");
		$this->assertEquals("Juillet",$liste_mois['07'],"Le mois ne corresponds pas au chiffre");
		$this->assertEquals("01",$liste_cle_mois[0],"Le premier mois n'est pas bon");		
		$this->assertEquals("07",$liste_cle_mois[6],"Le mois de Juillet est mal positionné");
	}

	public function testInitGraphe(){
		$graph=array();
		$this->obj->initGraphe($graph,"lol","users");
		$this->assertEquals(12,count($graph['dataset']["lol"]["set"]),"Le nombre de mois est incorrecte");
		$liste=array_keys($graph['dataset']["lol"]["set"]);
		$this->assertEquals("01",$liste[0],"Le premier mois devrait être Janvier");
	}
	
	public function testReturnCat(){
		$cat=$this->obj->returnCat(10,2011);
		$liste=array_keys($cat);

		$this->assertEquals(12,count($cat),"Le nombre de catégorie est incorrecte");
		$this->assertEquals("01",$liste[0],"Le premier mois devrait être Janvier");
	}

};
?>
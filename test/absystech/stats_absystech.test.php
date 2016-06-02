<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class stats_absystech_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	public function testConstructEtInitilisation(){
		$c_stats=new stats_absystech();

		$this->assertTrue(is_array($c_stats->liste_annees),"Les années ne sont pas retournées");	
		$this->assertEquals(count($c_stats->stats),count($c_stats->liste_annees),"Il n'y a pas le même nombre de données");
		//vérification du contenu
		$this->assertEquals(1,$c_stats->liste_annees['hotline'][date('Y')],"La liste des années est incorrecte");
	}
	
	public function testConditionYearSimple(){
		$c_stats=new stats_att();
		ATF::tache()->q->reset();
		$c_stats->conditionYearSimple(ATF::tache()->q,"horaire_fin","2011");
		$tabyear=ATF::tache()->q->getWhere();
		$this->assertTrue(isset($tabyear["annee_2011"]),"Le nom de la condition a changé ou la condition n'est pas présente");
		$this->assertEquals("horaire_fin >= '2011-07-01' AND horaire_fin <= '2012-06-30'",$tabyear["annee_2011"],"L'intervalle a changé ou n'est plus pris en compte");	
	}
	
	public function testConditionYear(){
		$c_stats=new stats_att();
		
		//check de la première condition
		ATF::tache()->q->reset();
		$c_stats->conditionYear(array("2009"=>1,"2010"=>0,"2011"=>1),ATF::tache()->q,"horaire_fin","autre");
		$tabyear=ATF::tache()->q->getWhere();
		$this->assertEquals(1,count($tabyear),"Le nombre de condition est inexacte");
		$this->assertEquals("(horaire_fin >= '2009-07-01' AND horaire_fin <= '2010-06-30') OR (horaire_fin >= '2011-07-01' AND horaire_fin <= '2012-06-30')",trim($tabyear[1]),"Les conditions sont fausses");	
	
		//check de la seconde condition
		ATF::tache()->q->reset();
		$c_stats->conditionYear(array("2011"=>1),ATF::tache()->q,"horaire_fin","users");
		$tabyear2=ATF::tache()->q->getWhere();
		$this->assertEquals("YEAR(horaire_fin) = '2011'",$tabyear2["YEAR(horaire_fin)"],"La condition est incorrecte");		
	}
	
	public function testIntitule(){
		$c_stats=new stats_att();
		
		//pas dans la condition
		$tab=$c_stats->intitule("6",2011,NULL);
		$this->assertTrue(($tab['int']==$tab['lib']?($tab['lib']==$tab['lab']?true:false):false),"Le label n'est pas utilisé pour chaque élément");
	
		//test de la condition
		$tab2=$c_stats->intitule("6",NULL,2011);		
		$this->assertEquals("2010/2011",$tab2['int'],"1/ L'intitule est faux");
		$this->assertEquals("2011-6",$tab2['lib'],"1/ Le libelle est faux");
		$this->assertEquals("2011",$tab2["lab"],"1/ Le label est faux");
	
		//seconde condition
		$tab3=$c_stats->intitule("8",NULL,2011);
		$this->assertEquals("2011/2012",$tab3['int'],"2/ L'intitule est faux");
		$this->assertEquals("2011-8",$tab3['lib'],"2/ Le libelle est faux");
		$this->assertEquals("2011",$tab3["lab"],"2/ Le label est faux");

	}
	
	public function testRecupMois(){
		$c_stats=new stats_att();

		$liste_mois=$c_stats->recupMois("autre");
		$liste_cle_mois=array_keys($liste_mois);
		$this->assertEquals(12,count($liste_mois),"Le nombre de mois est incorrecte");
		$this->assertEquals("Juillet",$liste_mois['07'],"Le mois ne corresponds pas au chiffre");
		$this->assertEquals("07",$liste_cle_mois[0],"Le premier mois n'est pas bon");		
		$this->assertEquals("01",$liste_cle_mois[6],"Le mois de Janvier est mal positionné");
		
		$liste_mois2=$c_stats->recupMois("users");
		$liste_cle_mois2=array_keys($liste_mois2);
		$this->assertEquals(12,count($liste_mois2),"2/ Le nombre de mois est incorrecte");
		$this->assertEquals("Juillet",$liste_mois2['07'],"2/ Le mois ne corresponds pas au chiffre");
		$this->assertEquals("01",$liste_cle_mois2[0],"2/ Le premier mois n'est pas bon");		
		$this->assertEquals("07",$liste_cle_mois2[6],"Le mois de Juillet est mal positionné");
	}
	
	public function testInitGraphe(){
		$c_stats=new stats_att();
		
		$graph=array();
		$c_stats->initGraphe($graph,"lol","users");
		$this->assertEquals(12,count($graph['dataset']["lol"]["set"]),"Le nombre de mois est incorrecte");
		$liste=array_keys($graph['dataset']["lol"]["set"]);
		$this->assertEquals("01",$liste[0],"Le premier mois devrait être Janvier");

		$graph2=array();
		$c_stats->initGraphe($graph2,"lol","autre");
		$this->assertEquals(12,count($graph2['dataset']["lol"]["set"]),"2/ Le nombre de mois est incorrecte");
		$liste2=array_keys($graph2['dataset']["lol"]["set"]);
		$this->assertEquals("07",$liste2[0],"Le premier mois devrait être Juillet");
	}
	
	public function testReturnCat(){
		$c_stats=new stats_att();
		
		$cat=$c_stats->returnCat();
		$liste=array_keys($cat);

		$this->assertEquals(12,count($cat),"Le nombre de catégorie est incorrecte");
		$this->assertEquals("07",$liste[0],"Le premier mois devrait être Juillet");
	}
	
};
?>
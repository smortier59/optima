<?
/* @author Nicolas BERTEMONT <nbertemont@absystech.fr> */
class gep_projet_test extends ATF_PHPUnit_Framework_TestCase {
	public function setUp(){
		ATF::db()->begin_transaction(true);
	}
	
	public function tearDown(){
		ATF::db()->rollback_transaction(true);
	}
	
	//---------Méthodes permettant la création d'éléments nécessaire aux tests -----------
	public function createPointSsHot(){
		$this->initUserOnly(false);
		//création d'un projet de test
		$id_gep_projet=ATF::gep_projet()->i(array('gep_projet'=>"lol","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d",strtotime("+3day"))));
		$this->assertTrue(is_numeric($id_gep_projet),"Création du projet echouee");
		//création des pointages
		//sans hotline
		$id_pointage=ATF::pointage()->i(array("id_user"=>$this->id_user,"date"=>date("Y-m-d"),"temps"=>"01:00:00","id_gep_projet"=>$id_gep_projet));
		$this->assertTrue(is_numeric($id_pointage),"Création du pointage echouee");
		
		return $id_gep_projet;
	}
	
	public function createPointAvecHot($id_gep_projet){
		//avec hotline
		//création d'une societe bidon
		$id_societe=ATF::societe()->i(array("societe"=>"mdr"));
		$this->assertTrue(is_numeric($id_societe),"Création de la societe echouee");
		//creation d'une hotline bidon
		$id_hotline=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"ptdr"));
		$this->assertTrue(is_numeric($id_hotline),"Création de la hotline echouee");
		//creation du new pointage
		$id_pointage_hot=ATF::pointage()->i(array("id_user"=>$this->id_user,"date"=>date("Y-m-d"),"temps"=>"00:30:00","id_gep_projet"=>$id_gep_projet,"id_hotline"=>$id_hotline));
		$this->assertTrue(is_numeric($id_pointage_hot),"Création du pointage hotline echouee");
	}
	//------------------------------------------------------------------------------------
		
	public function testSec_to_time(){
		$temps=$this->obj->sec_to_time(5400);
		$this->assertEquals("1h30",$temps,"1/ Le temps renvoyé n'est pas correct");
		
		$temps2=$this->obj->sec_to_time(65);
		$this->assertEquals("0h01",$temps2,"2/ Le temps renvoyé n'est pas correct");
	}
	
	public function testDetail_projet(){
		//sans hotline
		$id_gep_projet=$this->createPointSsHot();
		
		$donnees=$this->obj->detail_projet($id_gep_projet);
		$this->assertEquals(1,count($donnees),"1/ Ne récupère pas le bon nombre de données");
		$this->assertEquals($this->id_user,$donnees[0]['pointage.id_user'],"1/ L'id user renvoyé n'est pas correct");
		$this->assertEquals("1h00",$donnees[0]['temps_total'],"1/ Le temps total renvoyé n'est pas correct");
		$this->assertEquals("0h00",$donnees[0]['temps_hot'],"1/ Le temps hotline renvoyé n'est pas correct");
		
		//avec hotline
		$this->createPointAvecHot($id_gep_projet);
		
		$donnees_hot=$this->obj->detail_projet($id_gep_projet);
		$this->assertEquals(1,count($donnees_hot),"2/ Ne récupère pas le bon nombre de données");
		$this->assertEquals($this->id_user,$donnees_hot[0]['pointage.id_user'],"2/ L'id user renvoyé n'est pas correct");
		$this->assertEquals("1h30<br />(0h30)",$donnees_hot[0]['temps_total'],"2/ Le temps total renvoyé n'est pas correct");
		$this->assertEquals("0h30",$donnees_hot[0]['temps_hot'],"2/ Le temps hotline renvoyé n'est pas correct");
	}
	
	public function testTps_total_projet(){
		//création des éléments de tests
		$id_gep_projet=$this->createPointSsHot();
		$this->createPointAvecHot($id_gep_projet);
		
		$donnees=$this->obj->tps_total_projet($id_gep_projet);
		$this->assertEquals(5400,$donnees[0],"Le temps total en secondes n'est pas correct");
		$this->assertEquals("1h30",$donnees['temps_total'],"Le temps total en heure n'est pas correct");
		$this->assertEquals(1800,$donnees[1],"Le temps total en secondes des hotlines n'est pas correct");
		$this->assertEquals("0h30",$donnees['temps_hot'],"Le temps total en heures des hotlines n'est pas correct");
	}

	//test également du liste_user car appelé dans cette méthode
	public function testDetail_projet_par_mois(){
		//création des éléments de tests
		$id_gep_projet=$this->createPointSsHot();
		$this->createPointAvecHot($id_gep_projet);
		//création d'une hotline supplémentaire pour avoir plusieurs echantillon de date
		$id_pointage_date=ATF::pointage()->i(array("id_user"=>$this->id_user,"date"=>date("Y-m-d",strtotime("+1month +1day")),"temps"=>"00:45:00","id_gep_projet"=>$id_gep_projet));
		$this->assertTrue(is_numeric($id_pointage_date),"Création du pointage avec date différente echouee");
		//ajout d'un pointage avec mon user, pour afficher mon user avec valeur nulle sur les autres mois que celui précisé
		$id_pointage_moi=ATF::pointage()->i(array("id_user"=>23,"date"=>date("Y-m-d",strtotime("+1month +1day")),"temps"=>"00:01:00","id_gep_projet"=>$id_gep_projet));
		$this->assertTrue(is_numeric($id_pointage_moi),"Création de mon pointage avec date différente echouee");
		
		$donnees=$this->obj->detail_projet_par_mois($id_gep_projet);
		$this->assertTrue(count($donnees)==2,"Le nombre de données retournées n'est pas correct");
		$this->assertTrue(is_array($donnees[date("Y-m")]),"La structure des données n'est pas correcte");
		$this->assertEquals("1h30<br />(0h30)",$donnees[date("Y-m")][$this->id_user],"Le temps total pour le mois courant n'est pas correct");
		$this->assertEquals("0h45",$donnees[date("Y-m",strtotime("+1month +1day"))][$this->id_user],"Le temps total pour le mois suivant n'est pas correct");
		//test de l'ajout de mon user avec valeur nulle sur le mois courant
		$this->assertEquals(0,$donnees[date("Y-m")][23],"Le temps total pour le mois courant de mon user n'est pas correct, prob de la methode liste_user ?");
		$this->assertEquals("0h01",$donnees[date("Y-m",strtotime("+1month +1day"))][23],"Le temps total pour le mois suivant de mon user n'est pas correct");
	}
	
	public function testTps_projet_mois(){
		//élément de tests
		$id_gep_projet=$this->createPointSsHot();
		
		$donnees=$this->obj->tps_projet_mois($id_gep_projet);
		$this->assertTrue(count($donnees)==1,"Le nombre de données retournées (sans hotline) n'est pas correct");
		$this->assertEquals("1h00",$donnees[date("Y-m")],"Le temps total (sans hotline) pour le mois courant n'est pas correct");
		
		//élément de tests
		$this->createPointAvecHot($id_gep_projet);
		
		$donnees_avec_hot=$this->obj->tps_projet_mois($id_gep_projet);
		$this->assertTrue(count($donnees_avec_hot)==1,"Le nombre de données retournées (avec hotline) n'est pas correct");
		$this->assertEquals("1h30<br />(0h30)",$donnees_avec_hot[date("Y-m")],"Le temps total (avec hotline) pour le mois courant n'est pas correct");
	}
	
	/************** STATISTIQUE  **************/
	public function initDonnees(){
		$this->initUserOnly(false);
		//vidage la table des interactions pour ne pas avoir de problème
		ATF::hotline_interaction()->q->reset()->addConditionNotNull("id_hotline_interaction");
		ATF::hotline_interaction()->delete();
		
		//création de 7 projets
		$this->id_projet=$this->obj->i(array("gep_projet"=>"lol","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		$this->id_projet2=$this->obj->i(array("gep_projet"=>"lol2","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		$this->id_projet3=$this->obj->i(array("gep_projet"=>"lol3","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		$this->id_projet4=$this->obj->i(array("gep_projet"=>"lol4","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		$this->id_projet5=$this->obj->i(array("gep_projet"=>"lol5","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		$this->id_projet6=$this->obj->i(array("gep_projet"=>"lol6","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		$this->id_projet7=$this->obj->i(array("gep_projet"=>"lol7","date_debut"=>date("Y-m-d"),"date_fin"=>date("Y-m-d")));
		
		//création d'une société
		$id_societe=ATF::societe()->i(array('societe'=>'soc lol'));
		
		//création des 6 hotlines
		$this->id_hotline=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol","id_gep_projet"=>$this->id_projet));
		$this->id_hotline2=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol2","id_gep_projet"=>$this->id_projet2));
		$this->id_hotline3=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol3","id_gep_projet"=>$this->id_projet3));
		$this->id_hotline4=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol4","id_gep_projet"=>$this->id_projet4));
		$this->id_hotline5=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol5","id_gep_projet"=>$this->id_projet5));
		$this->id_hotline6=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol6","id_gep_projet"=>$this->id_projet6));
		$this->id_hotline7=ATF::hotline()->i(array("id_societe"=>$id_societe,"hotline"=>"hot lol7","id_gep_projet"=>$this->id_projet7));
		
		//creation de 6 interactions
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline,"temps_passe"=>"01:00:00",'id_user'=>$this->id_user));
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline2,"temps_passe"=>"03:00:00",'id_user'=>$this->id_user));
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline3,"temps_passe"=>"05:00:00",'id_user'=>$this->id_user));
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline4,"temps_passe"=>"02:00:00","date"=>date("Y-m-d H:i:s",strtotime("-12 week")),'id_user'=>$this->id_user));
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline5,"temps_passe"=>"04:00:00","date"=>date("Y-m-d H:i:s",strtotime("-13 week")),'id_user'=>$this->id_user));
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline6,"temps_passe"=>"06:00:00","date"=>date("Y-m-d H:i:s",strtotime("-12 month")),'id_user'=>$this->id_user));
		ATF::hotline_interaction()->i(array("id_hotline"=>$this->id_hotline7,"temps_passe"=>"07:00:00","date"=>date("Y-m-d H:i:s",strtotime("-13 month")),'id_user'=>$this->id_user));
	}
	
	public function testSetProjet(){
		$this->initDonnees();
		$liste=$this->obj->setProjet();
		$this->assertEquals(5,count($liste),"Le nombre de projet retourné est incorrecte");
		$this->assertEquals($this->id_projet6,$liste[0]['id_projet'],"Le projet le plus conséquent n'est pas le bon");	
		$this->assertEquals("0.7 / 0.7",$liste[1]['tot'],"Le second total est incorrecte");
		$this->assertEquals($this->id_projet5,$liste[2]['id_projet'],"Le second projet le plus conséquent n'est pas le bon");
		$this->assertEquals("0.4 / 0.4",$liste[3]['tot'],"Le 4e total est incorrecte");
		$this->assertEquals($this->id_projet4,$liste[4]['id_projet'],"Le dernier projet le plus conséquent n'est pas le bon");
		
		//test avec le pas d'une semaine
		$this->obj->pas="semaine";
		$liste2=$this->obj->setProjet();
		$this->assertEquals(4,count($liste2),"(semaine) Le nombre de projet retourné est incorrecte");
		$this->assertEquals($this->id_projet3,$liste2[0]['id_projet'],"(semaine) Le projet le plus conséquent n'est pas le bon");	
		$this->assertEquals("0.4 / 0.4",$liste2[1]['tot'],"(semaine) Le second total est incorrecte");
		$this->assertEquals($this->id_projet4,$liste2[2]['id_projet'],"(semaine) Le second projet le plus conséquent n'est pas le bon");
		$this->assertEquals("0.1 / 0.1",$liste2[3]['tot'],"(semaine) Le 4e total est incorrecte");
		
	}
	
	public function testFetchLabelsByWeek(){
		$this->obj->fetchLabelsByWeek(3,$graph);
		$this->assertEquals(12,count($graph['categories']['category']),"1/ Le nombre de semaine retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['categories']['category']);
		$this->assertEquals(44,$cat[0],"La semaine 44 n'a pas été prise en compte");
		$this->assertEquals(3,$cat[11],"La semaine 3 n'a pas été prise en compte");
		
		$graph=array();
		$this->obj->fetchLabelsByWeek(20,$graph);
		$this->assertEquals(12,count($graph['categories']['category']),"2/ Le nombre de semaine retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['categories']['category']);
		$this->assertEquals(9,$cat[0],"La semaine 9 n'a pas été prise en compte");
		$this->assertEquals(20,$cat[11],"La semaine 20 n'a pas été prise en compte");	
	}
	
	public function testFetchLabelsByMonth(){
		$this->obj->fetchLabelsByMonth(strtotime("2011-03-25"),$graph);
		$this->assertEquals(12,count($graph['categories']['category']),"1/ Le nombre de mois retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['categories']['category']);
		$this->assertEquals(4,$cat[0],"Le mois 4 n'a pas été prise en compte");
		$this->assertEquals(3,$cat[11],"Le mois 3 n'a pas été prise en compte");
		$this->assertEquals("Mars (11)",$graph['categories']['category'][3]['label'],"Le label du mois est incorrecte");

		$graph=array();
		$this->obj->fetchLabelsByMonth(strtotime("2011-12-25"),$graph);
		$this->assertEquals(12,count($graph['categories']['category']),"2/ Le nombre de mois retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['categories']['category']);
		$this->assertEquals(1,$cat[0],"Le mois 1 n'a pas été prise en compte");
		$this->assertEquals(12,$cat[11],"Le mois 12 n'a pas été prise en compte");
				
	}
	
	public function testFetchValuesByWeek(){
		$this->obj->fetchValuesByWeek(3,$graph,array("gep_projet"=>"lol"));
		$this->assertEquals(12,count($graph['dataset']["lol"]['set']),"1/ Le nombre de semaine retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['dataset']["lol"]['set']);
		$this->assertEquals("44",$cat[0],"La semaine 44 n'a pas été prise en compte");
		$this->assertEquals("03",$cat[11],"La semaine 3 n'a pas été prise en compte");
		$this->assertEquals("lol : 0",$graph['dataset']["lol"]['set']["03"]['titre'],"Le titre de la semaine est incorrecte");

		$graph=array();
		$this->obj->fetchValuesByWeek(20,$graph,array("gep_projet"=>"lol"));
		$this->assertEquals(12,count($graph['dataset']["lol"]['set']),"2/ Le nombre de semaine retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['dataset']["lol"]['set']);
		$this->assertEquals("09",$cat[0],"La semaine 9 n'a pas été prise en compte");
		$this->assertEquals("20",$cat[11],"La semaine 20 n'a pas été prise en compte");	
	}
	
	public function testFetchValuesByMonth(){
		$this->obj->fetchValuesByMonth(3,$graph,array("gep_projet"=>"lol"));
		$this->assertEquals(12,count($graph['dataset']["lol"]['set']),"1/ Le nombre de mois retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['dataset']["lol"]['set']);
		$this->assertEquals(4,$cat[0],"Le mois 4 n'a pas été prise en compte");
		$this->assertEquals(3,$cat[11],"Le mois 3 n'a pas été prise en compte");
		$this->assertEquals("lol : 0",$graph['dataset']["lol"]['set'][3]['titre'],"Le titre du mois est incorrecte");

		$graph=array();
		$this->obj->fetchValuesByMonth(12,$graph,array("gep_projet"=>"lol"));
		$this->assertEquals(12,count($graph['dataset']["lol"]['set']),"2/ Le nombre de mois retourné est incorrecte");
		//on vérifie les extrémités
		$cat=array_keys($graph['dataset']["lol"]['set']);
		$this->assertEquals(1,$cat[0],"Le mois 1 n'a pas été prise en compte");
		$this->assertEquals(12,$cat[11],"Le mois 12 n'a pas été prise en compte");
	}

	public function testStats(){
		$this->initDonnees();
		
		//pas de 1 mois
		$stats=$this->obj->stats(array(0=>array("id_projet"=>$this->id_projet),1=>array("id_projet"=>$this->id_projet2)));
	
		//check de la méthode ajoutDonnees
		$this->assertEquals(2,count($stats['dataset']),'Problème de récupération des données à afficher');
		//check de la méthode paramGraphe
		$this->assertTrue(is_array($stats['params']),'Problème de récupération des params');
		//le nombre de valeur dans les set de data doit être égal au nombre de catégorie
		$this->assertTrue(isset($stats['dataset']['lol']),'Problème de récupération des dataset');
		$this->assertEquals('lol',$stats['dataset']['lol']['params']['seriesname'],'Problème de params dans les données');
		$this->assertEquals(count($stats['categories']['category']),count($stats['dataset']['lol']['set']),"Le nombre de données à afficher en abscisse n'est pas correct");

		//test du contenu
		if(date("m")<3)$annee=date("Y")-1;
		else $annee=date("Y");
		$this->assertEquals("Mars (".substr($annee,-2).")",$stats['categories']['category']['3']['label'],"Le nom des catégories n'est pas correct");
		$this->assertEquals("Charge par projet",$stats['params']['caption'],"1/Le nom des légendes n'est pas correct");
		$this->assertEquals("Temps passe (pas mois)",$stats['params']['yaxisname'],"2/Le nom des légendes n'est pas correct");
		$this->assertEquals("0.1",$stats['dataset']["lol"]['set'][intval(date('m'))]['value'],'1/ Les valeurs ne sont pas correctes');
		$this->assertEquals("lol : 0.1 jour(s)",$stats['dataset']["lol"]['set'][intval(date('m'))]['titre'],'1/ Les titres ne sont pas correctes');
		$this->assertEquals("0.4",$stats['dataset']["lol2"]['set'][intval(date('m'))]['value'],'2/ Les valeurs ne sont pas correctes');
		$this->assertEquals("lol2 : 0.4 jour(s)",$stats['dataset']["lol2"]['set'][intval(date('m'))]['titre'],'2/ Les titres ne sont pas correctes');

		//pas d'une semaine
		$this->obj->pas="semaine";
		$this->obj->projet=array($this->id_projet=>1,$this->id_projet2=>1);
		$stats_sem=$this->obj->stats();
		
		$this->assertEquals(2,count($stats_sem['dataset']),'semaine/ Problème de récupération des données à afficher');
		$this->assertTrue(is_array($stats_sem['params']),'semaine/ Problème de récupération des params');
		$this->assertTrue(isset($stats_sem['dataset']['lol']),'semaine/ Problème de récupération des dataset');
		$this->assertEquals('lol',$stats_sem['dataset']['lol']['params']['seriesname'],'semaine/ Problème de params dans les données');
		//$this->assertEquals(count($stats_sem['categories']['category'])-1,count($stats_sem['dataset']['lol']['set']),"semaine/ Le nombre de données à afficher en abscisse n'est pas correct");
	
		//test du contenu
		$this->assertEquals(date('W'),$stats_sem['categories']['category'][(date('W')<10?substr(date('W'),-1):date('W'))]['label'],"semaine/ Le nom des catégories n'est pas correct");
		$this->assertEquals("Charge par projet",$stats_sem['params']['caption'],"semaine/ 1/Le nom des légendes n'est pas correct");
		$this->assertEquals("Temps passe (pas semaine)",$stats_sem['params']['yaxisname'],"semaine/ 2/Le nom des légendes n'est pas correct");
		$this->assertEquals("0.1",$stats_sem['dataset']["lol"]['set'][date('W')]['value'],'semaine/ 1/ Les valeurs ne sont pas correctes');
		$this->assertEquals("lol (".date("d/m/Y")." au ".date("d/m/Y").") : 0.1 jour(s)",$stats_sem['dataset']["lol"]['set'][date('W')]['titre'],'semaine/ 1/ Les titres ne sont pas correctes');
		$this->assertEquals("0.4",$stats_sem['dataset']["lol2"]['set'][date('W')]['value'],'semaine/ 2/ Les valeurs ne sont pas correctes');
		$this->assertEquals("lol2 (".date("d/m/Y")." au ".date("d/m/Y").") : 0.4 jour(s)",$stats_sem['dataset']["lol2"]['set'][date('W')]['titre'],'semaine/ 2/ Les titres ne sont pas correctes');

		ATF::$msg->getNotices();
	}

	public function testMajTpsProjet(){
		$this->initDonnees();
		
		//pas d'un mois
		$this->obj->projet=array($this->id_projet=>1,$this->id_projet2=>1);
		$this->obj->majTpsProjet();

		$this->assertEquals("0.1 / 0.1",$this->obj->projet[$this->id_projet],"1/ Le temps retourné est incorrect");
		$this->assertEquals("0.4 / 0.4",$this->obj->projet[$this->id_projet2],"2/ Le temps retourné est incorrect");
		
		//pas d'une semaine
		$this->obj->projet=array($this->id_projet3=>1,$this->id_projet4=>1);
		$this->obj->majTpsProjet("semaine");
		
		$this->assertEquals("0.7 / 0.7",$this->obj->projet[$this->id_projet3],"3/ Le temps retourné est incorrect");
		$this->assertEquals("0.3 / 0.3",$this->obj->projet[$this->id_projet4],"4/ Le temps retourné est incorrect");

		ATF::$msg->getNotices();		
	}
	
	public function testModifStatProjet(){
		$this->initDonnees();
		$this->obj->modifStatProjet(array("projet"=>$this->id_projet2,"ajout"=>true));
		$this->assertEquals("0.4 / 0.4",$this->obj->projet[$this->id_projet2],"Le temps retourné est incorrect");
		$this->obj->modifStatProjet(array("projet"=>$this->id_projet2,"pas"=>"semaine"));
		$this->assertFalse(isset($this->obj->projet[$this->id_projet2]),"L'element ne devrait plus être présent");
		$this->assertEquals("semaine",$this->obj->pas,"Le pas n'a pas changé");
		ATF::$msg->getNotices();
		
	}
};
?>
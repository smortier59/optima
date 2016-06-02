<?
/**
* Gestion du pointage
* @package Optima
*/
require_once dirname(__FILE__)."/../pointage.class.php";
class pointage_demo extends pointage { 
	/**
	* Contructeur
	*/
	public function __construct() {
		parent::__construct();
		
		$this->addPrivilege("planification","update");
		$this->addPrivilege("getPointageByUser");
		$this->addPrivilege("updateTemps","update");
		$this->addPrivilege("updateTache","update");
		$this->addPrivilege("exportXLS","update");
	}	
	
	public function getPointageByUser($infos) {
		
		$date = mktime(0,0,0,$infos['month']+2,0,$infos['year']);
		$this->q->reset()
					->addField('pointage.date','date')
					->addField('pointage.id_user','id_user')
					->addField('pointage.id_pointage_tache','id_pointage_tache')
					->addField('user.login')
					->addField('SUM(temps)','temps')
					->from('pointage','id_user','user','id_user')
					->where('pointage.date',date("Y-m",$date)."-%","OR", false, "LIKE")
					->where('pointage.daynight',$infos['daynight'])
					->addGroup('id_user')
					->addGroup('date')
					->addOrder("nom","asc");
		
//		$this->q->setToString();
//		log::logger($this->sa(),'qjanon');
//		$this->q->unsetToString();
		$sa = $this->sa();
		$total = 1;
		foreach ($sa as $k=>$i) {
			$date = explode("-",$i['date']);
			
			$return[$i['id_user']]["matricule"] = $i['user.login'];
			$return[$i['id_user']]["user"] = ATF::user()->nom($i['id_user']);
			$return[$i['id_user']]["id_user"] = ATF::user()->cryptId($i['id_user']);
			$return[$i['id_user']]["$date[2]"] =$i['temps'];
//			$return[$i['id_user']]["type_".$date[2]] = ATF::pointage_tache()->nom($i['id_pointage_tache']);
			if ($i['id_pointage_tache']) {
				$return[$i['id_user']]["type_label_".$date[2]] = ATF::pointage_tache()->nom($i['id_pointage_tache']);
				$return[$i['id_user']]["type_".$date[2]] = $i['id_pointage_tache'];
			}
			$return[$i['id_user']]["id_".$date[2]] = $i['pointage.id_pointage'];
			
		}
		
		$pointageTaches = ATF::pointage_tache()->getAllLib();
		// Pour enlever les index du tableaux qui font chier le store
		foreach ($return as $k=>$i) {
			// Ajout de pointage manquant sinon la gestion des index foire complètement.
			for ($j=1; $j<32; $j++) {
				if ($j<10) $j = "0".$j;
				if (!$i[$j]) $i[$j] = 0;
			}
			
			$semaine = 0;
			$total = 0;
			// Pour chaque entrée du tableau qui concerne le temps journalier
			// Ici on calcule le total en parcourant le tableau de pointage.
			for ($forVal=1; $forVal<32; $forVal++) {
				if ($forVal<10) $forVal = "0".$forVal;
				$date = mktime(0,0,0,$infos['month']+1,$forVal,$infos['year']);
				
				
				// Total de la semaine pour un user
				$i['total'.$semaine] += $i[$forVal];
				// Total de la semaine pour tous les user
				$tot['total'.$semaine] += $i[$forVal];
				
				// Si la date tombe un dimanche, on incrémente le compteur pour passer al a semaine suivante
				if (date('N',$date)==7) {
					$semaine++;
				}
				
				// Total des temps de travail du lundi au vendredi pour un user
				if (date('N',$date)!=7 && date('N',$date)!=6) {
					$i['totalSemaine'] += $i[$forVal];
					$tot['totalSemaine'] += $i[$forVal];
				}
				
				// Total des temps de travail du week end pour un user
				if (date('N',$date)==7 || date('N',$date)==6) {
					$i['totalWeek'] += $i[$forVal];
					$tot['totalWeek'] += $i[$forVal];
				}
				
				// Total de la ligne pour un user
				$i['total'] += $i[$forVal];
				
				// Total journalier
				$tot[$forVal] += $i[$forVal];
				
				// Total par tâches
				foreach ($pointageTaches as $tache) {
					if ($i["type_label_".$forVal] == $tache) {
						$totalTaches[$tache][$forVal] += $i[$forVal];
						$totalTaches[$tache]["total"] += $i[$forVal];
					} else {
						$totalTaches[$tache][$forVal] += 0;
						$totalTaches[$tache]["total"] += 0;
					}
				}
			}
			
			// Total du total
			$tot['total'] += $i['total'];
			$result[] = $i;	
		}
		
		foreach ($pointageTaches as $k=>$i) {
			$semaine = 0;
			$addToResult = array();
			$addToResult["matricule"] = $k?"":"<b>Total</b>";
			$addToResult["user"] = "<b>".$i."</b>";
			foreach ($totalTaches[$i] as $k_=>$i_) {
				
				$addToResult[$k_] += $i_;
				$addToResult["type_label_".$k_] = $i;
				if (is_numeric($k_)) {
					$date = mktime(0,0,0,$infos['month']+1,$k_,$infos['year']);
					// Total de la semaine
					$addToResult['total'.$semaine] += $i_;
					// Si la date tombe un dimanche, on incrémente le compteur pour passer al a semaine suivante
					if (date('N',$date)==7) {
						$semaine++;
					}
					// Total des temps de travail du lundi au vendredi pour un user
					if (date('N',$date)!=7 && date('N',$date)!=6) {
						$addToResult['totalSemaine'] += $i_;
					}
					
					// Total des temps de travail du week end pour un user
					if (date('N',$date)==7 || date('N',$date)==6) {
						$addToResult['totalWeek'] += $i_;
					}
				}
				$addToResult['cls'] = 'total-row';
				$addToResult['rowId'] = 'total'.$k_;
			}
			$result[] = $addToResult;
		}
//		log::logger($addToResult,'qjanon');
		$tot['matricule'] = "";
		$tot['user'] = "<b>Toutes tâches confondues</b>";
		$tot['rowId'] = "total";
		$result[] = $tot;

		ATF::$json->add('totalCount',count($result));
		return $result;
		
	}
	
	public function planification($infos) {
		//Si un jour les checkbox group fonctionne avec les crochet dans le name, je pourrais retirer ce bout de code.
		if ($infos['lundi']) $infos['pointage']['jours'][1] = $infos['lundi'];
		if ($infos['mardi']) $infos['pointage']['jours'][2] = $infos['mardi'];
		if ($infos['mercredi']) $infos['pointage']['jours'][3] = $infos['mercredi'];
		if ($infos['jeudi']) $infos['pointage']['jours'][4] = $infos['jeudi'];
		if ($infos['vendredi']) $infos['pointage']['jours'][5] = $infos['vendredi'];
		if ($infos['samedi']) $infos['pointage']['jours'][6] = $infos['samedi'];
		if ($infos['dimanche']) $infos['pointage']['jours'][7] = $infos['dimanche'];
		$this->infoCollapse($infos);
		if ($infos['tache']) {
			$id_pointage_tache = ATF::pointage_tache()->getByLibelle($infos['tache'],'id_pointage_tache');
		}
		$this->q->reset();
		foreach (explode(",",$infos['userSelector']) as $k=>$i) {
			if (!$i) continue;
			$id_user = ATF::user()->decryptId($i);
			$checkDate = $infos['startDate'];
			$endDate = date('Y-m-d',strtotime($infos['endDate']." + 1 day"));
			while ($endDate!=$checkDate) {
				$j = date('N',strtotime($checkDate));
				if (array_key_exists($j,$infos['jours'])) {
					$this->q
						->orWhere("date",$checkDate,"temps".$checkDate."_".$id_user."_".$infos['daynight'])
						->andWhere("id_user",$id_user,"temps".$checkDate."_".$id_user."_".$infos['daynight'])
						->andWhere("daynight",$infos['daynight'],"temps".$checkDate."_".$id_user."_".$infos['daynight'])
						->addSuperCondition("user".$id_user.",temps".$checkDate."_".$id_user."_".$infos['daynight'],"OR","user".$id_user,false);
//					if ($d = $this->selectWithDate($checkDate,$id_user,$infos['daynight'])) {
//						$d['temps'] = $infos['temps'];
//						$d['id_pointage_tache'] = $id_pointage_tache;
//						$this->u($d);
//					} else {
						$insert[] = array(
							"date"=>$checkDate
							,"id_user"=>$id_user
							,'id_pointage_tache'=>$id_pointage_tache
							,"temps"=>$infos['temps']
							,"daynight"=>$infos['daynight']
						);
//						$this->i($insert);
//					}
				}
				
				$checkDate = date('Y-m-d',strtotime($checkDate." + 1 day"));
			}
			$this->q->addSuperCondition("user".$id_user.",users","OR","users");
		}
		ATF::tracabilite()->maskTrace($this->table);
		$this->delete();
		$this->multi_insert($insert);
		ATF::tracabilite()->unmaskTrace($this->table);
		return true;
	}
	
	public function selectWithDate($date,$id_user=false,$daynight='day'){
		$this->q->reset()
					->where('date',$date)
					->where('id_user',$id_user)
					->where('daynight',$daynight)
					->setDimension('row');
		return $this->sa();
	}

	public function updateTemps($infos) {
		if (!$infos['id_user']) return false;
		else $infos['id_user'] = ATF::user()->decryptId($infos['id_user']);
		
		if ($infos['year'] && $infos['month'] && $infos['day']) {
			$infos['date'] = date('Y-m-d',mktime(0,0,0,$infos['month'],$infos['day']	,$infos['year']));
			unset($infos['year'],$infos['month'],$infos['day']);
		} else return false;
		
		if ($infos['id_pointage']) {
			return $this->update($infos);
		} else {
			unset($infos['id_pointage']);
			return $this->insert($infos);
		}
		
	}

	public function updateTache($infos) {
		if (!$infos['id_pointage'] || !$infos['id_pointage_tache']) return false;
		
		foreach (json_decode($infos['id_pointage']) as $k=>$i) {
			$u = array("id_pointage"=>$i,"id_pointage_tache"=>$infos['id_pointage_tache']);
			$this->update($u);
		}
		
		return true;
	}

	/** Insertion des données pour le premier onglet de l'export
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function exportXLS($infos){
		session_write_close();
	
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_workbook.inc.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/php_writeexcel/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new writeexcel_workbook($fname);
		
		$date = mktime(0,0,0,$infos['month']+1,1,$infos['year']);
		$worksheet =& $workbook->addworksheet("Export ".date('F',$date)." ".date("Y",$date));
		
		$line = 1;
		$r = $this->getPointageByUser($infos);

		foreach ($r as $k=>$i) {
			if (!$k) {
				$worksheet->set_column(0,0,15);
				$worksheet->set_column(1,1,20);
				$worksheet->set_column(2,100,5);
			}
			
			$worksheet->write(0, 0, "Matricule");  
			$worksheet->write(0, 1, "Utilisateur");  
			$incrementation=2;
			$countTotal = 0;
			$worksheet->write($line, 0, $i['matricule']);  
			$worksheet->write($line, 1, $i['user']); 

			ksort($i);

			foreach($i as $k_=>$i_){
				if (!is_numeric($k_)) continue;
				
				$timestamp = mktime(0,0,0,$infos['month']+1,$k_,$infos['year']);
				if (date('m',$timestamp) != $infos['month']+1) continue;
				
				$worksheet->write(0, $k_+1+$countTotal, date('D',$timestamp)."\n".$k_);  
				
//				if ($line==3) log::logger($k_.date("D",$timestamp)." et ".$countTotal." = ".($k_+1+$countTotal)." ecrire : ".$i_,"qjanon");
				
				$worksheet->write($line, $k_+1+$countTotal, $i_);  
				
				if (date("N",$timestamp)==7) {
					$worksheet->write(0, $k_+2+$countTotal, "Total\nSemaine"); 
					$worksheet->write($line, $k_+2+$countTotal, $i["total".$countTotal]);  
					$countTotal++; 
				}
				
				if (date("N",$timestamp)==7 || date("N",$timestamp)==6) {
					$totalWE += $i_;
					$total += $i_;
				} else {
					$totalSE += $i_;	
					$total += $i_;
				}
			}
			$worksheet->write(0, $k_+2+$countTotal, "SE"); 
			$worksheet->write($line, $k_+2+$countTotal, $totalSE);  
			$worksheet->write(0, $k_+3+$countTotal, "WE"); 
			$worksheet->write($line, $k_+3+$countTotal, $totalWE);  
			$worksheet->write(0, $k_+4+$countTotal, "Total"); 
			$worksheet->write($line, $k_+4+$countTotal, $total);  
			$line++;
			$total = $totalSE = $totalWE = 0;
		}
		
		$workbook->close();

		header("Content-Type: application/x-msexcel; name=Export_pointage".date("_F_Y",$date).".xls");
		header("Content-Disposition: attachment; filename=Export_pointage".date("_F_Y",$date).".xls");;
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);

	}

};
?>
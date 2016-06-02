<?
/** 
* Crontab en travaux qui permet de réinsérer les champs malencontreusement supprimée ici pour cleodis
*/
define("__BYPASS__",true);

/********************MODIFIER LE NOM DE LA BASE**********************/
$_SERVER["argv"][1] = "cleodis";

include(dirname(__FILE__)."/../global.inc.php");

ATF::tracabilite()->q->reset()->addCondition("tracabilite","delete")->addCondition("id_module",52);
$tracabilites_affaires=ATF::tracabilite()->sa();
foreach($tracabilites_affaires as $key=>$item){
	$affaire=unserialize($item["avant_modification"]);
	if($affaire[0]){
		foreach($affaire as $k=>$i){
			if($i[0]){
				foreach($i as $k1=>$i1){
					$affaires[]=$i1;
				}
			}else{
				$affaires[]=$i;
			}
		}
	}else{
		$affaires[]=$affaire;
	}
}

ATF::tracabilite()->q->reset()->addCondition("tracabilite","delete")->addCondition("nom_element","suivi_notifie");
$tracabilites_suivi_notifies=ATF::tracabilite()->sa();

foreach($tracabilites_suivi_notifies as $key=>$item){
	$suivi_notifie=unserialize($item["avant_modification"]);
	if($suivi_notifie[0]){
		foreach($suivi_notifie as $k=>$i){
			if($i[0]){
				foreach($i as $k1=>$i1){
					$suivi_notifies[]=$i1;
				}
			}else{
				$suivi_notifies[]=$i;
			}
		}
	}else{
		$suivi_notifies[]=$suivi_notifie;
	}
}


ATF::tracabilite()->q->reset()->addCondition("tracabilite","delete")->addCondition("nom_element","suivi_contact");
$tracabilites_suivi_contacts=ATF::tracabilite()->sa();

foreach($tracabilites_suivi_contacts as $key=>$item){
	$suivi_contact=unserialize($item["avant_modification"]);
	if($suivi_contact[0]){
		foreach($suivi_contact as $k=>$i){
			if($i[0]){
				foreach($i as $k1=>$i1){
					$suivi_contacts[]=$i1;
				}
			}else{
				$suivi_contacts[]=$i;
			}
		}
	}else{
		$suivi_contacts[]=$suivi_contact;
	}
}


ATF::tracabilite()->q->reset()->addCondition("tracabilite","delete")->addCondition("id_module",9);
$tracabilites_suivis=ATF::tracabilite()->sa();

foreach($tracabilites_suivis as $key=>$item){
	$suivi=unserialize($item["avant_modification"]);
	if($suivi[0]){
		foreach($suivi as $k=>$i){
			if($i[0]){
				foreach($i as $k1=>$i1){
					$suivis[]=$i1;
				}
			}else{
				$suivis[]=$i;
			}
		}
	}else{
		$suivis[]=$suivi;
	}
}
$nb=0;
ATF::db()->begin_transaction();
foreach($suivis as $key=>$item){
	foreach($affaires as $k=>$i){
		if($i["id_affaire"]==$item["suivi.id_affaire"]){
			ATF::affaire()->q->reset()
						   ->addCondition("ref",$i["ref"])
						   ->setDimension("row");
			$affaire=ATF::affaire()->sa();
			if($affaire){

print_r(array(	"id_user"=>$item["suivi.id_user"],
				"id_contact"=>$item["suivi.id_contact"],
				"id_societe"=>$item["suivi.id_societe"],
				"origine"=>$item["suivi.origine"],
				"type"=>$item["suivi.type"],
				"date"=>$item["suivi.date"],
				"texte"=>$item["suivi.texte"],
				"public"=>$item["suivi.public"],
				"id_affaire"=>$$affaire["id_affaire"]
));
		
				$id_suivi=ATF::suivi()->i(array(
										"id_user"=>$item["suivi.id_user"],
										"id_contact"=>$item["suivi.id_contact"],
										"id_societe"=>$item["suivi.id_societe"],
										"origine"=>$item["suivi.origine"],
										"type"=>$item["suivi.type"],
										"date"=>$item["suivi.date"],
										"texte"=>$item["suivi.texte"],
										"public"=>$item["suivi.public"],
										"id_affaire"=>$$affaire["id_affaire"]
									)
								);
				
				foreach($suivi_notifies as $k1=>$i1){
					if($i1["suivi_notifie.id_suivi"]==$item["suivi.id_suivi"]){
print_r("\n id_suivi : ".$id_suivi);			
						ATF::suivi_notifie()->q->reset()->addCondition("id_user",$i1["suivi_notifie.id_user"])->addCondition("id_suivi",$id_suivi)->setDimension("row");
						$suivi_notifie=ATF::suivi_notifie()->sa();
						if(!$suivi_notifie){
print_r(array("id_suivi"=>$id_suivi,"id_user"=>$i1["suivi_notifie.id_user"]));			
							ATF::suivi_notifie()->i(array(
													"id_suivi"=>$id_suivi,
													"id_user"=>$i1["suivi_notifie.id_user"]
												)
											);
						}
					}
				}
	
				foreach($suivi_contacts as $k1=>$i1){
					if($i1["suivi_contact.id_suivi"]==$item["suivi.id_suivi"]){
print_r("\n id_suivi : ".$id_suivi);			
						ATF::suivi_contact()->q->reset()->addCondition("id_contact",$i1["suivi_contact.id_contact"])->addCondition("id_suivi",$id_suivi)->setDimension("row");
						$suivi_contact=ATF::suivi_contact()->sa();
						if(!$suivi_contact){
print_r(array("id_suivi"=>$id_suivi,"id_contact"=>$i1["suivi_contact.id_contact"]));			
							ATF::suivi_contact()->i(array(
													"id_suivi"=>$id_suivi,
													"id_contact"=>$i1["suivi_contact.id_contact"]
												)
											);
						}
								
					}
				}

				
				$nb++;
			}else{
				print_r("pas affaire");
				print_r($i);
			}
		}
	}
}
		ATF::db()->commit_transaction();

				print_r($nb);


?>
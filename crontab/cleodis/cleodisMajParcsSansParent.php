<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

/*Permet de mettre à jour les lignes de commande ayant une affaire provenance mais pour laquelle il n'y a ni parc dans l'affaire courante ni dans l'affaire parente*/

ATF::db()->begin_transaction();


$query="SELECT * 
		FROM  `parc` 
		GROUP BY  `serial` 
		HAVING COUNT(  `serial` ) >1";
$parcs=ATF::db()->sql2array($query);
$nb=0;
foreach ($parcs as $item) {
	$query="SELECT * 
			FROM  `parc` 
			WHERE `serial` ='".$item["serial"]."'";
	
	$serials=ATF::db()->sql2array($query);
	foreach($serials as $it){
		if($affaire=ATF::parc()->select($it["id_parc"])){
			$query="SELECT * 
					FROM  `parc` 
					WHERE `serial` ='".$it["serial"]."'
					AND `id_parc` !=".$it["id_parc"]."
					AND `id_affaire` =".$it["id_affaire"];
			
			$serials_affaire=ATF::db()->sql2array($query);
			foreach($serials_affaire as $i){
				if($i["etat"]==$it["etat"]){
					$nb++;
					ATF::parc()->d($i["id_parc"]);
					$tab[$i["id_affaire"]]=$i["id_affaire"];
				}
			}
		}

	}
}
print_r($tab);
print_r("\n Parcs supprimés ".$nb);

/*Chercher les affaires qui sont filles alors qu'elles sont perdues*/
$query="SELECT b.`id_affaire` AS parent, a.`id_affaire` AS fille
		FROM  `affaire` AS a
		INNER JOIN  `affaire` AS b ON a.`id_affaire` = b.`id_fille` 
		WHERE a.`etat` =  'perdue'";

$affaires=ATF::db()->sql2array($query);
$var1=0;
foreach ($affaires as $item) {
	ATF::affaire()->u(array("id_affaire"=>$item["parent"],"id_fille"=>NULL));
	$var1++;
}
print_r("\n les affaires qui sont filles alors qu'elles sont perdues ".$var1);

/*Produit vendu des ventes*/
//$query="SELECT * 
//		FROM  `parc` 
//		INNER JOIN  `affaire` ON  `affaire`.`id_affaire` =  `parc`.`id_affaire` 
//		WHERE  `nature` =  'vente'
//		AND  `parc`.`etat` !=  'vendu'";
//
//$parcs=ATF::db()->sql2array($query);
//$var2=0;
//foreach ($parcs as $item) {
//	ATF::parc()->u(array("id_parc"=>$item["id_parc"],"etat"=>'vendu'));
//	$var2++;
//}
//print_r("\n Produit vendu des ventes ".$var2);
//
//$query="SELECT  `parc`.`id_parc` ,  `affaire`.`id_affaire` ,  `parc`.`etat` ,  `affaire`.`nature` ,`affaire`.`ref`
//		FROM  `parc` 
//		INNER JOIN  `affaire` ON  `affaire`.`id_affaire` =  `parc`.`id_affaire` 
//		WHERE  `provenance` IS NOT NULL 
//		AND (
//		`nature` =  'AR'
//		OR  `nature` =  'avenant'
//		OR  `nature` =  'vente'
//		)";
//
//
//$parcs=ATF::db()->sql2array($query);
//foreach ($parcs as $item) {
//	$etat="";
//	if($item["nature"]=="AR"){
//		if($item["etat"]!="reloue"){
//			$etat="reloue";
//		}
//	}elseif($item["nature"]=="vente"){
//		if($item["etat"]!="vendu"){
//			$etat="vendu";
//		}
//	}elseif($item["nature"]=="avenant"){
//		if($item["etat"]!="broke"){
//			$etat="broke";
//		}
//	}
//	if($etat){
//		ATF::parc()->u(array("id_parc"=>$item["id_parc"],"etat"=>$etat));
//	}
//}



$query="SELECT * 
		FROM  `parc` 
		ORDER BY  `parc`.`id_affaire` DESC ";

$parcs=ATF::db()->sql2array($query);
$a=$arar=$arav=$arv=$av=$v=$n=0;
$nb=count($parcs);
$nbi=$nbp=0;

foreach ($parcs as $item) {
	$nbi++;
	print_r("\n ".$nbi."/".$nb);

	$affaire=ATF::affaire()->select($item["id_affaire"]);
	
	ATF::commande()->q->reset()->addCondition("id_affaire",$item["id_affaire"])->setDimension("row");
	$commande=ATF::commande()->sa();
	unset($parc);
	unset($parcMaj);
	
	/*           Validé     */
	if(!$item["provenance"]){
		
		$parcMaj=$item;
		$parcMaj["provenance"]=NULL;
		if($affaire["nature"]=="vente"){
			$parcMaj["etat"]="vendu";
			ATF::facture()->q->reset()->addCondition("id_affaire",$item["id_affaire"]);
			if($facture=ATF::facture()->sa()){
				$parcMaj["date_inactif"]=NULL;
				$parcMaj["existence"]="actif";
			}else{
				$parcMaj["existence"]="inactif";
			}
		}else{
			$parcMaj["etat"]="loue";
			if($commande["etat"]=="mis_loyer" || $commande["etat"]=="prolongation"){
				$parcMaj["date_inactif"]=NULL;
				$parcMaj["existence"]="actif";
			}else{
				$parcMaj["existence"]="inactif";
			}
		}
		if($parcMaj!=$item){
			$query= 'SELECT * 
					 FROM  `parc` 
					 WHERE  `serial` = "'.$parcMaj["serial"].'"
					 AND `etat` = "'.$parcMaj["etat"].'"
					 AND `ref` = "'.$parcMaj["ref"].'"';

			$parc_existe=ATF::db()->sql2array($query);
			if(!$parc_existe){
print_r("\n11111111111\n");
print_r($parcMaj);
				ATF::parc()->u($parcMaj);
				$item=ATF::parc()->select($item["id_parc"]);
			}
		}
		
		//MAJ commande courant
		if($commande){
			if($id_commande_ligne=possibilites($item,$commande)){
				$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);
				if($commande_ligne["id_affaire_provenance"]){
//					print_r("\n-----------------------------------parc sans provenance alors que commande avec provenance");					
//					print_r($item);
					if(!$item["provenance"] || $item["provenance"]!=$commande_ligne["id_affaire_provenance"]){
						$item["provenance"]=$commande_ligne["id_affaire_provenance"];
print_r("\n222222222222\n");
print_r($item);
						ATF::parc()->u($item);
						$item=ATF::parc()->select($item["id_parc"]);
					}
				}
			}else{
				print_r("\nProbleme pas de id_commande_ligne1");					
				print_r($item);	
				ATF::parc()->d($item["id_parc"]);				
			}
		}

	/*------------Validé     */
	}
	
	if($item["provenance"]){
		$parcMaj=$item;
		if($affaire["nature"]=="vente"){
			$parcMaj["etat"]="vendu";
			ATF::facture()->q->reset()->addCondition("id_affaire",$item["id_affaire"]);
			if($facture=ATF::facture()->sa()){
				$parcMaj["date_inactif"]=NULL;
				$parcMaj["existence"]="actif";
			}else{
				$parcMaj["existence"]="inactif";
			}
		}else{
			if($affaire["nature"]=="AR"){
				$parcMaj["etat"]="reloue";
			}elseif($affaire["nature"]=="avenant"){
				$parcMaj["etat"]="broke";
			}elseif($affaire["nature"]=="affaire"){
				print_r($item);
				print_r("\nProbleme le parc a une provenance alors qu'il est en nature == affaire");					
			}
			
			if($commande["etat"]=="mis_loyer" || $commande["etat"]=="prolongation"){
				$parcMaj["date_inactif"]=NULL;
				$parcMaj["existence"]="actif";
			}else{
				$parcMaj["existence"]="inactif";
			}
		}
		
		if($parcMaj!=$item){
			$query= 'SELECT * 
					 FROM  `parc` 
					 WHERE  `serial` = "'.$parcMaj["serial"].'"
					 AND `etat` = "'.$parcMaj["etat"].'"
					 AND `ref` = "'.$parcMaj["ref"].'"';

			$parc_existe=ATF::db()->sql2array($query);
			if(!$parc_existe){
print_r("\n333333333333\n");
print_r($parcMaj);
				ATF::parc()->u($parcMaj);
				$item=ATF::parc()->select($item["id_parc"]);
			}
		}

		//MAJ commande courant
		if($commande){
			if($id_commande_ligne=possibilites($item,$commande)){
				$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);
				if($commande_ligne["id_affaire_provenance"]){
					if($commande_ligne["id_affaire_provenance"]!=$item["provenance"]){
						print_r("\nProvenance différente ".$commande_ligne["id_affaire_provenance"]."!=".$item["provenance"]);					
						print_r("\ncommande_ligne");					
						print_r($commande_ligne);					
						print_r("\nitem");					
						print_r($item);					
					}
				}else{
					$commande_ligne["id_affaire_provenance"]=$item["provenance"];
print_r($commande_ligne);
					ATF::commande_ligne()->u($commande_ligne);
				}
			}else{
				print_r("\nProbleme pas de id_commande_ligne2");					
				print_r($item);					
				ATF::parc()->d($item["id_parc"]);				
			}
		}

		ATF::commande()->q->reset()->addCondition("id_affaire",$item["provenance"])->setDimension("row");
		$commande_parent=ATF::commande()->sa();

		ATF::parc()->q->reset()->addCondition("id_affaire",$item["provenance"])->addCondition("serial",$item["serial"])->setDimension("row");
		
		if($parctrouv=ATF::parc()->sa()){
			$parcMaj=$parctrouv;
			if($commande_parent["etat"]=="mis_loyer" || $commande_parent["etat"]=="prolongation"){
				$parcMaj["date_inactif"]=NULL;
				$parcMaj["existence"]="actif";
			}else{
				$parcMaj["existence"]="inactif";
				$parcMaj["date_inactif"]=$commande["date_debut"];
			}
			if($parcMaj!=$parctrouv){
				$query= 'SELECT * 
						 FROM  `parc` 
						 WHERE  `serial` = "'.$parcMaj["serial"].'"
						 AND `etat` = "'.$parcMaj["etat"].'"
						 AND `ref` = "'.$parcMaj["ref"].'"';
	
				$parc_existe=ATF::db()->sql2array($query);
				if(!$parc_existe){
print_r("\n44444444444\n");
print_r($parcMaj);
					ATF::parc()->u($parcMaj);
				}
			}
		}else{
		
			$affaire_parente=ATF::affaire()->select($item["provenance"]);

			$parc=$item;
			$parc["id_affaire"]=$parc["provenance"];
			$parc["date"]=$commande["date_debut"];

			if($commande_parent["etat"]=="mis_loyer" || $commande_parent["etat"]=="prolongation"){
				$parc["date_inactif"]=NULL;
				$parc["existence"]="actif";
			}else{
				$parc["existence"]="inactif";
				$parc["date_inactif"]=$commande["date_debut"];
			}

			unset($parc["id_parc"]);
			unset($parc["provenance"]);
		
			//Reloue
			if($item["etat"]=="reloue"){
				if($affaire["nature"]=="affaire"){
					print_r("\n probleme reloue affaire");
					$a++;
				}elseif($affaire["nature"]=="AR"){
					$parc["etat"]="loue";
					
					$id_parc=ATF::parc()->i($parc);
					$nbp++;
					$parc=ATF::parc()->select($id_parc);
					//MAJ commande parent
					if($id_commande_ligne=possibilites($parc,$commande_parent)){
						$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);
						if($commande_ligne["id_affaire_provenance"]){
							if(!$parc["provenance"] || $parc["provenance"]!=$commande_ligne["id_affaire_provenance"]){
								$parc["provenance"]=$commande_ligne["id_affaire_provenance"];
print_r("\n5555555\n");
print_r($parc);
								ATF::parc()->u($parc);
							}
						}
					}else{
						print_r("\nProbleme reloue pas de id_commande_ligne");					
						print_r($item);					
						print_r($parc);					
					}
					$n++;
				}elseif($affaire["nature"]=="avenant"){
					print_r("\n probleme reloue avenant");
					$av++;
				}elseif($affaire["nature"]=="vente"){
					print_r("\n probleme reloue vente");
					$v++;
				}
			//Vendu
			}elseif($item["etat"]=="vendu"){
				if($affaire["nature"]=="affaire"){
					print_r("\n probleme vendu affaire");
					$a++;
				}elseif($affaire["nature"]=="AR"){
					print_r("\n probleme vendu AR");
					$n++;
				}elseif($affaire["nature"]=="avenant"){
					print_r("\n probleme vendu avenant");
					$av++;
				}elseif($affaire["nature"]=="vente"){
					$parc["etat"]="broke";
					
					$id_parc=ATF::parc()->i($parc);
					$nbp++;
					$parc=ATF::parc()->select($id_parc);
					if($id_commande_ligne=possibilites($parc,$commande_parent)){
						$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);
						if($commande_ligne["id_affaire_provenance"]){
							if(!$parc["provenance"] || $parc["provenance"]!=$commande_ligne["id_affaire_provenance"]){
								$parc["provenance"]=$commande_ligne["id_affaire_provenance"];
print_r("\n66666666\n");
print_r($parc);
								ATF::parc()->u($parc);
							}
						}
					}else{
						print_r("\nProbleme vendu pas de id_commande_ligne");					
						print_r($parc);					
					}
					$n++;
						
				}
			//Avenant
			}elseif($item["etat"]=="broke"){
				if($affaire["nature"]=="vente"){
					print_r("\n probleme broke vente");
					$a++;
				}else{
					$parc["etat"]="loue";
					
					$id_parc=ATF::parc()->i($parc);
					$nbp++;
					$parc=ATF::parc()->select($id_parc);
					if($id_commande_ligne=possibilites($parc,$commande_parent)){
						$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);
						if($commande_ligne["id_affaire_provenance"]){
							if(!$parc["provenance"] || $parc["provenance"]!=$commande_ligne["id_affaire_provenance"]){
								$parc["provenance"]=$commande_ligne["id_affaire_provenance"];
print_r("\n7777777777\n");
print_r($parc);
								ATF::parc()->u($parc);
							}
						}
					}else{
						print_r("\nProbleme broke pas de id_commande_ligne");					
						print_r($parc);					
					}
					$n++;
						
				}
				
			}elseif($item["etat"]=="loue"){
				print_r("\nProbleme provenance alors que naure == affaire");					
				print_r($parc);					
			}
		}
	}
//if($parc){
//	$parc["ref_affaire"]=$affaire_parente["ref"];
//	print_r($parc);
//}
//print_r($commande);
//print_r($item);
}
print_r($nbp." Parcs insérés");

ATF::db()->commit_transaction();

function possibilites($item,$commande_parent){
	if($item["provenance"]){
		$query0='SELECT * 
				FROM  `commande_ligne` 
				WHERE  `id_commande` ='.$commande_parent["id_commande"].'
				AND `id_produit` = '.$item["id_produit"].'
				AND `ref` = "'.$item["ref"].'"
				AND `produit` = "'.$item["libelle"].'"
				AND `id_affaire_provenance` = '.$item["provenance"];
	
		$commande_lignes=ATF::db()->sql2array($query0);
	
		if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
			return $id_commande_ligne; 
		}
	}else{
		$query0='SELECT * 
				FROM  `commande_ligne` 
				WHERE  `id_commande` ='.$commande_parent["id_commande"].'
				AND `id_produit` = '.$item["id_produit"].'
				AND `ref` = "'.$item["ref"].'"
				AND `produit` = "'.$item["libelle"].'"
				AND `id_affaire_provenance` IS NULL';
	
		$commande_lignes=ATF::db()->sql2array($query0);
	
		if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
			return $id_commande_ligne; 
		}
	}


	$query1='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `id_produit` = '.$item["id_produit"].'
			AND `ref` = "'.$item["ref"].'"
			AND `produit` = "'.$item["libelle"].'"';

	$commande_lignes=ATF::db()->sql2array($query1);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}
	
	$query2='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `id_produit` = '.$item["id_produit"].'
			AND (`ref` = "'.$item["ref"].'"
			OR `produit` = "'.$item["libelle"].'")';

	$commande_lignes=ATF::db()->sql2array($query2);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}

	$query3='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `ref` = "'.$item["ref"].'"
			AND (`id_produit` = '.$item["id_produit"].'
			OR `produit` = "'.$item["libelle"].'")';

	$commande_lignes=ATF::db()->sql2array($query3);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}

	$query4='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `produit` = "'.$item["libelle"].'"
			AND (`id_produit` = '.$item["id_produit"].'
			OR `ref` = "'.$item["ref"].'")';

	$commande_lignes=ATF::db()->sql2array($query4);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}
	
	$query5='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND (`produit` = "'.$item["libelle"].'"
			OR `id_produit` = '.$item["id_produit"].'
			OR `ref` = "'.$item["ref"].'")';

	$commande_lignes=ATF::db()->sql2array($query5);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}
	
print_r($query0);
	return false;
}


function trouv_commande_ligne($item,$commande_parent,$commande_lignes){
	if($commande_parent){
		$modif=false;
		unset($cl);
		if(!$commande_lignes){
			$probleme="\npas de commande ligne ".$item["serial"];
		}elseif(count($commande_lignes)==1){
			if($commande_lignes[0]["quantite"]==1){
				if($commande_lignes[0]["serial"] && $commande_lignes[0]["serial"]!=$item["serial"]){
					$probleme="\nProblème le serial existe mais il est mauvais...".$commande_lignes[0]["serial"]."!=".$item["serial"];
				}else{
					$cl=array("id_commande_ligne"=>$commande_lignes[0]["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
					if($item["serial"]!=$commande_lignes[0]["serial"] || $item["id_produit"]!=$commande_lignes[0]["id_produit"]){
						$modif=true;
					}
				}
			}else{
				$serials=explode(" ",$commande_lignes[0]["serial"]);
				if(count($serials)>$commande_lignes[0]["quantite"]){
					$probleme="\nProblème Il y a plus de serial que de quantité...";
				}elseif(count($serials)==$commande_lignes[0]["quantite"]){
					if(in_array($item["serial"],$serials)){
						return $commande_lignes[0]["id_commande_ligne"];
					}else{
						$probleme="\nProblème Il y a autant de serial que de quantité et le serial n'est pas présent...";
					}
				}elseif($serials){
					foreach($serials as $ks=>$ss){
						if($ss==$item["serial"]){
							return $commande_lignes[0]["id_commande_ligne"];
						}
					}
					$cl=array("id_commande_ligne"=>$commande_lignes[0]["id_commande_ligne"],"serial"=>$commande_lignes[0]["serial"]." ".$item["serial"],"id_produit"=>$item["id_produit"]);
				}else{
					$cl=array("id_commande_ligne"=>$commande_lignes[0]["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
					if($item["serial"]!=$commande_lignes[0]["serial"] || $item["id_produit"]!=$commande_lignes[0]["id_produit"]){
						$modif=true;
					}
				}
			}
		}elseif(count($commande_lignes)>1){
			foreach($commande_lignes as $k=>$i){
				if($i["quantite"]==1){
					if($i["serial"]==$item["serial"]){
						return $i["id_commande_ligne"];
					}elseif(!$i["serial"]){
						$cl=array("id_commande_ligne"=>$i["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
						if($item["serial"]!=$i["serial"] || $item["id_produit"]!=$i["id_produit"]){
							$modif=true;
						}
					}
				}else{
					$serials=explode(" ",$i["serial"]);
					if(count($serials)>$i["quantite"]){
						$probleme="\n2 Problème Il y a plus de serial que de quantité...";
					}elseif($serials && count($serials)<$i["quantite"]){
						foreach($serials as $ks=>$ss){
							if($ss==$item["serial"]){
								return $i["id_commande_ligne"];
							}
						}
						$cl=array("id_commande_ligne"=>$i["id_commande_ligne"],"serial"=>$i["serial"]." ".$item["serial"],"id_produit"=>$item["id_produit"]);
					}else{
						$cl=array("id_commande_ligne"=>$i["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
						if($item["serial"]!=$i["serial"] || $item["id_produit"]!=$i["id_produit"]){
							$modif=true;
						}
					}
				}
			}
			if(!$cl){
				$probleme="\les bons de commandes son déjà serialisé et le parc n'y est pas";
			}
		}else{
			$probleme="\pas de commande mais un parc";
		}
	
		if($cl){
			if($modif){
//print_r($cl);
//				ATF::commande_ligne()->u($cl);
			}
			return $cl["id_commande_ligne"];
		}
//print_r($probleme);
		return false;

	}else{
		$probleme="\n Pas de commande parent";
//		print_r("\n Pas de commande parent");
	}
//print_r($probleme);
	return false;
}



?>
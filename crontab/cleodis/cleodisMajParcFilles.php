<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

/*Permet de mettre à jour les lignes de commande ayant une affaire provenance mais pour laquelle il n'y a ni parc dans l'affaire courante ni dans l'affaire parente*/

ATF::db()->begin_transaction();

/*Affaire fille*/
$query="SELECT * 
		FROM  `parc` 
		ORDER BY  `parc`.`id_affaire` ASC ";

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

	
	if($affaireFilles=ATF::affaire()->getFilles($item["id_affaire"])){
		foreach($affaireFilles as $k=>$i){
			
			$affaireFille=ATF::affaire()->select($i["id_affaire"]);
			
			if($affaireFille["nature"]=="affaire"){
				print_r("\n1 Probleme le parc a une provenance alors qu'il est en nature == affaire");

				print_r($affaireFille);					
				print_r($item);					
			}else{	
			
				ATF::parc()->q->reset()->addCondition("id_affaire",$affaireFille["id_affaire"])->addCondition("serial",$item["serial"]);
				if($parc_trouv=ATF::parc()->sa()){
					if(count($parc_trouv)>1){
						if($affairePetitesFilles=ATF::affaire()->getFilles($item["id_affaire"])){
							ATF::parc()->q->addCondition("etat","broke","AND",false,"!=");
							if($parc_trouv1=ATF::parc()->sa()){
								if(count($parc_trouv1)>1){
									print_r("\n pb plusieurs parcs identique dans même affaire 1111");
									print_r($affaire);
									print_r($affaireFille);
									print_r($item);
									print_r($parc_trouv);
								}
							}
						}else{
							print_r("\n pb plusieurs parcs identique dans même affaire 2222222");
							print_r($affaire);
							print_r($affaireFille);
							print_r($item);
							print_r($parc_trouv);
						}
					}elseif($parc_trouv[0]["provenance"]!=$item["id_affaire"]){
						print_r("\n probleme provenance ".$parc_trouv[0]["provenance"]."!=".$item["id_affaire"]);
						print_r($item);
					}
				}else{
					ATF::commande()->q->reset()->addCondition("id_affaire",$affaireFille["id_affaire"]);
					if($commandeFille=ATF::commande()->sa()){
						$trouv=false;
						foreach($commandeFille as $fille){
							if($id_commande_ligne=possibilites($item,$fille)){
								if($trouv){
									print_r("\ns probleme plusieurs commandes pour un parc");
									print_r($item);
									print_r($trouv);
									print_r($fille);
								}else{
									$trouv=$fille;
									$commande_ligne=ATF::commande_ligne()->select($id_commande_ligne);
									$commande=ATF::commande()->select($commande_ligne["id_commande"]);
									
								/*Insertion parc*/
									$parc=$item;
									$parc["date"]=date("Y-m-d");
									$parcAncien=$item;
									
									unset($parc["id_parc"]);
									
									$parc["id_affaire"]=$fille["id_affaire"];
									$parc["provenance"]=$affaire["id_affaire"];
									
									if($affaireFille["nature"]=="vente"){
										$parc["etat"]="vendu";
										$parcAncien["etat"]="broke";
										
										ATF::facture()->q->reset()->addCondition("id_affaire",$fille["id_affaire"]);
										if($facture=ATF::facture()->sa()){
											$parc["date_inactif"]=NULL;
											$parc["existence"]="actif";
											
											$parcAncien["existence"]="inactif";
											$parcAncien["date_inactif"]=$facture["date"];
										}else{
											$parc["existence"]="inactif";
	
											$parcAncien["date_inactif"]=NULL;
											$parcAncien["existence"]="actif";
										}
										
										$nbp++;
										print_r($parc);
										$id_parc=ATF::parc()->i($parc);
										
										if($parcAncien!=$item){
											print_r($parcAncien);
											ATF::parc()->u($parcAncien);
										}
								
									}else{
										
										if($commande["etat"]=="mis_loyer" || $commande["etat"]=="prolongation"){
											$parc["date_inactif"]=NULL;
											$parc["existence"]="actif";
											
											$parcAncien["existence"]="inactif";
											$parcAncien["date_inactif"]=$commande["date_debut"];
										}else{
											$parc["existence"]="inactif";
										}
										
										if($affaireFille["nature"]=="AR"){
											$parc["etat"]="reloue";
										}elseif($affaireFille["nature"]=="avenant"){
											$parc["etat"]="broke";
										}else{
											print_r("\n2 Probleme le parc a une provenance alors qu'il est en nature == affaire");
											print_r($item);					
											print_r($affaireFille);					
											print_r($commande);					
										}
									

										$nbp++;
										print_r($parc);
										$id_parc=ATF::parc()->i($parc);
								
										if($parcAncien!=$item){
											print_r($parcAncien);
											ATF::parc()->u($parcAncien);
										}
	
									}
								}
							}
						}
						if($affaireFille["nature"]=="AR" && !$trouv){

							ATF::parc()->q->reset()->addCondition("id_affaire",$affaire["id_affaire"])
												   ->addCondition("serial",$item["serial"])
												   ->addCondition("etat","broke","AND",false,"=");

							if(!ATF::parc()->sa()){
								print_r($affaire);
								unset($item["id_parc"]);
								$item["etat"]="broke";
								$item["existence"]="actif";
								print_r($item);
								$id_parc=ATF::parc()->i($item);
								$nbp++;
							}
						}
					}else{
						if($affaireFille["etat"]!="perdue" && $affaireFille["etat"]!="devis"){
							print_r("\n2 affaire fille sans commande");
							print_r($affaire);
							print_r($affaireFille);
							print_r($item);
						}
					}
				}
			}
		}
	}
}
print_r($nbp." Parcs insérés");


ATF::db()->commit_transaction();
function possibilites($item,$commande_parent){

	$query='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `serial` LIKE "'.$item["serial"].'"';

	$commande_lignes=ATF::db()->sql2array($query);

	if($commande_lignes[0]["id_commande_ligne"]){
		return $commande_lignes[0]["id_commande_ligne"]; 
	}
	

	$query0='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `id_affaire_provenance` = '.$item["id_affaire"].'
			AND `id_produit` = '.$item["id_produit"].'
			AND `ref` = "'.$item["ref"].'"
			AND `produit` = "'.$item["libelle"].'"';

	$commande_lignes=ATF::db()->sql2array($query0);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}
	
	$query2='SELECT * 
			FROM  `commande_ligne` 
			WHERE  `id_commande` ='.$commande_parent["id_commande"].'
			AND `id_affaire_provenance` = '.$item["id_affaire"].'
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
			AND `id_affaire_provenance` = '.$item["id_affaire"].'
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
			AND `id_affaire_provenance` = '.$item["id_affaire"].'
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
			AND `id_affaire_provenance` = '.$item["id_affaire"].'
			AND (`produit` = "'.$item["libelle"].'"
			OR `id_produit` = '.$item["id_produit"].'
			OR `ref` = "'.$item["ref"].'")';

	$commande_lignes=ATF::db()->sql2array($query5);

	if($id_commande_ligne=trouv_commande_ligne($item,$commande_parent,$commande_lignes)){
		return $id_commande_ligne; 
	}
	
	return false;
}


function trouv_commande_ligne($item,$commande_parent,$commande_lignes){
	if($commande_parent){
		$mod=false;
		unset($cl);
		if(!$commande_lignes){
			$probleme="\npas de commande ligne ".$item["serial"];
		}elseif(count($commande_lignes)==1){
			if($commande_lignes[0]["quantite"]==1){
				if($commande_lignes[0]["serial"] && $commande_lignes[0]["serial"]!=$item["serial"]){
					$probleme="\nProblème le serial existe mais il est mauvais...".$commande_lignes[0]["serial"]."!=".$item["serial"];
				}else{
					$query='SELECT * 
							 FROM  `parc` 
							 WHERE `provenance` = '.$item["id_affaire"].'
							 AND `ref` = "'.$item["ref"].'"
							 AND `libelle` = "'.$item["libelle"].'"';

					$parc_existe1=ATF::db()->sql2array($query);

					$query='SELECT * 
							 FROM  `parc` 
							 WHERE `provenance` = '.$commande_lignes[0]["id_affaire_provenance"].'
							 AND `ref` = "'.$commande_lignes[0]["ref"].'"
							 AND `libelle` = "'.$commande_lignes[0]["produit"].'"';

					$parc_existe2=ATF::db()->sql2array($query);

					if(!$parc_existe1 && !$parc_existe2){
						$cl=$commande_lignes[0];
						$cl=array("id_commande_ligne"=>$commande_lignes[0]["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
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
					$cl=$commande_lignes[0];
					$cl=array("id_commande_ligne"=>$commande_lignes[0]["id_commande_ligne"],"serial"=>$commande_lignes[0]["serial"]." ".$item["serial"],"id_produit"=>$item["id_produit"]);
				}else{
					$cl=$commande_lignes[0];
					$cl=array("id_commande_ligne"=>$commande_lignes[0]["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
				}
			}
		}elseif(count($commande_lignes)>1){
			foreach($commande_lignes as $k=>$i){
				if($i["quantite"]==1){
					if($i["serial"]==$item["serial"]){
						return $i["id_commande_ligne"];
					}elseif(!$i["serial"]){
						$query='SELECT * 
								 FROM  `parc` 
								 WHERE `provenance` = '.$item["id_affaire"].'
								 AND `ref` = "'.$i["ref"].'"
								 AND `libelle` = "'.$i["produit"].'"';
	
						$parc_existe1=ATF::db()->sql2array($query);
	
						$query='SELECT * 
								 FROM  `parc` 
								 WHERE `provenance` = '.$commande_lignes[0]["id_affaire_provenance"].'
								 AND `ref` = "'.$commande_lignes[0]["ref"].'"
								 AND `libelle` = "'.$commande_lignes[0]["produit"].'"';
	
						$parc_existe2=ATF::db()->sql2array($query);
	
						if(!$parc_existe1 && !$parc_existe2){
							$cl=$commande_lignes[0];
							$cl=array("id_commande_ligne"=>$i["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
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
						$cl=$commande_lignes[0];
						$cl=array("id_commande_ligne"=>$i["id_commande_ligne"],"serial"=>$i["serial"]." ".$item["serial"],"id_produit"=>$item["id_produit"]);
					}else{
						$cl=$commande_lignes[0];
						$cl=array("id_commande_ligne"=>$i["id_commande_ligne"],"serial"=>$item["serial"],"id_produit"=>$item["id_produit"]);
					}
				}
			}
			if(!$cl){
				$probleme="\les bons de commandes son déjà serialisé et le parc n'y est pas";
			}
		}else{
			$probleme="\pas de commande mais un parc";
		}
	
		if($cl!=$commande_lignes[0] && $cl){
print_r("\n Modif");
print_r($commande_lignes[0]);
print_r($cl);
			ATF::commande_ligne()->u($cl);
			return $cl["id_commande_ligne"];
		}
		return false;

	}else{
		$probleme="\n Pas de commande parent";
//		print_r("\n Pas de commande parent");
	}
	return false;
}

?>

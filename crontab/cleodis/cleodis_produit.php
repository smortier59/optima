<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

 //Convention de nommage et création du code 
for ($n=1;$n<9;$n++) {
	if ($n==3 || $n==5 || $n==6 || $n==7) continue;
	for ($i = 1.5; $i < 5; $i += .1) {
		$frequence[($i*10).$n] = $n."x ".number_format($i,1,"."," ")." gHz";
	}
}
for ($i = 12; $i < 33; $i += 1){
	$viewable[$i] = number_format($i,0,"."," ")." pouces";
}
for ($i = 0.20; $i < 0.30; $i += .01){
	$dotpitch[$i*100] = number_format($i,1,"."," ");
}

$id=0;
for ($n=1;$n<8;$n++) {
	for ($i=2;$i<71;$i++) {
		$b = $id % 75;
		$a = floor($id/75);
		$dd_tab[chr(48+$a).chr(48+$b)] = $n."x ".$i."0 Go";
		$id++;
	}
}

$codes["type"] = array("W"=>"Station","P"=>"Portable","S"=>"Serveur","C"=>"Client léger");
$codes["ram"] = array("0"=>"64 Mo","A"=>"128 Mo","7"=>"192 Mo","1"=>"256 Mo","8"=>"384 Mo","2"=>"512 Mo","B"=>"768 Mo","3"=>"1024 Mo","C"=>"1536 Mo","4"=>"2048 Mo","5"=>"3072 Mo","6"=>"4096 Mo");
$codes["puissance"] = $frequence;
$codes["typeecran"] = array("T"=>"Ecran TFT","C"=>"Ecran Cathodique");
$codes["viewable"] = $viewable;
$codes["dotpitch"] = $dotpitch;
$codes["technique"] = array("E"=>"Imprimante laser couleur", "O"=>"Imprimante laser monochrome","J"=>"Imprimante jet d'encre","S"=>"Imprimante à Sublimation","M" => "Imprimante matricielle", "T"=>"Traceur", "Q"=>"Imprimante étiquette");;
$codes["dd"] = $dd_tab;//array("06"=>"60 Go","08"=>"80 Go","10"=>"100 Go","15"=>"150 Go","16"=>"160 Go","18"=>"180 Go","20"=>"200 Go","25"=>"250 Go","30"=>"300 Go");
$codes["format"] = array("0"=>"A0","1"=>"A1","2"=>"A2","3"=>"A3","4"=>"A4","5"=>"A5","6"=>"A6");
$codes["lan"] = array("0"=>"10 Mbit","1"=>"100 Mbit","2"=>"1000 Mbit","3"=>"10000 Mbit");
$codes["OS"] = array("WVB"=>"Windows Vista Basic","WBE"=>"Windows Vista Enterprise","WVP"=>"Windows Vista Premium","WBU"=>"Windows Vista Ultimate", "WXP"=>"Windows XP", "W00"=>"Windows 2000", "XPP"=>"Windows XP PRO", "XPH"=>"Windows XP Family", "W03"=>"Windows 2003 Server", "MAC"=>"MacOS X", "LIN"=>"Linux");
$codes["lecteur"] = array("CDRO"=>"Lecteur de CD", "DVRO"=>"Lecteur de DVD", "CDRW"=>"Graveur de CD", "DVRW"=>"Graveur de DVD", "COMB"=>"Combo");
$codes["garantie"] = array("1"=>"Garantie 1 an", "2"=>"Garantie 2 ans","3"=>"Garantie 3 ans");
$codes["garantie2"] = array("1"=>"Garantie 1 an", "2"=>"Garantie 2 ans","3"=>"Garantie 3 ans");
$codes["garantie3"] = array("1"=>"Garantie 1 an", "2"=>"Garantie 2 ans","3"=>"Garantie 3 ans");

ATF::produit()->q->reset()->setCount();

$produits=ATF::produit()->sa();
$nb=0;
foreach($produits["data"] as $key=>$item){
	$nb++;
	print_r("\n".$nb." \ ".$produits["count"]);
	if($item["code"]){
		$marque=substr($item["code"],0,2);
		$type=substr($item["code"],2, 1);
		$processeur=substr($item["code"],3, 3);
		$puissance=substr($item["code"],6, 3);
		$ram=substr($item["code"],9, 1);
		$dd=substr($item["code"],10, 2);
		$lecteur=substr($item["code"],12, 4);
		$lan=substr($item["code"],16, 3);
		$OS=substr($item["code"],17, 3);
		$garantie=substr($item["code"],20, 1);
		$marque2=substr($item["code"],21, 1);
		$typeecran=substr($item["code"],23, 1);
		$viewable=substr($item["code"],24, 2);
		$garantie2=substr($item["code"],26, 1);
		$marque3=substr($item["code"],27, 2);
		$technique=substr($item["code"],29, 1);
		$format=substr($item["code"],30, 1);
		$garantie3=substr($item["code"],31, 1);

		$produit=array();
		
		if($type){
			if ($codes["type"][$type]) {
				ATF::produit_type()->q->reset()->addCondition("produit_type",$codes["type"][$type]);
				if($produit_type=ATF::produit_type()->select_all()){
					$produit["id_produit_type"]=$produit_type[0]["id_produit_type"];
				}
			}
		}
		if(!$produit["id_produit_type"]){
			$produit["id_produit_type"]=NULL;
		}

	
		if($processeur=ATF::processeur()->select(intval($processeur))){
			$produit["id_processeur"]=intval($processeur);
		}
		if(!$produit["id_processeur"]){
			$produit["id_processeur"]=NULL;
		}

		
		if($puissance){
			if ($codes["puissance"][$puissance]) {
				ATF::produit_puissance()->q->reset()->addCondition("produit_puissance",$codes["puissance"][$puissance]);
				if($produit_puissance=ATF::produit_puissance()->select_all()){
					$produit["id_produit_puissance"]=$produit_puissance[0]["id_produit_puissance"];
				}
			}
		}
		if(!$produit["id_produit_puissance"]){
			$produit["id_produit_puissance"]=NULL;
		}

	
		if($ram){
			if ($codes["ram"][$ram]) {
				ATF::produit_ram()->q->reset()->addCondition("produit_ram",$codes["ram"][$ram]);
				if($produit_ram=ATF::produit_ram()->select_all()){
					$produit["id_produit_ram"]=$produit_ram[0]["id_produit_ram"];
				}
			}
		}
		if(!$produit["id_produit_ram"]){
			$produit["id_produit_ram"]=NULL;
		}

		
		if($dd){
			if ($codes["dd"][$dd]) {
				ATF::produit_dd()->q->reset()->addCondition("produit_dd",$codes["dd"][$dd]);
				if($produit_dd=ATF::produit_dd()->select_all()){
					$produit["id_produit_dd"]=$produit_dd[0]["id_produit_dd"];
				}
			}
		}
		if(!$produit["id_produit_dd"]){
			$produit["id_produit_dd"]=NULL;
		}


		if($lecteur){
			if ($codes["lecteur"][$lecteur]) {
				ATF::produit_lecteur()->q->reset()->addCondition("produit_lecteur",$codes["lecteur"][$lecteur]);
				if($produit_lecteur=ATF::produit_lecteur()->select_all()){
					$produit["id_produit_lecteur"]=$produit_lecteur[0]["id_produit_lecteur"];
				}
			}
		}
		if(!$produit["id_produit_lecteur"]){
			$produit["id_produit_lecteur"]=NULL;
		}


		if($lan){
			if ($codes["lan"][$lan]) {
				ATF::produit_lan()->q->reset()->addCondition("produit_lan",$codes["lan"][$lan]);
				if($produit_lan=ATF::produit_lan()->select_all()){
					$produit["id_produit_lan"]=$produit_lan[0]["id_produit_lan"];
				}
			}
		}
		if(!$produit["id_produit_lan"]){
			$produit["id_produit_lan"]=NULL;
		}


		if($OS){
			if ($codes["OS"][$OS]) {
				ATF::produit_OS()->q->reset()->addCondition("produit_OS",$codes["OS"][$OS]);
				if($produit_OS=ATF::produit_OS()->select_all()){
					$produit["id_produit_OS"]=$produit_OS[0]["id_produit_OS"];
				}
			}
		}
		if(!$produit["id_produit_OS"]){
			$produit["id_produit_OS"]=NULL;
		}


		if($garantie){
			if ($codes["garantie"][$garantie]) {
				ATF::produit_garantie()->q->reset()->addCondition("produit_garantie",$codes["garantie"][$garantie]);
				if($produit_garantie=ATF::produit_garantie()->select_all()){
					$produit["id_produit_garantie_uc"]=$produit_garantie[0]["id_produit_garantie"];
				}
			}
		}
		if(!$produit["id_produit_garantie_uc"]){
			$produit["id_produit_garantie_uc"]=NULL;
		}


		if($typeecran){
			if ($codes["typeecran"][$typeecran]) {
				ATF::produit_typeecran()->q->reset()->addCondition("produit_typeecran",$codes["typeecran"][$typeecran]);
				if($produit_typeecran=ATF::produit_typeecran()->select_all()){
					$produit["id_produit_typeecran"]=$produit_typeecran[0]["id_produit_typeecran"];
				}
			}
		}
		if(!$produit["id_produit_typeecran"]){
			$produit["id_produit_typeecran"]=NULL;
		}


		if($viewable){
			if ($codes["viewable"][$viewable]) {
				ATF::produit_viewable()->q->reset()->addCondition("produit_viewable",$codes["viewable"][$viewable]);
				if($produit_viewable=ATF::produit_viewable()->select_all()){
					$produit["id_produit_viewable"]=$produit_viewable[0]["id_produit_viewable"];
				}
			}
		}
		if(!$produit["id_produit_viewable"]){
			$produit["id_produit_viewable"]=NULL;
		}


		if($garantie2){
			if ($codes["garantie2"][$garantie2]) {
				ATF::produit_garantie()->q->reset()->addCondition("produit_garantie",$codes["garantie2"][$garantie2]);
				if($produit_garantie=ATF::produit_garantie()->select_all()){
					$produit["id_produit_garantie_ecran"]=$produit_garantie[0]["id_produit_garantie"];
				}
			}
		}
		if(!$produit["id_produit_garantie_ecran"]){
			$produit["id_produit_garantie_ecran"]=NULL;
		}


		if($technique){
			if ($codes["technique"][$technique]) {
				ATF::produit_technique()->q->reset()->addCondition("produit_technique",ATF::db()->real_escape_string($codes["technique"][$technique]));
				if($produit_technique=ATF::produit_technique()->select_all()){
					$produit["id_produit_technique"]=$produit_technique[0]["id_produit_technique"];
				}
			}
		}
		if(!$produit["id_produit_technique"]){
			$produit["id_produit_technique"]=NULL;
		}


		if($format){
			if ($codes["format"][$format]) {
				ATF::produit_format()->q->reset()->addCondition("produit_format",$codes["format"][$format]);
				if($produit_format=ATF::produit_format()->select_all()){
					$produit["id_produit_format"]=$produit_format[0]["id_produit_format"];
				}
			}
		}
		if(!$produit["id_produit_format"]){
			$produit["id_produit_format"]=NULL;
		}


		if($garantie3){
			if ($codes["garantie3"][$garantie3]) {
				ATF::produit_garantie()->q->reset()->addCondition("produit_garantie",$codes["garantie3"][$garantie3]);
				if($produit_garantie=ATF::produit_garantie()->select_all()){
					$produit["id_produit_garantie_imprimante"]=$produit_garantie[0]["id_produit_garantie"];
				}
			}
		}
		if(!$produit["id_produit_garantie_imprimante"]){
			$produit["id_produit_garantie_imprimante"]=NULL;
		}
		
		
		
		$produit["id_produit"]=$item["id_produit"];
		ATF::produit()->update($produit);
	}
}
?>
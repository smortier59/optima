<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

//echo "HELLO WORLD";
//log::logger("HELLO WORLD","amaitre");

/*ATF::stock()->q->setToString();
log::logger(ATF::stock()->select_all(),"amaitre");
ATF::stock()->q->unsetToString();*/

// Selectionner tous les stocks qui ont comme dernier état 'STOCK'
// select all, querier : $this->q


ATF::stock()->q->reset()
			   ->where("to_magento", "oui")
			   ->whereIsNotNull("categories_magento")
			   ->whereIsNotNull("description")
			   ->whereIsNotNull("ref")
			   ->whereIsNotNull("prix")
			   ->addGroup("ref");

$db = ATF::stock()->sa();
$tabl_factice = array();

foreach ($db as $k=>$i) {
	if ($i["id_stock"]) {
		if (ATF::stock_etat()->getEtat($i["id_stock"]) == "stock"){
			$tabl_factice[] = $i;
		}
	}
}

$fich = fopen("Produit_mag.csv", "w+");
fputs($fich, "sku,_attribute_set,_type,qty,description,image,is_in_stock,media_gallery,name,price,short_description,small_image,status,tax_class_id,thumbnail,visibility,weight,_category,_media_attribute_id,_media_image,_media_is_disabled,_product_websites,_root_category\n");


$count_fich = 0;

foreach ($tabl_factice as $i) {

	if (strlen($i["libelle"]) > 255) { 					//Le name ne peut pas dépasser 255 caractère, sinon il n'est pas pris par magento
		$i["libelle"] = substr($i["libelle"], 0, 255);
	}
	$line = array(
		"sku"=>$i['ref'],
		"_attribute_set"=>"Default",					//Attribut à mettre par défaut, non optionnel
		"_type"=>"simple",								//Attribut à mettre par défaut, non optionnel
		"qty"=> number_format(ATF::stock()->getQuantity($i["ref"]),4),
		"description"=>$i["libelle"],
		"image" => $i["id_stock"].".photo.png",
		"is_in_stock"=>1,
		"media_gallery" => $i["id_stock"].".photo.png",
		"name"=>$i["libelle"],
		"price"=>($i["prix"]?$i["prix"]:1),
		"short_description"=>$i["short_description"],
		"small_image" => $i["id_stock"].".photo.png",
		"status"=>1,
		"tax_class_id"=>2,
		"thumbnail" => $i["id_stock"].'.photo.png',
		"visibility"=>4,
		"weight"=>number_format($i['poids']/1000, 4),
		"_category"=>ATF::$usr->trans($i['categories_magento'], "stock"),			//Chemin de l'emplacement de l'objet dans le magasin
		"_media_attribute_id" => 88,
		"_media_image" => $i["id_stock"].".photo.png",
		"_media_is_disabled" => 0,
		"_product_websites"=>"base",					//Permets le stock des produits dans le frontend et pas seulement le backend
		"_root_category"=>"Default Category" 			//Permets de sélectionner le magasin
	);
	$count_fich++;
	fputcsv($fich, $line);
	fputs("\n");
}

fclose($fich);


?>

<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cleodis";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// include(dirname(__FILE__)."/../../includes/cleodis/boulangerpro.class.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

$GLOBALS['logFile'] = "maj-garantie-batch";

// MARK: STRUCTS
class ServiceEntity {
    public $reference = "";
	public $price_tax_incl = 0.0;
	public $price_tax_excl = 0.0;
	public $name = "";
	public $type = "";
    public $nb_years = 0;
    public $rate = 0.0;
    public $rent = 0.0;

    function __construct(...$args) {
        $this->reference = $args[0]["reference"];
        $this->price_tax_incl = $args[0]["price_tax_incl"];
        $this->price_tax_excl = $args[0]["price_tax_excl"];
        $this->name = $args[0]["name"];
        $this->type = $args[0]["type"];
        $this->nb_years = $args[0]["nb_years"];
        log::logger("====== Garantie ======", $GLOBALS['logFile']);
        log::logger("price_tax_incl: $price_tax_incl", $GLOBALS['logFile']);
        log::logger("price_tax_excl: $price_tax_excl", $GLOBALS['logFile']);
        log::logger("nb_years: $nb_years", $GLOBALS['logFile']);
    }

    public function set_rate(float $rate):ServiceEntity {
        log::logger("Taux calcule: $rate", $GLOBALS['logFile']);
        $this->rate = $rate;
        return $this;
    }

    public function set_rent(float $rent):ServiceEntity {
        log::logger("Loyer calcule: $rent", $GLOBALS['logFile']);
        $this->rent = $rent;
        return $this;
    }
}

class MappedResponse {
    public $reference = "";
    public $services = [];
    
    function __construct(...$args) {
        $this->reference = $args[0]["reference"];
        if(isset($args[0]["services"])) {
            $this->_push_service_object($args[0]["services"]);
        }
    }

    public function _push_service_object($services) {
        foreach ($services as $service){
            array_push($this->services, new ServiceEntity($service));
        }
    }
}




function get_all_products(): Array {
    ATF::produit()->q
        ->reset()
        ->from("produit","id_sous_categorie", "sous_categorie","id_sous_categorie")
        ->where("sous_categorie.sous_categorie", "%Extension de garantie%", "OR", false, "LIKE");     
    $items = ATF::produit()->sa();
    
    $total_of_products = count($items);
    log::logger("Total of products: $total_of_products", $GLOBALS['logFile']);

    return ($total_of_products > 0 ? $items : Array());
}

function does_product_by_ref_exist($reference): bool{
    ATF::produit()->q
        ->reset()
        ->where("ref", $reference);     
    $items = ATF::produit()->sa();
    $doesIsExist = isset($items) ? true : false;
    log::logger("Warranty ref# $reference does exist = $doesIsExist", $GLOBALS['logFile']);
    
    return $doesIsExist;
}

function update_warranty_financial_attributes(string $product_id, ServiceEntity $service){
    if(!$service->rate || !$service->rent || !$service->price_tax_excl) {
        throw new LogicException('Rate, rent or price_tax_excl cannot be equal to zero');
    }

    ATF::produit()->update(array(
        "loyer"=>$service->rent,
        "prix_achat"=>$service->price_tax_excl,
        "id_produit"=>$product_id,
    ));

    ATF::db()->commit_transaction();

    log::logger("Warranty id# $product_id updated", $GLOBALS['logFile']);
}

function main() {
    $boulanger = ATF::boulangerpro();
    $pack_produit = ATF::pack_produit();

    foreach (get_all_products() as $product) {
        $pack_ids_list = explode(",", $pack_produit->getIdPackFromProduit($product['id_produit']));
        $pack_ids_list_count = count($pack_ids_list);
        log::logger("getIdPackFromProduit count: $pack_ids_list_count", $GLOBALS['logFile']);

        foreach ($pack_ids_list as $key => $pack_ids_list_item) {
            $product_id = $pack_produit->getProduitPrincipal($pack_ids_list_item);
            log::logger("getProduitPrincipalt: $product_id", $GLOBALS['logFile']);

            $product_id_buffer = $product['id_produit'];

            if(isset($product_id)){
                $product = ATF::produit()->select($product_id);
                $responses_from_boulanger_api = $boulanger->APIBoulPROservice($product['ref'], $GLOBALS['logFile']);
                foreach ($responses_from_boulanger_api as $response) {
                    if($response == 'unfound_product') {
                        log::logger("$response" .$product_id."", $GLOBALS['logFile']);
                        break;
                    }

                    $mapped = new MappedResponse($response);
                    foreach($mapped->services as $service) {
                        $service->set_rate($boulanger->getTaux($service->price_tax_incl))
                                ->set_rent($boulanger->vpm(($service->rate / 12), $product['duree'],-$service->price_tax_incl, 0, 1));

                        if(does_product_by_ref_exist($service->reference)) {
                           try {
                            update_warranty_financial_attributes($product_id_buffer, $service);
                            log::logger("====== PRODUIT ======", $GLOBALS['logFile']);
                            log::logger("Reference" .$product["ref"]."", $GLOBALS['logFile']);
                            log::logger("Nom" .$product["produit"]."", $GLOBALS['logFile']);
                            log::logger("via pack id#" .$pack_ids_list_item."", $GLOBALS['logFile']);
                            
                           } catch (Exception $e) {
                            $error_type = get_class($e);
                            $error_message = $e->getMessage();
                            log::logger("$error_type", $GLOBALS['logFile']);
                            log::logger("$error_message", $GLOBALS['logFile']);
                           }
                        }
                    }
                }
            }
        }
    }
}


log::logger("========= DEBUT DE SCRIPT =========", $GLOBALS['logFile']);
// Début de transaction SQL
ATF::db()->begin_transaction(true);
main();
// Rollback la transaction
//ATF::db()->rollback_transaction();
// Valide la trnasaction
ATF::db()->commit_transaction();
log::logger("========= FIN DE SCRIPT =========", $GLOBALS['logFile']);


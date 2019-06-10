
<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cleodis";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// include(dirname(__FILE__)."/../../includes/cleodis/boulangerpro.class.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);


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
        log::logger("====== Garantie ======", 'warranty_migration');
        log::logger("price_tax_incl: $price_tax_incl", 'warranty_migration');
        log::logger("price_tax_excl: $price_tax_excl", 'warranty_migration');
        log::logger("nb_years: $nb_years", 'warranty_migration');
    }

    public function set_rate(float $rate):ServiceEntity {
        log::logger("Taux calcule: $rate", 'warranty_migration');
        $this->rate = $rate;
        return $this;
    }

    public function set_rent(float $rent):ServiceEntity {
        log::logger("Loyer calcule: $rent", 'warranty_migration');
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

log::logger("========= DEBUT DE SCRIPT =========", 'warranty_migration');

// Début de transaction SQL
ATF::db()->begin_transaction(true);

// Rollback la transaction
//ATF::db()->rollback_transaction();
// Valide la trnasaction
// ATF::db()->commit_transaction();

function get_all_products(): Array {
    ATF::produit()->q
        ->reset()
        ->from("produit","id_sous_categorie", "sous_categorie","id_sous_categorie")
        ->where("sous_categorie.sous_categorie", "%Extension de garantie%", "OR", false, "LIKE")
        ->where("produit.ref", 656709);     
    $items = ATF::produit()->sa();
    
    $total_of_products = count($items);
    log::logger("Total of products: $total_of_products", 'warranty_migration');

    return ($total_of_products > 0 ? $items : Array());
}

function does_product_by_ref_exist($reference): bool{
    ATF::produit()->q
        ->reset()
        ->where("ref", $reference);     
    $items = ATF::produit()->sa();
    $doesIsExist = isset($items) ? true : false;
    log::logger("Warranty ref# $reference does exist = $doesIsExist", 'warranty_migration');
    
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

    log::logger("Warranty id# $product_id updated", 'warranty_migration');
}

function main() {
    $boulanger = ATF::boulangerpro();
    $pack_produit = ATF::pack_produit();

    foreach (get_all_products() as $product) {
        $pack_ids_list = explode(",", $pack_produit->getIdPackFromProduit($product['id_produit']));
        $pack_ids_list_count = count($pack_ids_list);
        log::logger("getIdPackFromProduit count: $pack_ids_list_count", 'warranty_migration');

        foreach ($pack_ids_list as $key => $pack_ids_list_item) {
            $product_id = $pack_produit->getProduitPrincipal($pack_ids_list_item);
            log::logger("getProduitPrincipalt: $product_id", 'warranty_migration');

            $product_id_buffer = $product['id_produit'];

            if(isset($product_id)){
                $product = ATF::produit()->select($product_id);
                $responses_from_boulanger_api = $boulanger->APIBoulPROservice($product['ref'], 'warranty_migration');
                foreach ($responses_from_boulanger_api as $response) {
                    if($response == 'unfound_product') {
                        log::logger("$response" .$product_id."", 'warranty_migration');
                        break;
                    }

                    $mapped = new MappedResponse($response);
                    foreach($mapped->services as $service) {
                        $service->set_rate($boulanger->getTaux($service->price_tax_incl))
                                ->set_rent($boulanger->vpm(($service->rate / 12), $product['duree'],-$service->price_tax_incl, 0, 1));

                        if(does_product_by_ref_exist($service->reference)) {
                           try {
                            update_warranty_financial_attributes($product_id_buffer, $service);
                            log::logger("====== PRODUIT ======", 'warranty_migration');
                            log::logger("Reference" .$product["ref"]."", 'warranty_migration');
                            log::logger("Nom" .$product["produit"]."", 'warranty_migration');
                            log::logger("via pack id#" .$pack_ids_list_item."", 'warranty_migration');
                            
                           } catch (Exception $e) {
                            $error_type = get_class($e);
                            $error_message = $e->getMessage();
                            log::logger("$error_type", 'warranty_migration');
                            log::logger("$error_message", 'warranty_migration');
                           }
                        }
                    }
                }
            }
        }
    }
}

log::logger("========= FIN DE SCRIPT =========", 'warranty_migration');

main();
<?
/**
 * Classe affaire
 * @package Optima
 */
require_once dirname(__FILE__)."/../affaire.class.php";
class affaire_boisethome extends affaire {
	/**
	 * Constructeur
	 */
	public function __construct() {
		parent::__construct();
		$this->table = "affaire";
		$this->fieldstructure();
		$this->no_insert=true;
		$this->onglets = array('devis','facture');
	}

	/**
    * Retourne la ref d'une affaire autre qu'avenant
    * @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
	function getRef($date,$class){
		if (!$date) {
			throw new error(ATF::$usr->trans("impossible_de_generer_la_ref_sans_date"),321);	
		}
		if($class=="devis"){
			$prefix="D";
		}elseif($class=="commande"){
			$prefix="C";
		}elseif($class=="facture"){
			$prefix="F";
		}
		$prefix.=strtoupper(substr(ATF::agence()->nom(ATF::$usr->get('id_agence')),0,2)).date("ym",strtotime($date));
		ATF::$class()->q->reset()
					   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
					   ->addField('SUBSTRING(`ref`,8)+1',"max_ref")
					   ->addOrder('ref',"DESC")
					   ->setDimension("row")
					   ->setLimit(1);
		$nb=ATF::$class()->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="0001";
		}
		return $prefix.$suffix;
	}}
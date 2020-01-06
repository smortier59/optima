<?
/** 
* Classe print_etat
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class print_etat_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
        $this->table='print_etat';
		$this->colonnes['fields_column']  = array(
			'print_etat.id_stock'
			,'print_etat.name'
			,'print_etat.date'
			,'print_etat.color'
			,'print_etat.current'
			,'print_etat.max'
			,'print_etat.type'
		); 
	}
	
	/**
	* Fonction _GET pour telescope
	* @package Telescope - Hyperviseur CC
	* @author Charlier Cyril <ccharlier@absystech.fr>
	* @param $get array.
	* @param $post array Argument obligatoire.
	* @return boolean | integer
	*/
	public function _GET($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "id_stock";
		if (!$get['trid']) $get['trid'] = "desc";
		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;
		if($get['graph']) $get['page'] = false;
		
		$colsData = array(
			'id_stock'
			,'date'
			,'name'
			,'color'
			,'current'
			,'max'
			,'type'
		);

		$this->q->reset();
		$this->q->addField($colsData);
		if ($get['id_stock']) {
			$this->q->where("id_stock",$get['id_stock'])
					->addOrder('date','desc');

		}
		if ($get['id_stock'] && $get['graph'] != 'true'){
			$this->q->setLimit($get['limit']); 			
		}
		if ($get['graph'] == 'true'){
			$range = ($get['range'] && $get['range']=="three")?date('Y-m-d H:i:s',strtotime('-3 years')):date('Y-m-d H:i:s',strtotime('-1 years'));
			$this->q->andWhere("date",$range,false,">")
					->addOrder('date','asc');
		}
		$this->q->setCount();
		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}				
			}
		}
		if ($get['id_stock'] && $get['graph'] != 'true') {
			$test = array();

			foreach ($data['data'] as $key => $value) {
				if(!$test[$value['name']]){
					$test[$value['name']] = $value;
				}
			}
			$data['data'] = array_values($test);
			$order = array('cyan','magenta','yellow','black',null,null);
			usort($data['data'], function ($item1, $item2) use ($order){
				$pos_a = array_search($item1['color'], $order);
    			$pos_b = array_search($item2['color'], $order);
    			return $pos_a - $pos_b;
			});
		}
		if (!$get['id_stock']){
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);
		}
		$return = $data['data'];
		return $return;
	}
	/**
	* Permet d'ajouter une ou plusieurs informations sur l'imprimante
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @package Telescope\Printer
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/  	
	public function _POST($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
		// ajout de toutes les cartouches & de leur état
		$etat = json_decode($post['etat'],true);
		foreach ($etat['toners'] as $k=>$i) {
			$toinsert[]= array(
				"id_stock"=>$post['id_stock']
				,"name"=>$i['name']
				,"current"=>$i['current']
				,"date"=>$etat['date']
				,"color"=>$i['color']
				,"max"=>$i['max']
				,"type"=>'toner'
			);
		}
		// Ajouter maintenant les cout copies
		foreach ($etat['copies'] as $k=>$i) {
			$toinsert[]= array(
				"id_stock"=>$post['id_stock']
				,"name"=>$k
				,"current"=>$i
				,"date"=>$etat['date']
				,"type"=> ($k == 'mono')?'copie_noir':'copie_couleur'
				,"color"=> NULL
				,"max"=>NULL

			);
		}

        try {	  
        	ATF::db($this->db)->begin_transaction();  		
			$result = $this->multi_insert($toinsert);
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
  			throw new errorATF($e->getMessage(),500);
		}
		ATF::db($this->db)->commit_transaction();

        $return['result'] = true;
        $return['etat_imprimante'] = $result['Records'];
        return $return;
	}


}

?>

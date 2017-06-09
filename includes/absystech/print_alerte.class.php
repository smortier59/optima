<?
/** 
* Classe print_alerte
* @author Cyril Charlier <ccharlier@absystech.fr>
* @package Optima
* @subpackage Absystech
*/
class print_alerte_absystech extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
        $this->table='print_alerte';
		$this->colonnes['fields_column']  = array(
			'print_alerte.id_stock'
			,'print_alerte.code'
			,'print_alerte.message'
			,'print_alerte.date'
			,'print_alerte.notification'
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
		if (!$get['tri']) $get['tri'] = "date";
		if (!$get['trid']) $get['trid'] = "desc";
		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;
		// Gestionde la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			'id_stock'
			,'code'
			,'message'
			,'id_print_alerte'
			,'date'
			,'notification'
			,'date_cloture'
		);


		$this->q->reset();
		$this->q->addField($colsData);

		if ($get['id_stock']) {
			$this->q->where("id_stock",$get['id_stock']);
		}
		if ($get['date_cloture'] && $get['date_cloture']==='NULL') {
			$this->q->addCondition("date_cloture",null,"OR",false,"IS NULL");
		}
		$this->q->addOrder('date','desc');
		$this->q->setLimit($get['limit']);
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
		if (!$get['id_stock']){
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);
		}
		$return = $data['data'];
			
		return $return;
	}
	/**
	* Permet d'ajouter une ou plusieurs notifications
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/  	
	public function _POST($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
		$alertes = json_decode($post['alerts'],true);
        try {	  
        	ATF::db($this->db)->begin_transaction();  		
			foreach ($alertes as $k=>$i) {
				$toinsert[]= array(
					"code"=>$i['code']
					,"message"=>$i['message']
					,"id_stock"=>$post['id_stock']
					,'date'=>$post['date']
				);
			}
			$result = $this->multi_insert($toinsert);
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
  			throw new errorATF($e->getMessage(),500);
		}
		ATF::db($this->db)->commit_transaction();

        $return['result'] = true;
        $return['records'] = $result['Records'];
        return $return;
	}
	/**
	* Permet de modifier une notification d'alerte
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array Contient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/  
	public function _PUT($get,$post){
		$input = file_get_contents('php://input');
	    if (!empty($input)) parse_str($input,$post);
		$return = array();
		if($post['id_print_alerte']){
			$post['notification']='non';
			$this->update($post);
		}else{
		    try {
		    	foreach ($post as $key => $value) {
			    	$this->update($value);
		    	}
		    } catch (errorATF $e) {
	  			throw new errorATF($e->getMessage(),500);
		    }			
		}

        $return['result'] = true;
        return $return;
	}


}

?>

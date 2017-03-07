<?
/** Classe constante - Gestion de l'constante
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__).("/../../libs/ATF/includes/constante.class.php");
class constante_lm extends constante {

	function __construct($table_or_id=NULL) {
		$this->table = "constante";
		parent::__construct($table_or_id);
		$this->fieldstructure();
	}


	public function getSequence($constante){

		$this->q->reset()->where("constante",$constante);
		$constData = $this->select_row();

		$const = intval($constData["valeur"]) + 1;

		$this->u(array("id_constante"=>$constData["id_constante"], "valeur"=>$const));



		if($const < 10){
			$const = "0000".$const;
		}elseif ($const < 100) {
			$const = "000".$const;
		}elseif ($const < 1000) {
			$const = "00".$const;
		}elseif ($const < 10000) {
			$const = "0".$const;
		}
		return $const;
	}

};
?>
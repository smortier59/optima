<?
/** Classe constante - Gestion de l'constante
* @package Optima
* @subpackage Cleodis
*/

class constante_lm extends constante {

	function __construct($table_or_id=NULL) {
		$this->table = "constante";
		parent::__construct($table_or_id);
		$this->fieldstructure();
	}


	public function getSequence($constante){

		$this->q->reset()->where("constante",$constante);
		$const = $this->select_row();

		$const = intval($const) + 1;

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
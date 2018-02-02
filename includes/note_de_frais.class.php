<?
/**
* @package Optima
*/
class note_de_frais extends classes_optima {

	private $jourLimit = 26;
	private $idSmortier = 3;
	private $idMmortier = 5;
	private $idMmortierATT = 31;

	private $idTPruvost = 64;
	private $idTPruvostATT = 49;

	private $idLRibeiro = 68;
	private $idLRibeiroATT = 54;


	public $mailFrais = "frais@absystech.net";

	private $god = array();

	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;

		$this->colonnes['fields_column'] = array(
			'note_de_frais.note_de_frais'
			,'note_de_frais.id_user'
			,'note_de_frais.etat'=>array("renderer"=>"etat","width"=>30)
			,'validation'=>array("renderer"=>"ndfValidation","custom"=>true,"nosort"=>true,"width"=>60,"align"=>"center")
			,'montant'=>array("renderer"=>"money","custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","nosearch"=>true)
		);
		$this->colonnes['primary'] = array(
			"date"
			,"id_user"
			,"etat"
		);
		$this->colonnes['panel']['lignes'] = array(
			"depenses"=>array("custom"=>true,"null"=>true)
		);

		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);

		// Champs masqués
		$this->colonnes['bloquees']['select'] =  array(
			'note_de_frais'
			,'depenses'
		);

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('note_de_frais','etat');

		$this->fieldstructure();
		$this->onglets = array('note_de_frais_ligne'=>array('opened'=>true));

		$this->files = array(
			"justificatifs"=>array("custom"=>true,'type'=>"zip","multiUpload"=>true,'opened'=>true)
		);
		$this->addPrivilege("valid","update");
		$this->addPrivilege("refus","update");

		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Notes_de_frais";

		$this->god[] = $this->idSmortier;
		$this->god[] = $this->idMmortier;
		$this->god[] = $this->idMmortierATT;
		$this->god[] = $this->idTPruvost;
		$this->god[] = $this->idTPruvostATT;
		$this->god[] = $this->idLRibeiro;
		$this->god[] = $this->idLRibeiroATT;

	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-03-2011
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field){

		switch ($field) {
			case "id_user":
				return ATF::$usr->getID();
			default:
				return parent::default_value($field);
		}
	}

	/**
	* Surcharge de l'insert afin d'insérer les lignes de note de frais, et initialise le nom comme il faut via la date
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-03-2011
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lignes = json_decode($infos["values_".$this->table]["depenses"],true);
		if(!$lignes){
			throw new errorATF(ATF::$usr->trans("aucunes_lignes_saisies"));
		}
		$this->infoCollapse($infos);

		ATF::db($this->db)->begin_transaction();

		// Insertion de la note de frais.
		$infos['note_de_frais'] = $this->getDateReference(strtotime($infos['date']));

		$id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		foreach ($lignes as $k=>$i) {
			foreach($i as $k_=>$i_){
				$k_unescape=util::extJSUnescapeDot($k_);
				$i[str_replace("note_de_frais_ligne.","",$k_unescape)]=$i_;
				unset($i[$k_]);
			}
			$i['id_note_de_frais'] = $id;
			$i['id_societe'] = ATF::societe()->decryptID($i['id_societe_fk']);
			$i['id_frais_kilometrique'] = ATF::societe()->decryptID($i['id_frais_kilometrique_fk']);
			unset($i['id_societe_fk'],$i['id_frais_kilometrique_fk']);
			ATF::note_de_frais_ligne()->insert($i,$s);
		}

		ATF::db($this->db)->commit_transaction();
//		ATF::db($this->db)->rollback();
		return $id;
	}

	/**
	* Surcharge de l'insert afin d'insérer les lignes de note de frais, et initialise le nom comme il faut via la date
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-03-2011
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lignes = json_decode($infos["values_".$this->table]["depenses"],true);

		if(!$lignes){
			throw new errorATF(ATF::$usr->trans("aucunes_lignes_saisies"));
		}
		$this->infoCollapse($infos);

		ATF::db($this->db)->begin_transaction();

		parent::update($infos,$s,$files,$cadre_refreshed,$nolog);

		foreach ($this->files as $k=>$i) {
			// Récupération des fichier a mettre dans le zip
			$dir = dirname($this->filepath(ATF::$usr->getID(),"*",true));
			if ($dir) {
				foreach (scandir($dir) as $k_=>$i_) {
					$f = explode(".",$i_);
					if ($f[0]!=ATF::$usr->getID()) continue;

					$filename = str_replace(ATF::$usr->getID().".".$k.".","",$i_);
					$fileToZip[$filename] = $dir."/".$i_;
					$filesToRm[] = $dir."/".$i_;
				}
			}

			if ($fileToZip) {
				$zip = new ZipArchive();
				$zipFileName = $this->filepath($this->decryptId($infos['id_note_de_frais']),$k);

				if (!file_exists($zipFileName)) {
					touch($zipFileName); // Nécessaire pour créer le fichier avant de l'open
				}

				if ($zip->open($zipFileName) !== TRUE) {
					ATF::db()->rollback_transaction();
					throw new errorATF("Problème avec l'ouverture du zip : ".$res,501);
				}

				foreach ($fileToZip as $k_=>$i_) {
					$zip->addFile($i_,$k_);
				}

				if (!$zip->close()) {
					ATF::db()->rollback_transaction();
					throw new errorATF("Problème avec la fermeture du zip.",502);
				}
				foreach ($fileToZip as $k_=>$i_) {
					util::rm($i_);
				}
			}
		}

		foreach ($lignes as $k=>$i) {
			foreach($i as $k_=>$i_){
				$k_unescape=util::extJSUnescapeDot($k_);
				$i[str_replace("note_de_frais_ligne.","",$k_unescape)]=$i_;
				unset($i[$k_]);
			}

			$i['id_note_de_frais'] = $infos['id_note_de_frais'];
			if (array_key_exists('id_societe_fk',$i)) {
				if  ($i['id_societe_fk']) $i['id_societe'] = ATF::societe()->decryptID($i['id_societe_fk']);
				unset($i['id_societe_fk']);
			} elseif ($i['id_societe']) {
				unset($i['id_societe']);
			}
			if ($i['id_frais_kilometrique_fk']) {
				$i['id_frais_kilometrique'] = ATF::societe()->decryptID($i['id_frais_kilometrique_fk']);
				unset($i['id_frais_kilometrique_fk']);
			} elseif ($i['id_frais_kilometrique']) {
				unset($i['id_frais_kilometrique']);
			}
			if ($i['id_frais_kilometrique_fk']=="") {
				unset($i['id_frais_kilometrique_fk']);
			}

			if ($i['id_note_de_frais_ligne']) {
				ATF::note_de_frais_ligne()->update($i,$s);
			} else {
				ATF::note_de_frais_ligne()->insert($i,$s);
			}
		}

		ATF::db($this->db)->commit_transaction();
		return true;
	}

	/**
    * Retourne VRAI si l'utilisateur peut accéder aux icônes de validation de la note de frais, sinon FALSE
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-03-2011
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function canValid($idUser){
		if (in_array(ATF::$usr->getId(),$this->god)) {
			return true;
		}
		if (ATF::$usr->getID()==ATF::user()->select($idUser,'id_superieur')) {
			return true;
		}
		return false;
	}

	/**
    * N'affiche que les notes de frais de l'utilisateur trié par date + l'aggrégat
    * @author Quentin JANON <qjanon@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q->addJointure("note_de_frais","id_note_de_frais","note_de_frais_ligne","id_note_de_frais")
					->addGroup("note_de_frais.id_note_de_frais")
					->addField("note_de_frais.id_user")
					->addField("SUM(note_de_frais_ligne.montant)","montant");
		if (!in_array(ATF::$usr->getId(),$this->god)) {
			$this->q->addCondition($this->table.".id_user",ATF::$usr->getID());
			ATF::user()->q->reset()->where("id_superieur",ATF::$usr->getID());
			foreach (ATF::user()->sa() as $k=>$i) {
				$this->q->addCondition($this->table.".id_user",$i['id_user']);
			}
		}
		$return = parent::select_all($order_by?$order_by:$this->table.".date",$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			if ($this->canValid($i['note_de_frais.id_user_fk'])) {
				$return['data'][$k]['canValid'] = true;
			} else {
				$return['data'][$k]['canValid'] = false;
			}
		}

		return $return;
	}

	/**
    * Prédicat retournant VRAI si la date limite est dépassée
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $time
	* @param boolean TRUE si la date est dépassée
    */
	public function checkDateLimite($time,$nf){

		// Récupérer le timestamp de la date de la note de frais, jour 20
		$dateLimitTmp = strtotime($nf['note_de_frais']."-".$this->jourLimit);
		$datelimit = mktime(0,0,0,date('m',$dateLimitTmp),date("d",$dateLimitTmp),date('Y',$dateLimitTmp));

		return $datelimit < $time;
	}

	/**
	* Retourne false car impossibilité
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 09-03-2011
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		$nf = $this->select($id);

		if (in_array(ATF::$usr->getId(),$this->god)) {
			return true;
		}

		//Si on a depassé le 20 du mois, impossible de modifier
		if ($nf['etat']!="en_cours") {
			throw new errorATF(ATF::$usr->trans("impossible_modifer_note_de_frais_qui_nest_pas_en_cours",$this->table),8766);
		} elseif ($this->checkDateLimite(time(),$nf)) {
			throw new errorATF(ATF::$usr->trans("impossible_modifer_note_de_frais_apres_le_20_du_mois",$this->table),8764);
		//Si on est le supérieur hiérarchique, on peut
		} elseif (ATF::user()->select($nf['id_user'],'id_superieur')==ATF::$usr->getID()) {
			return true;
		//Si on est pas le propriétaire de la note de frais, on peut pas
		} elseif ($nf['id_user']!=ATF::$usr->getID()) {
			throw new errorATF(ATF::$usr->trans("impossible_modifer_note_de_frais_dun_autre_user",$this->table),8765);
		}
		return true;
	}

	/**
	* Retourne false car impossibilité
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-03-2011
	* @return boolean
	*/
	public function isExists(){
		$this->q->reset()
					->addField("id_note_de_frais")
					->where("note_de_frais",$this->getDateReference())
					->where('id_user',ATF::$usr->getID())
					->setStrict()
					->setDimension('cell');

		// Check si on a depassé le 20 du mois courant
		if (strtotime($this->getDateReference()."-".$this->jourLimit) < time()) return false;

		return $this->sa();
	}

	/**
	* Retourne le nom d'une note de frais par rapport a une date.
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param date Date de base pour le calcul
	* @return string Date
	*/
	public function getDateReference($d=false){
		if (!$d) {
			$d = time();
		}
		if (date('d',$d)>=$this->jourLimit) {
			$d = mktime(0,0,0,date('m',$d)+1,1,date('Y',$d));
		}

		return date("Y-m",$d);
	}

	/**
	* Retourne le jour limit pour la saisi de la note de frais
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-03-2011
	* @return jourLimit
	*/
	public function getJourLimit(){
		return $this->jourLimit;
	}

	/**
	* Valide une  note de frais
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-03-2011
	* @return boolean
	*/
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lignes = ATF::note_de_frais_ligne()->ss("id_note_de_frais",$this->decryptID($infos['id_note_de_frais']));

		foreach ($lignes as $k=>$i) {
			if ($i['etat']=="en_cours") {
				$i['etat'] = "ok";
				ATF::note_de_frais_ligne()->update($i);
//				throw new errorATF("Toutes les lignes ne sont pas validées.");
			}
		}

		$u = array(
			"id_note_de_frais"=>$infos['id_note_de_frais']
			,"etat"=>"ok"
		);
		$this->u($u);

		return true;
	}

	/**
	* Refuse une  note de frais
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-03-2011
	* @return boolean
	*/
	public function refus($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$lignes = ATF::note_de_frais_ligne()->ss("id_note_de_frais",$this->decryptID($infos['id_note_de_frais']));

		foreach ($lignes as $k=>$i) {
			if ($i['etat']=="en_cours") {
				$i['etat'] = "nok";
				ATF::note_de_frais_ligne()->update($i);
//				throw new errorATF("Toutes les lignes ne sont pas validées.");
			}
		}

		$u = array(
			"id_note_de_frais"=>$infos['id_note_de_frais']
			,"etat"=>"nok"
		);
		$this->u($u);

		return true;
	}

	public function getTotal($id) {
		$this->q->reset()
				->addJointure("note_de_frais","id_note_de_frais","note_de_frais_ligne","id_note_de_frais")
				->addGroup("note_de_frais.id_note_de_frais")
				->addField("SUM(note_de_frais_ligne.montant)","montant")
				->where('note_de_frais.id_note_de_frais',$id)
				->setDimension('cell');
		return $this->sa();
	}


	public function checkEndMonth() {
		if (date("d")==$this->jourLimit) {
			$this->q->reset()->where("note_de_frais",date("Y-m"));
			if ($data = $this->sa()) {
				$info_mail["objet"] = "Notes de frais ".date("Y-m");
				$info_mail["from"] = "Optima no-reply <no-reply@absystech.net>";
				$info_mail["html"] = true;
				$info_mail["template"] = 'note_de_frais';
				$info_mail["data"] = $data;
				$info_mail["recipient"] = $this->mailFrais;


				$mail = new mail($info_mail);
				$mail->send();
			}
		}

	}

};
?>

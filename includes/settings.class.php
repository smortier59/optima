<?
/**
* Classe de configuration
* @package ATF
*/
class settings extends classes_optima {
	public function __construct() {
		parent::__construct();

	}

	public function getSettings($module, $id, $id_societe, $fields = null) {
		$this->q->reset();

		if (!$module) throw new errorATF("Il faut au moins un nom de module pour récupérer la configuration");
		if (!$id) throw new errorATF("Il faut au moins un id d'élément pour récupérer la configuration");

		$this->q->where('module', $module);
		$this->q->where('id', $id);

		if ($id_societe) $this->q->where('id_societe', $id_societe);

		if ($fields) $this->q->addFields($fields);

		return $this->sa();

	}

	public function _setSettings($get, $post) {
		if (!$post) throw new errorATF("POST MISSING",1002);
		if (!$post['module']) throw new errorATF("MODULE_MISSING",1003);
		if (!$post['id_societe']) throw new errorATF("SOCIETE_MISSING",1003);

		$libId = "id_".$post['module'];

		ATF::db($this->db)->begin_transaction();
		try {
			foreach ($post[$libId] as $k=>$v) {
				//On check les data.
				$data = array(
					"id" => $v,
					"module" => $post['module'],
					"id_societe" => $post['id_societe'],
					"mail_to" => $post['mail_to'][$k],
					"mail_content" => $post['mail_content'][$k]
				);

				if ($post['id_settings'][$k]) {
					$data['id_settings'] = $post['id_settings'][$k];
					$this->u($data);
				} else {
					if ($data['mail_to'] && $data['mail_content']) {
						$this->i($data);
					}
				}

			}
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}

		ATF::db($this->db)->commit_transaction();
		return true;

	}

};

?>

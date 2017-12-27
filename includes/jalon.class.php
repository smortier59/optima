<?
/**
* Classe de configuration
* @package ATF
*/
class jalon extends classes_optima {
	public function __construct() {
		parent::__construct();

	}

	public function _GET($get, $post) {
		$this->q->reset();

		$fields = array(
			'jalon.id_jalon'=>array("alias"=>"id_jalon"),
			'jalon.jalon'=>array("alias"=>"jalon"),
			'jalon.category'=>array("alias"=>"category"),
			'jalon.module'=>array("alias"=>"module"),
			'settings.mail_to'=>array("alias"=>"mail_to"),
			'settings.mail_content'=>array("alias"=>"mail_content")
		);

    $this->q->addField($fields);

		if ($get['module']) {
			$this->q->where("module", $get['module']);
		}
		if ($get['category']) {
			$this->q->where("category", $get['category']);
		}

		if ($get['withSettings']) {
    	$this->q->addField("settings.id_settings", "id_settings");
			$this->q->from('jalon','id_jalon','settings','id', NULL, NULL, NULL, 'jsj1');
			$this->q->where('settings.module', 'jalon', 'OR', 'jsj1');

			if ($get['id_societe']) $this->q->where('settings.id_societe', $get['id_societe']);
		}

		return $this->sa();

	}

};

?>

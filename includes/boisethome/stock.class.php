<?
class stock extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->colonnes['fields_column'] = array(
			'stock.date'
			,'devis.id_societe'=>array("custom"=>true)
			,'devis.id_devis'=>array("custom"=>true)
			,'article.nature'=>array("custom"=>true)
			,'stock.id_devis_lot'
			,'devis_lot_produit.id_produit'=>array("custom"=>true)
			,'stock.id_article'			
			,'stock.id_user'
			,'stock.id_fournisseur'
			,'stock.mouvement'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right")
			,'stock.prix_achat'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
			,'total'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
		);
		$this->table = __CLASS__;
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->fieldstructure();
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$this->q
			->addField("devis.id_societe")
			->addField("devis.id_devis")
			->addField("stock.prix_achat * stock.mouvement","total")
			->from("stock","id_article","article","id_article")
			->from("stock","id_devis_lot_produit","devis_lot_produit","id_devis_lot_produit")
			->from("devis_lot_produit","id_devis_lot","devis_lot","id_devis_lot")
			->from("devis_lot","id_devis","devis","id_devis")
		;
		return parent::select_all($order_by,$asc,$page,$count);
	}

}
<?php
/**
* Le requêteur permet d'externaliser la couche de filtrage conditionnel (WHERE),
* le tri (ORDER BY), la pagination (LIMIT), et toutes les autres possibilités offertes
* par une requête à une base de données, sans écrire de SQL. Si les noms des variables
* reprennent des termes spécifiques à MySQL, le requêteur n'est pas conceptuellement
* borné à ce SGBD. Le SQL étant purement écrit dans la classe ATF mysql et classes.
*
* Un requêteur peut alors être appliqué à n'importe quel appel à par exemple classes::select_all
* pour en retourner la page correspondate ou le tri demandé.
*
* @date 2008-10-30
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
* @error FIELD_REPLACE_DENIED Remplacement d'un champ n'est pas possible, car il a déjà été défini.
*/
class querier {
	/**
	* Liste des champs
	* @var mixte
	*/
	public $field;

	/**
	* Clause where
	* @var mixte
	*/
	private $where;

	/**
	* Clause Group by
	* @var mixte
	*/
	public $group;

	/**
	* Clause having
	* @var mixte
	*/
	public $having;

	/**
	* Définition de la clause Order
	* @var array
	*/
	public $order;

	/**
	* Clause limit
	* @var mixte
	*/
	public $limit;

	/**
	* @var mixte
	*/
	public $page;

	/**
	* @var mixte
	*/
	public $count;

	/**
	* @var mixte
	*/
	public $distinct;

	/**
	* @var mixte
	*/
	public $jointure;

	/**
	* @var mixte
	*/
	public $search;

	/**
	* @var mixte
	*/
	public $strict;

	/**
	* Valeurs à insérer ou à mettre à jour
	* @var mixte
	*/
	public $set;

	/**
	* L'indice de l'élément
	* @var mixte
	*/
	private $arrayKeyIndex;

	/**
	* Clé étrangère, servant à filtrer la requête avec la méthode classes::genericSelectAll()
	* @var mixte
	*/
	private $fk;

	/**
	* Nombre de lignes renvoyées, attribut calculé
	* @var mixte
	*/
	public $nb_rows;

	/**
	* Nombre de pages renvoyées, attribut calculé
	* @var mixte
	*/
	public $nb_page;

	/**
	* @todo commentaire
	* @var mixte
	*/
	public $name;

	/**
	* @todo commentaire
	* @var mixte
	*/
	public $filter_key;

	/**
	* @todo commentaire
	* @var mixte
	*/
	public $view;

	/**
	* Dimension souslaquelle sera retourné le tableau (sql2Array par défaut)
	* valeurs : NULL|row|cell|row_arro
	* @var mixte
	*/
	public $dimension;

	/**
	* Permet de mettre un alias
	* @var string
	*/
	public $alias;

	/**
	* Le selectAll renverra une string
	* @var string
	*/
	public $toString;

	/**
	* Insertion, Mise à jour
	* @var mixte
	*/
	public $values;

	/**
	* last SQL retourné par le SGBD à chaque query
	* @var string
	*/
	public $lastSQL;

	/**
	* last SQL pour utilisation postérieure de la même requête mais avec aggrégattions
	* @var string
	*/
	public $lastSQLForAggregate;

	/**
	* Attributs divers
	* @var mixte
	*/
	public static $operateur = array(
		'LIKE'=>array("not_this_type"=>array("list","enum","set","date","datetime")),
		'NOT LIKE'=>array("not_this_type"=>array("list","enum","set","date","datetime")),
		'=',
		'>'=>array("not_this_type"=>array("list","enum","set","date","datetime")),
		'<'=>array("not_this_type"=>array("list","enum","set","date","datetime")),
		'>='=>array("not_this_type"=>array("list","enum","set")),
		'<='=>array("not_this_type"=>array("list","enum","set")),
		'!=',
		'LIKE%'=>array("not_this_type"=>array("list","enum","set","date","datetime")),
		'%LIKE'=>array("not_this_type"=>array("list","enum","set","date","datetime")),
		'IS NULL',
		'IS NOT NULL'/*,
		'BETWEEN'=>array("type"=>array("date","datetime"))*/
	);

	/**
	* Table de référence lorsqu'on part d'une table spécifique ou subQuery
	* @var string
	*/
	public $table;

	/**
	* Numéro de séquence unique pour la création d'alias unique
	* @var string
	*/
	private $sequence=0;

	/**
	* Permet d'utiliser les unions
	*/
	public $unions;

	/**
	* Permet que la vue actuelle ne soit pas écrasée
	*/
	public $protectedView;

	/**
	* intervalle de date entre lesquelles on veut afficher les données
	* @var array
	*/
	public $between_date;

	/**
    * Ajoute tous les champs de la table
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $table : nom de la table courante
	* @return querier
    */
	public function addAllFields($table) {
		if ($rows = ATF::db()->fields($table)) {
			foreach($rows as $field){
				$this->addField($table.".".$field);
			}
		}
		return $this;
	}

	/**
    * Ajoute une condition spéciale pour le mot clé between
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $field : champ
	* @param string $value : valeur
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $operand : =, !=, LIKE, NOT LIKE etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @return querier
    */
	public function addConditionBetween($field,$value,$condition="OR",$cle=false,$operand="=",$overwrite=false) {
		if ($field) {
			if ($cle===false) {
				$cle = $field;
			}
			if ($this->where[$cle]) {
				if ($overwrite) {
					$this->where[$cle] = NULL;
				} else {
					$this->where[$cle] .= " ".$condition." ";
				}
			}

			preg_match('`(.*)AND(.*)`',$value,$values);
			$this->where[$cle] .= $field." ".$operand." '".trim($values[1])."' AND '".trim($values[2])."'";
		}
		return $this;
	}

	/**
    * Ajoute une condition de regroupement GROUP BY
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @return querier
    */
	public function addGroup($field) {
		$this->group[$field] = $field;
		return $this;
	}

	/**
    * Ajoute une condition entre d'autres conditions applicables sur des champs calculés (Having) (x OR y) AND (z OR w)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $keys : Clés de conditions séparés par virgules
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @return querier
    */
	public function addHavingSuperCondition($keys,$condition="OR",$cle=false,$strict=true) {
		$keys = explode(",",$keys);
		if ($cle===false) {
			$cle = implode("-",$keys);
		}
		foreach ($keys as $key) {
			if (isset($this->having[$key])) {
				$cond[] = $this->having[$key];
				unset($this->having[$key]);
			}
		}
		if ($strict) {
			$this->having[$cle] = " (".implode(") ".$condition." (",$cond).") ";
		} else {
			$this->having[$cle] = " ".implode(" ".$condition." ",$cond)." ";
		}
		return $this;
	}

	/**
    * Ajoute une condition de tri
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $fields : ordonner par ce champ, possibilité d'en mettre plusieurs séparés par virgule
	* @param string $sens : ordonner dans ce sens (ASC,DESC), possibilité d'en mettre plusieurs séparés par virgule
    * @return querier
    */
	public function addOrder($fields,$sens='asc'){
		if ($this->order===NULL) {
			$this->order = array(
				"field"=>array()
				,"sens"=>array()
				,"sensinv"=>array()
			);
		}

		$fields = explode(",",$fields);
		$this->order["field"] = array_merge($this->order["field"],$fields);

		$sens = explode(",",$sens);
		$this->order["sens"] = array_merge($this->order["sens"],$sens);

		$this->order["sensinv"] = array();
		foreach($this->order["field"] as $k => $i) {
			if (!$this->order["sens"][$k]) {
				$this->order["sens"][$k]="asc";
			}
			$this->order["sensinv"][$k] = $this->order["sens"][$k]=="asc"?"desc":"asc";
		}
		return $this;
	}

	/**
	* Permet de réunir les résultats de deux requêtes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param $sql
	*/
	public function addUnion($sql){
		$this->unions[]=$sql;
		return $this;
	}

	/**
	* Ajoute plusieurs valeurs à une requête d'insertion multiple via un paramètre de tableau associatif
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return querier
	*/
	public function addMultiValues($infos){
		//initialisation éventuelle des valeurs
		if(!is_array($this->values)){
			$this->values=array();
		}

		$this->values = array_merge($this->values,$infos);

		return $this;
	}

	/**
    * On rajoute des éléments à la recherche
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $champs
	* @param string $search
	* @return querier
    */
	public function addSearch($champs,$search) {
		$this->search[$champs] = $search;
		return $this;
	}

	/**
    * Ajoute une condition entre d'autres conditions   (x OR y) AND (z OR w)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $keys : Clés de conditions séparés par virgules
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param boolean $strict : le mode strict permet de ne pas encapsuler les parensthèse avec FALSE, TRUE pour encapsuler les conditions avec des parenthèses
	* @return querier
    */
	public function addSuperCondition($keys,$condition="OR",$cle=false,$strict=true) {
		$keys = explode(",",$keys);
		if ($cle===false) {
			$cle = implode("-",$keys);
		}
		foreach ($keys as $key) {
			if (isset($this->where[$key])) {
				$cond[] = $this->where[$key];
				unset($this->where[$key]);
			}
		}
		if ($strict) {
			$this->where[$cle] = " (".implode(") ".$condition." (",$cond).") ";
		} else {
			$this->where[$cle] = " ".implode(" ".$condition." ",$cond)." ";
		}
		return $this;
	}

	/**
	* Ajout un champ et sa valeur à insérer
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $field le champ de la table concernée
	* @param string $value la valeur que l'on désire insérée
	* @param boolean $replace true si on désire remplacer la valeur actuelle
	* @return querier
	*/
	public function addValue($field,$value,$replace=false){
		if(isset($this->values[$field]) && !$replace) {
			throw new errorATF(__CLASS__."__FIELD_REPLACE_DENIED");
		}
		$this->values[$field] = $value;
		return $this;
	}

	/**
	* Ajoute plusieurs valeurs à une requête d'insertion via un paramètre de tableau associatif
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @param array $infos le tableau associatif, champ, valeur
	* @return querier
	*/
	public function addValues($infos,$replace=false){
		foreach($infos as $index=>$valeur){
			$this->addValue($index,$valeur,$replace);
		}
		return $this;
	}

	/**
    * Retire la condition
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $cle
	* @return querier
    */
	public function delCondition($cle) {
		if ($this->where[$cle]) {
			unset($this->where[$cle]);
		}

		//on supprime également le having éventuellement raccordé
		$this->delHavingCondition($cle);

		return $this;
	}

	/**
    * Retire la condition sur champs calculés (Having)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $cle
	* @return querier
    */
	public function delHavingCondition($cle) {
		if ($this->having[$cle]) {
			unset($this->having[$cle]);
		}
		return $this;
	}

	/**
    * On supprime des éléments à la recherche
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $champs
	* @return querier
    */
	public function delSearch($champs) {
		unset($this->search[$champs]);
		return $this;
	}

	/**
    * Ne retourne pas $this
    */
	public function end() {
		return;
	}

	/**
    * Ajoute une jointure avec une autre table
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $table_left : Table de gauche, est sensée déjà exister dans l'environnement de jointures
	* @param string $field_left : Champ de jointure sur table de gauche
	* @param string $table_right : Table de droite
	* @param string $field_right : Champ de jointure sur table de droite
	* @param string $alias : Alias de la table de droite
	* @param string $fields_only : On ne met dans fields que ces champs : séparés par virgule
	* @param string $fields_alias : Ces champ seront visibles sous ces alias là : séparés par virgule
	* @param string $cle_condition : Clé de la condition supplémentaire de jointure définie dans les conditions, permet de faire une condition de jointure de manière externe et donc non sine qua non
	* @param string $externe : "left"|"right"|"inner" La jointure peut être définie NATIVE ou EXTERNE (left par défaut)
	* @param string $change_base :Dans le cas où la jointure porte sur une table d'une autre base
	* @param string $subquery : dans le cas où on applique une sous requête pour récupérer les enregistrements adéquats
	* @return querier
    */
	public function fromInner($table_left,$field_left,$table_right,$field_right,$alias=NULL,$fields_only=NULL,$fields_alias=NULL,$cle_condition=NULL,$externe="inner",$change_base=false) { return $this->from($table_left,$field_left,$table_right,$field_right,$alias,$fields_only,$fields_alias,$cle_condition,$externe,$change_base); }
	public function from($table_left,$field_left,$table_right,$field_right,$alias=NULL,$fields_only=NULL,$fields_alias=NULL,$cle_condition=NULL,$externe="left",$change_base=false,$subquery=NULL) { return $this->addJointure($table_left,$field_left,$table_right,$field_right,$alias,$fields_only,$fields_alias,$cle_condition,$externe,$change_base,$subquery); }
	public function addJointure($table_left,$field_left,$table_right,$field_right,$alias=NULL,$fields_only=NULL,$fields_alias=NULL,$cle_condition=NULL,$externe="left",$change_base=false,$subquery=NULL) {
		$this->jointure[($alias?$alias:$table_right)] = array(
			"table_left" => $table_left
			,"field_left" => $field_left
			,"table_right" => $table_right
			,"field_right" => $field_right
			,"alias" => $alias
			,"fields_only" => $fields_only
			,"fields_alias" => $fields_alias
			,"cle_condition" => $cle_condition
			,"externe" => $externe
			,"change_base" => $change_base
			,"subquery" => $subquery
		);
		return $this;
	}

	/**
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* Retourne l'alias, ou la table si pas d'alias
	* @return string
	*/
	public function getAlias() {
		if ($this->alias) {
			return $this->alias;
		}
		return $this->table;
	}

	/**
	* Permet récupérer la clé d'index définie
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string
	*/
	public function getArrayKeyIndex(){
		return $this->arrayKeyIndex;
	}

	/** Récupère la fourchette de date
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function getBetweenDate(){
		return $this->between_date;
	}

	/**
	* Retourne le flag dimension
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string
	*/
	public function getDimension(){
		return $this->dimension;
	}

	/**
    * Retourne la clé de filtre utilisée
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string $filter_key
    */
	public function getFilterKey() {
		return $this->filter_key;
	}

	/**
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* Retourne la clé de filtrage immuable de ce querier
	* @return array
	*/
	public function getFk(){
		return $this->fk;
	}

	/**
	* Permet récupérer le contenu du having de la classe courante
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $key Clé si on ne veut qu'un seul élément
	* @return array having
	*/
	public function getHaving($key=NULL){
		if ($key) {
			return $this->having[$key];
		} else {
			return $this->having;
		}
	}

	/**
    * Retourne la limite par page courante
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int $limit
    */
	public function getLimit() {
		return $this->limit["limit"];
	}

	/**
    * Retourne les conditions de tri
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @return array
    */
	public function getOrder() {
		if(is_array($this->order["field"])){
			foreach ($this->order["field"] as $k => $i) {
				$s[$i] = $this->order["sens"][$k];
			}
		}
		return $s;
	}

	/**
    * Retourne les conditions de tri brutes
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param $f field ou sens ?
    * @return string
    */
	public function getOrderBrut($f="field") {
		if (isset($this->order[$f])) {
			return implode(",",$this->order[$f]);
		}
	}

	/**
    * Retourne la page courante
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int $page
    */
	public function getPage() {
		return $this->page;
	}

	/**
    * Retourne les mots clés recherchés
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return int $page
    */
	public function getSearch(){
		return $this->search;
	}

	/**
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* Incrémente et retourne le numéro de séquence : numéro unique de la query crée avec ce querier.
	* @return int
	*/
	public function getSequence(){
		return ++$this->sequence;
	}

	/**
	* Retourne le flag toString
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	public function getToString(){
		return $this->toString;
	}

	/**
	* Permet de contruire la requête permettant d'unir les résultats de deux requêtes
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return String
	*/
	public function getUnion(){
		return  "(".implode(   ") UNION ALL (",$this->unions).")";
	}

	/**
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* Donne les valeurs de l'élément que l'on désire insérer
	* @return array
	*/
	public function getValues(){
		return $this->values;
	}

	/**
    * Retourne la vue de ce requêteur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
    */
	public function getView() {
		return $this->view;
	}

	/**
	* Permet récupérer le contenu du where de la classe courante
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $key Clé si on ne veut qu'un seul élément
	* @return array where
	*/
	public function getWhere($key=NULL){
		if ($key) {
			return $this->where[$key];
		} else {
			return $this->where;
		}
	}

	/**
    * Retourne un boolean, VRAI si une vue est définie dans ce requêteur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
    */
	public function hasView() {
		return $this->view!==NULL;
	}

	/**
    * Ajoute une condition perso alternative sur les champs calculés (clause HAVING)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $value : valeur
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $operand : =, !=, LIKE, NOT LIKE etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @return querier
    */
	public function having($field,$value,$condition="OR",$cle=false,$operand="=",$overwrite=false,$subquery=false) { return $this->addHavingCondition($field,$value,$condition,$cle,$operand,$overwrite,$subquery); }
	public function addHavingCondition($field,$value,$condition="OR",$cle=false,$operand="=",$overwrite=false,$subquery=false) {
		if ($cle===false) {
			$cle = $field;
		}
		if ($this->having[$cle]) {
			if ($overwrite) {
				$this->having[$cle] = NULL;
			} else {
				$this->having[$cle] .= " ".$condition." ";
			}
		}
		if ($value===NULL) {
			$this->having[$cle] .= $field." ".$operand;
		} elseif($subquery) {
			$this->having[$cle] .= $field." ".$operand." (".$value.")";
		} else {
			$this->having[$cle] .= $field." ".$operand." '".$value."'";
		}
		return $this;
	}

	/**
    * Ajoute une condition perso alternative HAVING avec pour valeur NOT NULL
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function havingNotNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) { return $this->addHavingConditionNotNull($field,$condition,$cle,$overwrite,$subquery,$join_subcondition); }
	public function addHavingConditionNotNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addHavingCondition($field,NULL,$condition,$cle,"IS NOT NULL",$overwrite,$subquery,$join_subcondition);
	}

	/**
    * Ajoute une condition perso alternative HAVING avec pour valeur NULL
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function havingNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) { return $this->addHavingConditionNull($field,$condition,$cle,$overwrite,$subquery,$join_subcondition); }
	public function addHavingConditionNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addHavingCondition($field,NULL,$condition,$cle,"IS NULL",$overwrite,$subquery,$join_subcondition);
	}

	/**
    * Ajoute une condition perso Having avec pour condition AND, la clé est impérative
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $value : valeur
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $operand : =, !=, LIKE, NOT LIKE etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function andHaving($field,$value,$cle=false,$operand="=",$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addHavingCondition($field,$value,"AND",$cle,$operand,$overwrite,$subquery,$join_subcondition);
	}

	/**
    * Retourne un table de jointure formaté correctement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $table_left : Table de gauche, est sensée déjà exister dans l'environnement de jointures
	* @param string $field_left : Champ de jointure sur table de gauche
	* @param string $table_right : Table de droite
	* @param string $field_right : Champ de jointure sur table de droite
	* @param string $alias : Alias de la table de droite
	* @param string $fields_only : On ne met dans fields que ces champs : séparés par virgule
	* @param string $fields_alias : Ces champ seront visibles sous ces alias là : séparés par virgule
	* @param string $cle_condition : Clé de la condition supplémentaire de jointure définie dans les conditions, permet de faire une condition de jointure de manière externe et donc non sine qua non
	* @param string $externe : "left"|"right"|"inner" La jointure peut être définie NATIVE ou EXTERNE (left par défaut)
	* @param string $change_base :Dans le cas où la jointure porte sur une table d'une autre base
    */
	public static function jointure($table_left,$field_left,$table_right,$field_right,$alias=NULL,$fields_only=NULL,$fields_alias=NULL,$cle_condition=NULL,$externe="left",$change_base=false) {
		return array(
			"table_left" => $table_left
			,"field_left" => $field_left
			,"table_right" => $table_right
			,"field_right" => $field_right
			,"alias" => $alias
			,"fields_only" => $fields_only
			,"fields_alias" => $fields_alias
			,"cle_condition" => $cle_condition
			,"externe" => $externe
			,"change_base" => $change_base
		);
	}

	/**
    * Sépare les DATA du COUNT et retourne les données résultat
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
    */
	public function query(&$result) {
		if (is_array($result) && isset($result["count"])) {
			$this->nb_rows = $result["count"];
			$this->nb_page = ceil($this->nb_rows / $this->limit["limit"]);
			return $result["data"];
		} elseif(is_array($result)) {
			$this->nb_rows = count($result);
		}
		return $result;
	}

	/**
    * Alias de query
    */
	public function q(&$result) {
		return $this->query($result);
	}

	/**
    * Remet à 0 les conditions perso
	* @param string $specific : seulement 1 ou plusieurs attributs séparés par virgule
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public function reset($specific=NULL) {
		if ($specific) {
			$specific = explode(",",$specific);
			foreach ($specific as $one) {
				$this->$one = NULL;
			}
		} else {
			$this->field 	=
			$this->where 	=
			$this->group 	=
			$this->having 	=
			$this->order 	=
			$this->limit 	=
			$this->page 	=
			$this->count 	=
			$this->distinct =
			$this->search 	=
			$this->jointure	=
			$this->nb_page	=
			$this->nb_rows 	=
			$this->name 	=
			$this->filter_key 	=
			$this->view 	=
			$this->values 	=
			$this->strict 	=
			$this->alias 	=
			$this->toString	=
			$this->dimension	=
			$this->arrayKeyIndex	=
			$this->fk	=
			$this->unions	=
			$this->lastSQL 	=
			$this->protectedView 	=
			$this->between_date =
			$this->forUpdate =
			$this->table 	=  NULL;
		}
		return $this;
	}

	/**
	* Sauvegarde le SQL en vue de l'utiliser éventuellement pour l'aggregate
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return querier
	*/
	public function saveSQLForAggregate(){
		$this->lastSQLForAggregate = $this->lastSQL;
		return $this;
	}

	/**
    * Ajoute un champ particulier à retourner en SELECT
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @author Quentin	JANON <qjanon@absystech.fr>
	* @param string|array $field :
	*			soit plusieurs champs en tableau ou sous forme de chaîne séparés par virgules
	*			soit un seul champ si $opt existe ; on n'attend qu'une string qui défini un seul champ qui peut être du SQL complexe
	* @param string|array $opt contient les options pour le champs :
	*			soit un tableau d'option
	*			soit une string (par défaut correspond a l'option "alias")
	* @param boolean $no_replace_field : si on veut que le field soit une valeur numérique (ex: user.class, inventaire)
	* @return querier
    */
	public function select($field,$opt) { return $this->addField($field,$opt); }
	public function addField($field,$opt=NULL,$no_replace_field=false) {
		if ($opt) {
			//Si $field est une string elle est soumise a un traitement particulier
			if (!is_array($field)) {
				//Si $opt est une string elle est soumise a un traitement particulier qui la transforme en tableau et qui la considère par défaut comme un ALIAS
				if (!is_array($opt)) {
					$opt = array("alias"=>$opt);
				}
				//On transforme le  champs passé sous forme de string en array pour pouvoir le passer au querier
				$field = array(	$field => $opt	);
			}
		} else {
			if (is_string($field)) {
				$field = explode(",",$field);
			}
		}
		foreach ($field as $k=>$i) {
			// Si on a pas passé d'option ou si l'on est dans une vue, la key sera un numeric,
			//afin de lui assurer un traitement cohérent on recopie l'item (qui est forcément une string) dans la clé.
			if (is_int($k) && !$no_replace_field) {
				$k = $i;
			}

			// Si l'alias existe déjà dans la déclaration constructeur des colonne.fields_column

			//S'il n'y a pas d'alias de defini dans les colonnes
			if (!is_array($i) || !isset($i['alias'])) {
				if (is_array($i)) {
					$this->field[$k] = array_merge($i,array("alias"=>$k));
				} else {
					$this->field[$k] = array("alias"=>$k);
				}
//			} elseif (isset($i['alias']) && $i['alias'] && in_array($i['alias'],$this->field)) {
//				$temp = array_combine(array_keys($this->field),array_keys($this->field));
//				$temp[$i['alias']] = $k;
//				$this->field = util::renameKeys($this->field,array_values($temp));
			} else {
				$this->field[$k] = $i;
			}
		}
		return $this;
	}

	/**
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* Permet de mettre un alias sur une table
	* @param string $alias Alias pour la table principale
	* @return querier
	*/
	public function setAlias($alias){
		$this->alias = $alias;
		return $this;
	}

	/**
	* Permet de renvoyer le tableau avec une clé d'index personalisée (exemple la clé d'un)
	* ATTENTION, un addField($key) sera aussi effectué pour éviter les surprises
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $key
	* @param boolean $noAddField Si FALSE alors on ne fait pas le addField()
	* @return querier
	*/
	public function setArrayKeyIndex($key,$addField=true){
		$this->arrayKeyIndex = $key;
		if ($addField) {
			$this->addField($key);
		}
		return $this;
	}

	/** Applique au querier la fourchette de date
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function setBetweenDate($args){
		$this->between_date=$args;
		return $this;
	}

	/**
    * Active la prévision de comptage
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $yes true pour activation, faux sinon
	* @return querier
    */
	public function setCount($yes=true) {
		$this->count = $yes;
		return $this;
	}

	/**
    * Active la prévision de comptage, et prévoit de ne retourner que ce comptage;
	* @param boolean $yes true pour activation, faux sinon
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return querier
    */
	public function setCountOnly() {
		$this->count = 1;
		return $this;
	}

	/**
	* Permet de renvoyer le tableau sous une dimension sélectionné
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $dimension = array|row|cell|row_arro|arro
	* @return querier
	*/
	public function setDimension($dimension){
		$this->dimension = $dimension;
		if ($this->dimension=="cell" || $this->dimension=="row") {
			$this->setLimit(1);
		}
		return $this;
	}

	/**
    * Active le dédoublonnement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $yes true pour activation, faux sinon
	* @return querier
    */
	public function setDistinct($yes=true) {
		$this->distinct = $yes;
		return $this;
	}

	/**
    * On défini plusieurs conditions d'un seul coup à l'aide d'une liste de type FILTRE
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @author Nicolas BERTEMONT <ygautheron@absystech.fr>
	* @param array filter
	* 		string name
	* 		string mode OR|AND
	* 		array conditions
	*			int => array (
	*				field => string
	*				operand => string (depuis la liste querier::$operateur)
	*				value => string
	*			)
	* @param int filter_key
	* @param classes $class Classe associée, permettant de vérifier quels champs sont des custom et les placer leurs conditions en HAVING plutot qu'en WHERE
	* @return querier
    */
	public function setFilter($filter,$filter_key,&$class) {

		//puisque l'on appelle cette méthode, qu'il y ait du contenu ou non, on réinitialise les conditions
		$this->delCondition("ATF_FILTER");
		// Reset le where, sinon lorsque l'on bascule du listing sur un onglet au module en select_all cela conserver l'id et flinguer le listing.
		$this->delCondition("where");
//log::logger("q->setFilter","ygautheron",true);
		// Au cas ou le filtre est passé sous forme serialisé.
		if (is_string($filter)) {
			$filter = unserialize($filter);
		}

		//ajout des jointures
		if (is_array($filter["jointures"])) {
			foreach ($filter["jointures"] as $jointure) {
				$module=explode(".",$jointure["module"]);
				$liste_champs=explode(".",$jointure["liste_champs"]);
				/*$this->addJointure($liste_champs[0],$liste_champs[1],$module[0],$module[1],($jointure["alias"]!="Alias"?$jointure["alias"]:NULL),NULL,NULL,NULL,$filter["choix_join"]) ;*/
				$this->addJointure($liste_champs[0],$liste_champs[1],$module[0],$module[1],NULL,NULL,NULL,NULL,$filter["choix_join"]) ;
			}
		}else{
			//lorsque l'on choisit un filtre, et qu'ensuite on remet sur la position 'creer un noveau filtre'
			$this->reset('jointure');
		}

		if (is_array($filter["conditions"])) {
			$overwrite = true;
			foreach ($filter["conditions"] as $condition) {
				switch ($condition["operand"]) {
					case 'NOT LIKE':
						$condition["value"] = "%".$condition["value"]."%";
						break;

					case 'LIKE':
						$condition["value"] = "%".$condition["value"]."%";
						break;

					case 'LIKE%':
						$condition["operand"] = "LIKE";
						$condition["value"] = $condition["value"]."%";
						break;

					case '%LIKE':
						$condition["operand"] = "LIKE";
						$condition["value"] = "%".$condition["value"];
						break;

					case 'IS NULL':
					case 'IS NOT NULL':
						$condition["value"] = NULL;
						break;
					case 'DATE_JOUR':
						$condition["operand"] = "LIKE";
						$condition["value"] = date("Y-m-d")."%";
						break;
					case '=':
					case '>':
					case '<':
					case '>=':
					case '<=':
					case '!=':
					case 'BETWEEN':
				}

				$table = strstr($condition["field"],".",true);
				if ($table && ($transform = ATF::getClass($table)->foreign_key_translator($condition["field"],$this->jointure))) {
//log::logger("q->setFilter ".$condition["field"]." = ".$transform["field"]."","ygautheron");
					$condition["field"] = $transform["field"];
//
//					if (isset($transform["jointure"]) && is_array($transform["jointure"])) {
//						$this->jointure[$transform["jointure"]["alias"]] = $transform["jointure"];
//					}
				}

				if (is_array($class->colonnes["fields_column"][$condition["field"]]) && $class->colonnes["fields_column"][$condition["field"]]["custom"]) {
					if($condition["operand"]=='BETWEEN'){
						$this->addConditionBetween($condition["field"],$condition["value"]." AND ".$condition["value_sup"],$filter["mode"],"ATF_FILTER",$condition["operand"],$overwrite);
					}else{
						$this->addHavingCondition($condition["field"],$condition["value"],$filter["mode"],"ATF_FILTER",$condition["operand"],$overwrite);
					}
				} else {
					if($condition["operand"]=='BETWEEN'){
						$this->addConditionBetween($condition["field"],$condition["value"]." AND ".$condition["value_sup"],$filter["mode"],"ATF_FILTER",$condition["operand"],$overwrite);
					}else{
						$this->addCondition($condition["field"],$condition["value"],$filter["mode"],"ATF_FILTER",$condition["operand"],$overwrite,$condition["subquery"]);
					}
				}
				$overwrite = false;
			}
		}

		/* Si la vue est définie pour ce filtre, on l'applique */
		if ($filter["view"]) {
			$this->setView($filter["view"]);
		//si il s'agit du select_all extjs, il faut que la vue soit conservé, car il s'agit d'une vue directement appliqué au tabpanel du filtre
		} elseif($class->selectAllExtjs===false) {
			$this->reset('view');
		}

		return $this->setFilterKey($filter_key);
	}

	/**
    * On défini une clé de filtre utilisée
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $filter_key
    */
	public function setFilterKey($filter_key) {
		$this->filter_key = $filter_key;
		return $this;
	}

	/**
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* Applique un clé de filtrage immuable pour ce querier
	* @param array $fk Clé de filtrage sous la forme d'un array
	* @return querier
	*/
	public function setFk($fk){
		$this->fk = $fk;
		return $this;
	}

	/**
	* Ajoute plusieurs valeurs à une requête d'insertion via un paramètre de tableau associatif
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $sql
	* @return querier
	*/
	public function setLastSQL($sql){
		$this->lastSQL = $sql;
		return $this;
	}

	/**
    * Défini les limites en terme de nombre d'enregistrement et l'offset de base
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $limit : Limiter en nombre de record : si -1, illimité
	* @param int $offset : Commencer le listing à partir de quel offset
	* @return querier
    */
	public function setLimit($limit,$offset=0) {
		if ($limit && $limit!==$this->limit["limit"]) {
//log::logger("querier::setLimit = ".$limit."!==".$this->limit["limit"],ygautheron,true);
			$this->limit["limit"] = $limit;
			$this->limit["offset"] = $offset;
			$this->page=0;
		}
		return $this;
	}

	/**
    * Défini un nom personnalisé pour ce requêteur
	* @param string $name
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
    * Applique les conditions de tri au format retourné par getOrder
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param bool $default true si on désire avoir l'ordre par défaut
    * @return querier
    */
	public function setOrder($s) {
		foreach ($s as $field => $sens) {
			$this->addOrder($field,$sens);
		}
		return $this;
	}

	/**
    * Défini la page à retourner
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $page : Demander cette page
	* @param int $increase : +1 ou -1 en général...
	* @return querier
    */
	public function setPage($page,$increase=0) {
		$this->page = $page+$increase;
		return $this;
	}

	/**
    * Définir la table de référence
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $table Définir cette table en référence
	* @param string $autoAlias Défini a FALSE si on est sûr que la table n'est pas une subQuery
    * @return querier
    */
	public function setRefTable($table,$autoAlias=true) {
		$this->table = $table;
		if ($autoAlias && !$this->alias) {
			$this->setAlias("refTable".$this->getSequence());
		}
		return $this;
	}

	/**
    * Définir une requête SQL en subQuery pour la table de référence, avec pour alias le second paramètre
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $sql
	* @param string $alias
    * @return querier
    */
	public function setSubQuery($sql,$alias) {
		return $this->setRefTable("(".$sql.")")->setAlias($alias)->setStrict();
	}

	/**
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* Permet de renvoyer la query sur un select_all
	* @return querier
	*/
	public function setToString(){
		$this->toString = true;
		return $this;
	}

	/**
    * On défini la recherche souhaitée
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $search
	* @return querier
    */
	public function setSearch($search) {
		$this->search = $search;
		return $this;
	}

	/**
    * Active le mode strict, évite les transformations de champs et jointures automatiques, donc par défaut on a tous les champs de la table sollicitée (societe.* par exemple)
	* En réduit (1) cela permet de garder les jointures automatiques, mais de ne pas ajouter de field (les clé étrangères comme id_contact_fk par exemple)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $yes true pour activation absolue, 1 pour activation seulement sur les ajouts de colonnes, faux sinon
	* @return querier
    */
	public function setStrict($yes=true) {
		$this->strict = $yes;
		return $this;
	}

	/**
    * Défini la vue d'affichage (colonnes à afficher, alignement, suffixe, préfixe)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $view
	*			array order
	*			array align
	*			array prefix
	*			array suffix
	* @param boolean $protectedView permet que cette vue ne soit jamais écrasée
	* @return querier
    */
	public function setView($view,$protectedView=false) {
		if(!$this->protectedView || $protectedView){
			$this->view = $view;
			$this->protectedView = $protectedView;
		}
		return $this;
	}

	/**
    * Active le verrou de lecture sur la transaction
    * @author Quentin JANON <qjanon@absystech.fr>
	* @return querier
    */
	public function setForUpdate() {
		if (!$this->forUpdate) {
			$this->forUpdate = true;
		}
		return $this;
	}

	/**
    * Inverse les sens de tri
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @return string
	* @return querier
    */
	public function switchSens() {
		foreach($this->order["sens"] as $k => $i) {
			$this->order["sens"][$k] = $this->order["sensinv"][$k];
			$this->order["sensinv"][$k] = $this->order["sens"][$k]=="asc"?"desc":"asc";
		}
		return $this;
	}

	/**
	* Annule la clé du tableau à retourner, en conséquence un select_all() retournera un tableau avec clé numérique
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return querier
	*/
	public function unsetArrayKeyIndex(){
		$this->arrayKeyIndex = NULL;
		return $this;
	}

	/**
    * Désactive la prévision de comptage
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return querier
    */
	public function unsetCount() {
		$this->count = false;
		return $this;
	}

	/**
    * Désactive le dédoublonnement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return querier
    */
	public function unsetDistinct() {
		$this->distinct = false;
		return $this;
	}

	/**
    * Désactive le mode strict, évite les transformations de champs et jointures automatiques
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return querier
    */
	public function unsetStrict() {
		$this->strict = false;
		return $this;
	}

	/**
	* @author Quentin JANON<qjanon@absystech.fr>
	* Fais l'invers du setToString
	* @return querier
	*/
	public function unsetToString(){
		$this->toString = false;
		return $this;
	}

	/**
    * Ajoute une condition perso alternative
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $value : valeur
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $operand : =, !=, LIKE, NOT LIKE etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function where($field,$value,$condition="OR",$cle=false,$operand="=",$overwrite=false,$subquery=false,$join_subcondition=false) { return $this->addCondition($field,$value,$condition,$cle,$operand,$overwrite,$subquery,$join_subcondition); }
	public function addCondition($field,$value,$condition="OR",$cle=false,$operand="=",$overwrite=false,$subquery=false,$join_subcondition=false) {
		if ($field) {
			if ($cle===false) {
				$cle = $field;
			}
			if ($this->where[$cle]) {
				if ($overwrite) {
					$this->where[$cle] = NULL;
				} else {
					$this->where[$cle] .= " ".$condition." ";
				}
			}
			if ($value===NULL) {
				$this->where[$cle] .= $field." ".$operand;
			} elseif($subquery) {
				$this->where[$cle] .= $field." ".$operand." (".$value.")";
			} elseif($join_subcondition) {
				$this->where[$cle] .= $field." ".$operand." ".$value;
			} else {
				if ($operand === 'IN') {
					$this->where[$cle] .= $field." ".$operand." (".$value.")";
				} else {
					$this->where[$cle] .= $field." ".$operand." '".$value."'";
				}

			}
		}
		return $this;
	}

	/**
    * Ajoute une condition perso alternative avec pour valeur NOT NULL
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $field : champ
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function whereIsNotNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) { return $this->addConditionNotNull($field,$condition,$cle,$overwrite,$subquery,$join_subcondition); }
	public function addConditionNotNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addCondition($field,NULL,$condition,$cle,"IS NOT NULL",$overwrite,$subquery,$join_subcondition);
	}

	/**
    * Ajoute une condition perso alternative avec pour valeur NULL
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $condition : condition OR, AND ...
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function whereIsNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) { return $this->addConditionNull($field,$condition,$cle,$overwrite,$subquery,$join_subcondition); }
	public function addConditionNull($field,$condition="OR",$cle=false,$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addCondition($field,NULL,$condition,$cle,"IS NULL",$overwrite,$subquery,$join_subcondition);
	}

	/**
	* Permet de concaténer les conditions présentes dans le where par des AND (utilisé notamment pour la tracabilité)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
  * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $where
	* @return querier
	*/
	public function whereMerged($where,$operand="AND"){
		$this->where = "(".implode(")".$operand."(",$where).")";
		return $this;
	}

	/**
    * Ajoute une condition perso avec pour condition OR, la clé est impérative
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $value : valeur
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $operand : =, !=, LIKE, NOT LIKE etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function orWhere($field,$value,$cle=false,$operand="=",$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addCondition($field,$value,"OR",$cle,$operand,$overwrite,$subquery,$join_subcondition);
	}

	/**
    * Ajoute une condition perso avec pour condition AND, la clé est impérative
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field : champ
	* @param string $value : valeur
	* @param string $cle : donne une clé à cette condition pour pouvoir l'utiliser dans des super conditions etc...
	* @param string $operand : =, !=, LIKE, NOT LIKE etc...
	* @param string $overwrite : Ecraser plutot qu'ajouter a la suite
	* @param string $subquery : Dans le cas d'une sous-requête
	* @param string $join_subcondition : - Dans le cas où l'on doit faire une jointure sur plusieurs champs
								(ex: pp_user.id_privilege=pp_submit.id_privilege
									and pp_user.id_module=pp_submit.id_module)
										- ou tout simplement si l'on souhaite tester une égalité sur un nombre et non une string
	* @return querier
    */
	public function andWhere($field,$value,$cle=false,$operand="=",$overwrite=false,$subquery=false,$join_subcondition=false) {
		return $this->addCondition($field,$value,"AND",$cle,$operand,$overwrite,$subquery,$join_subcondition);
	}
//	/**
//    * Ajoute un champ particulier à retourner en SELECT mais à ignorer de la vue
//    * @author Yann GAUTHERON <ygautheron@absystech.fr>
//	* @param string|array $field :
//	*			soit plusieurs champs en tableau ou sous forme de chaîne séparés par virgules
//	*			soit un seul champ si $opt existe ; on n'attend qu'une string qui défini un seul champ qui peut être du SQL complexe
//	* @param string|array $opt contient les options pour le champs :
//	*			soit un tableau d'option
//	*			soit une string (par défaut correspond a l'option "alias")
//	* @return querier
//    */
//	public function addHiddenField($field,$opt=array()) {
//		$opt["hidden"]=true;
//		return $this->addField($field,$opt=NULL);
//	}
//
//	/**
//    * Retourne les champs à ignorer, qui ne sont pas dans la vue
//    * @author Yann GAUTHERON <ygautheron@absystech.fr>
//	* @return array
//    */
//	public function getHiddenFields() {
//		$hidden=array();
//		foreach ($this->field as $k => $i) {
//			if ($i["hidden"] && !$this->view["order"][$k]) {
//				$hidden[]=$k;
//			}
//		}
//		return $hidden;
//	}
};
?>

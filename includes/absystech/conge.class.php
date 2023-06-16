<?
/**
* @package Optima
*/
require_once dirname(__FILE__)."/../conge.class.php";
class conge_absystech extends conge {

	/**
    * Méthode d'insertion
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_conge
    */
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		$id_conge=parent::insert($infos,$s,NULL,$cadre_refreshed);

		$infos = $infos[$this->table];
		if($infos['periode']!=="autre")$infos['date_fin']=$infos['date_debut'];

		if(!$infos["id_user"])$infos["id_user"]=ATF::$usr->getID();

		ATF::user()->q->reset()->where("login","egremy");
		$emma = ATF::user()->select_row();

		$infos_user = ATF::user()->select($infos["id_user"]);

		if($emma){
			$infos['id_superieur']=$emma['id_user'];
			$infos['nom']=ATF::user()->nom($infos["id_user"]);
			$mail = new mail(array(
					"recipient"=>ATF::user()->select($emma["id_user"],'email')
					,"objet"=>ATF::$usr->trans('adresse','conge')." ".$infos['nom']
					,"template"=>"conge"
					,"optima_url"=>ATF::permalink()->getURL($this->createPermalink($id_conge))
					,"conge"=>$infos
					,"from"=>$infos['nom']." <".$infos_user["email"].">"));
			$mail->send();
		}

		return $id_conge;
	}

	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$return = parent::select_all($order_by,$asc,$page,$count);

		ATF::user()->q->reset()->where("login","egremy");
		$emma = ATF::user()->select_row();

		if(ATF::$usr->getID() == $emma["id_user"]){
			foreach ($return["data"] as $key => $value) {
				if($value["conge.etat"] == "en_cours" && ($value["id_user"] !== $emma["id_user"])){
					$return["data"][$key]["allowValid"] = true;
					$return["data"][$key]["allowRefus"] = true;
				}
			}
		}
		return $return;

	}

	/**
	* Permet de récupérer la liste des utilisateurs
	* @package Telescope
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _GET($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "date_debut";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit'] && !$get['no-limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$this->q->reset()->addJointure('conge','id_user', 'user','id_user')
						 ->select('conge.id_conge', 'id_conge')
						 ->select('conge.date_debut', 'date_debut')
						 ->select('conge.date_fin', 'date_fin')
						 ->select('conge.type', 'type')
						 ->select('conge.periode', 'periode')
						 ->select('conge.etat', 'etat')
						 ->select('conge.raison', 'raison')
						 ->select('conge.commentaire', 'commentaire')
						 ->where('user.email', $get['email'], "AND");


		if ($get['id']) {
			$this->q->where("user.id_user",$get['id'])->setLimit(1);
		} else {

			if (!$get['no-limit']) $this->q->setLimit($get['limit']);

			$data = $this->sa($get['tri'],$get['trid'],$get['page'],true);
			if ($get['id']) {
				$return = $data['data'][0];
			} else {
				header("ts-total-row: ".$data['count']);
				if ($get['limit']) header("ts-max-page: ".ceil($data['count']/$get['limit']));
				if ($get['page']) header("ts-active-page: ".$get['page']);
				if ($get['no-limit']) header("ts-no-limit: 1");
				$return = $data['data'];
			}
			return $return;
		}
	}

};

class conge_att extends conge_absystech { };
class conge_atoutcoms extends conge_absystech { };
class conge_nco extends conge_absystech { };
class conge_i2m extends conge_absystech { };
?>
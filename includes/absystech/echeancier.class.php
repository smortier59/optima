<?
/**
 * Classe echeancier
 * @package Optima
 */
class echeancier extends classes_optima {
 
  /**
   * Constructeur
   */
  public function __construct() {
    parent::__construct();

    $this->table = "echeancier";
    $this->colonnes['fields_column'] = array(
       'id_societe'
      ,'id_affaire'
      ,'designation'
      ,'montant_ht'
      ,'debut'
      ,'fin'
      ,'variable'
      ,'periodicite'
     
    );

    $this->colonnes['primary'] = array(
       'id_societe'
      ,'id_affaire'
      ,'designation'
      ,'montant_ht'
      ,'debut'
      ,'fin'
      ,'variable'
      ,'periodicite'
      ,'jour_paiement'
    );
  }
  /**
  * Fonctions _GET pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr> 
  * @param $get array contient le tri, page limit et potentiellement un id.
  * @param $post array Argument obligatoire mais inutilisé ici.
  * @return array un tableau avec les données
  */
  public function _GET($get,$post) {
    // Gestion du tri
    if (!$get['tri']) $get['tri'] = "designation";
    if (!$get['trid']) $get['trid'] = "asc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $colsData = array("id_echeancier","designation","montant_ht","commentaire","affaire","societe.id_societe","debut","fin","variable","periodicite","actif","societe","prochaine_echeance","jour_facture","methode_reglement","echeancier.id_affaire");
    $this->q->reset();
    $this->q->addField($colsData)
        ->from("echeancier","id_societe","societe","id_societe")
        ->addJointure("echeancier",'id_affaire',"affaire","id_affaire")
    ;

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

    if ($get['id_echeancier']) {
      $this->q->where("id_echeancier",$get['id_echeancier'])->setCount(false)->setDimension('row');
      $data = $this->select_all();
    } else {
      // gestion des filtres
      if ($get['filters']['actif'] == "on") {
        $this->q->andWhere("actif","oui");
      }
      $this->q->setLimit($get['limit'])->setCount();
      $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
    }
    if($get['id_echeancier']){
      // GET d'un élément, on ajoute ses lignes récurrentes et ponctuelles
      $data['periodique'] = ATF::echeancier_ligne_periodique()->select_special('id_echeancier', $get['id_echeancier']);
      $data['ponctuelle'] = ATF::echeancier_ligne_ponctuelle()->select_special('id_echeancier', $get['id_echeancier']);
      $return = $data;
    }else{
      header("ts-total-row: ".$data['count']);
      header("ts-max-page: ".ceil($data['count']/$get['limit']));
      header("ts-active-page: ".$get['page']);
      $return = $data['data'];
    }
    return $return;
  } 
  /**
  * Fonctions _POST echeancier pour telescope
  * @package Telescope 
  * @author Charlier Cyril <ccharlier@absystech.fr> 
  * @param   $[get] array 
  * @param $post array contient toutes les donnees envoyées par le formulaire
  * @return Integer & boolean
  */
  public function _POST($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $return = array();
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // Si on fait un ajout de l'echeance
    else {
      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      if($post['jour_facture'] =="custom") $post["jour_facture"]= $post["custom"];
      $explodeDebut =explode("-", $post["debut"]);
      
      // switch permettant de calculer la prochaine date d'echeance en fonction de la periodicité
      switch($post["periodicite"]){
        case "annuelle":
          $post["prochaine_echeance"]= $explodeDebut[0]."-01-01";
        break;
        case "semestrielle":
          if($explodeDebut[1]/6 <=1)
            $sem = $explodeDebut[0].'-01-01';
          else
            $sem = $explodeDebut[0].'-07-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "trimestrielle":
          if($explodeDebut[1]/3 <=1 )
              $sem = $explodeDebut[0].'-01-01';
          elseif($explodeDebut[1]/3 <=2)
              $sem = $explodeDebut[0].'-04-01';
          elseif($explodeDebut[1]/3 <=3)
            $sem = $explodeDebut[0].'-07-01';
          elseif($explodeDebut[1]/3 <=4)
            $sem = $explodeDebut[0].'-10-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "mensuelle":
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["debut"]."first day of this month"));
        break;
      }
      if($post['jour_facture'] =='fin_mois')
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["prochaine_echeance"]."first day of this month"));
      else{
          $temp =explode("-", $post["prochaine_echeance"]);
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($temp[0]."-".$temp[1])."-".$post["jour_facture"]);
      }
      unset($post["id_echeancier"],$post['custom']);
      empty(rtrim($post['fin']))? $post['fin']= NULL :$post['fin']=date("Y-m-d",strtotime($post['fin']));      
      try {
        // Try / catch pour avoir une erreur 500 forcée
        $result = $this->insert($post);       
      }catch(errorATF $e){
        throw new errorATF($e->getMessage(),500);
      }    
      $return['result'] = true;
      $return['id_echeancier'] = $result;
    }
    return $return;
  }

  public function _PUT($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $return = array();
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // Si on fait un ajout de l'echeance
    else {
      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));

      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      if($post['jour_facture'] =="custom") $post["jour_facture"]= $post["custom"];
      $explodeDebut =explode("-", $post["debut"]);
      
      // switch permettant de calculer la prochaine date d'echeance en fonction de la periodicité
      switch($post["periodicite"]){
        case "annuelle":
          $post["prochaine_echeance"]= $explodeDebut[0]."-01-01";
        break;
        case "semestrielle":
          if($explodeDebut[1]/6 <=1)
            $sem = $explodeDebut[0].'-01-01';
          else
            $sem = $explodeDebut[0].'-07-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "trimestrielle":
          if($explodeDebut[1]/3 <=1 )
              $sem = $explodeDebut[0].'-01-01';
          elseif($explodeDebut[1]/3 <=2)
              $sem = $explodeDebut[0].'-04-01';
          elseif($explodeDebut[1]/3 <=3)
            $sem = $explodeDebut[0].'-07-01';
          elseif($explodeDebut[1]/3 <=4)
            $sem = $explodeDebut[0].'-10-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "mensuelle":
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["debut"]."first day of this month"));
        break;
      }
      if($post['jour_facture'] =='fin_mois')
        $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["prochaine_echeance"]."first day of this month"));
      else{
        $temp =explode("-", $post["prochaine_echeance"]);
        $post["prochaine_echeance"]= date("Y-m-d",strtotime($temp[0]."-".$temp[1])."-".$post["jour_facture"]);
      }
      empty(rtrim($post['fin']))? $post['fin']= NULL :$post['fin']=date("Y-m-d",strtotime($post['fin']));      
      unset($post['custom']);
      $result = $this->update($post);       
      $return['result'] = true;
      $return['id_echeancier'] = $post["id_echeancier"];
    }
    return $return;
  }
  /**
  * Permet de supprimer un contrat
  * @package Telescope
  * @author Cyril CHARLIER <ccharlier@absystech.fr> 
  * @param $get array contient l'id a l'index 'id'
  * @param $post array vide
  * @return array result en booleen et notice sous forme d'un tableau
  */ 
  public function _DELETE($get,$post) {
    if (!$get['id']) throw new Exception("MISSING_ID",1000);
    $get["actif"]="non";
    $get["id_echeancier"]=$get['id'];
    unset($get["id"],$get["path"],$get["method"]);
    $return['result'] = $this->update($get);
    return $return;
  }

  public function _getPdf(&$get, $post){

    $id_echeancier = $get["id_echeancier"];
    $lignes = $get["lignes"];

    $date_debut_periode = $get["prochaine_echeance"];
    $date_fin_periode   = $get["fin_echeance"];
    $date_facture = $get["date_facture"];

    $echeancier = $this->select($id_echeancier);

    $produits = array();

    $lignes_echeancier = $this->getLignesPeriode($id_echeancier , $date_debut_periode, $date_fin_periode);

    foreach ($lignes_echeancier as $key => $value) {
      $p=array();
     
      $p["facture_ligne__dot__ref"] = $value["ref"];
      $p["facture_ligne__dot__produit"] = $value["designation"];        

      if($value["valeur_variable"] == "oui"){
        $p["facture_ligne__dot__quantite"] = $lignes[$value["type"]][$value["id_echeancier_ligne_".$value["type"]]]["quantite"];
        $p["facture_ligne__dot__prix"] = $lignes[$value["type"]][$value["id_echeancier_ligne_".$value["type"]]]["puht"];
      }else{
        $p["facture_ligne__dot__quantite"] = $value["quantite"];
        $p["facture_ligne__dot__prix"] = $value["puht"];
      }
      
      $p["facture_ligne__dot__prix_achat"] = NULL;
      $p["facture_ligne__dot__id_fournisseur_fk"] = NULL;            
      $p["facture_ligne__dot__id_compte_absystech_fk"] = $value["id_compte_absystech"];
      $p["facture_ligne__dot__serial"] = null;
      $p["facture_ligne__dot__prix_nb"] = null;
      $p["facture_ligne__dot__prix_couleur"] = null;
      $p["facture_ligne__dot__prix_achat_nb"] = null;
      $p["facture_ligne__dot__prix_achat_couleur"] = null;
      $p["facture_ligne__dot__index_nb"] = null;
      $p["facture_ligne__dot__index_couleur"] = null;
      $p["facture_ligne__dot__visible"] = "oui";
      $p["facture_ligne__dot__marge"] = NULL;
      $p["facture_ligne__dot__marge_absolue"] = NULL;

      $produits[] = $p;
    }

    $facture = array();

    $facture["facture"] = array("id_societe"=> $echeancier["id_societe"],
                                "type_facture" => "facture_periodique",
                                "date"=> $date_facture,
                                "infosSup" => NULL,                                
                                "id_affaire" => $echeancier["id_affaire"],
                                "date_previsionnelle" => NULL,
                                "date_relance" => NULL,
                                "affaire_sans_devis_libelle" => NULL,
                                "mode" => NULL, 
                                "periodicite" => $echeancier["periodicite"],
                                "id_facture_parente" => NULL,
                                "id_termes" => NULL, 
                                "date_debut_periode" => $date_debut_periode,
                                "date_fin_periode" => $date_fin_periode,
                                "sous_total" => NULL, 
                                "marge" => NULL, 
                                "frais_de_port" => NULL, 
                                "marge_absolue" => NULL,
                                "prix" =>  NULL,
                                "tva" => "1.200",
                                "prix_achat" => NULL,
                                "email" => NULL,
                                "emailTexte" => NULL,
                                "emailCopie" => NULL
                               );
    $facture["values_facture"]["produits"] = json_encode($produits);
    if($get["preview"]){
      $facture["preview"] = true;
    }else{
      if(!$get["fin_contrat"]){
        // on change la date de début de prochaine echeance s'il n'y a pas de fin de contrat
        ATF::echeancier()->u(
          array(
            "id_echeancier"=>$id_echeancier, 
            "prochaine_echeance"=>
              date("Y-m-d",strtotime("+1 day",strtotime($date_fin_periode)))
          )
        );
      }else{
        // si la date de fin de contrat est avant la fin de période actuelle
        if(date('Y-m-d',strtotime($get["fin_contrat"])) < date('Y-m-d', strtotime($date_fin_periode)) ){
          // le contrat est fini, on peut cacher la ligne de la liste des facturations
          ATF::echeancier()->u(array("id_echeancier"=>$id_echeancier,"actif"=>"non"));         
        }else{
           ATF::echeancier()->u(
            array(
              "id_echeancier"=>$id_echeancier, 
              "prochaine_echeance"=>
              date("Y-m-d",strtotime("+1 day",strtotime($date_fin_periode)))
            )
          );
        }   
      }
    }
    $facture["echeancier"] = true;


    try{
      $return = ATF::facture()->insert($facture);
            
      /*header('Content-Type: application/pdf');
      header("Content-Transfer-Encoding: binary");
      header('x-optima-binary: pdf');

      echo $return;
      die;
log::logger($return,ygautheron)      ;
      $get["display"] = true;
      return $return;

      //*/return base64_encode($return);
    
    }catch(errorATF $e){
      throw new errorATF($e->getMessage(),500);
    }    
  }


  public function _getLignePeriode($get , $post){
    return $this->getLignesPeriode($get["id_echeancier"], $get["periode_debut"], $get["periode_fin"], true);
  }


  public function getLignesPeriode($id_echeancier , $periode_debut, $periode_fin, $from_web = false){
    $d1 = explode("-",$periode_debut);
    $periode_debut = $d1[2].$d1[1].$d1[0];
    $d2 = explode("-",$periode_fin);
    $periode_fin = $d2[2].$d2[1].$d2[0];

    ATF::echeancier_ligne_ponctuelle()->q->reset()->where("id_echeancier", $id_echeancier);
    $echeancier_ligne_ponctuelle = ATF::echeancier_ligne_ponctuelle()->select_all();

    ATF::echeancier_ligne_periodique()->q->reset()->where("id_echeancier", $id_echeancier);
    $echeancier_ligne_periodique = ATF::echeancier_ligne_periodique()->select_all();

    foreach ($echeancier_ligne_ponctuelle as $key => $value) {      
      $d3 = str_replace("-", "", $value["date_valeur"]);   

      if($periode_debut <= $d3 && $d3 <= $periode_fin){
          $value["type"] = "ponctuelle";
          $lignes[] = $value;
      }      
    }

    foreach ($echeancier_ligne_periodique as $key => $value) {     
      $value["type"] = "periodique";
      $lignes[] = $value;
    }

    //Si from_web a true c'est qu'on viens de télescope et il faut des informations pour afficher les infos sur la page
    if($from_web){
      $return["lignes"] = $lignes;
      $return["echeancier"] = ATF::echeancier()->select($id_echeancier);
      $return["echeancier"]["societe"] = ATF::societe()->select($return["echeancier"]["id_societe"], "societe");
      $return["echeancier"]["affaire"] = ATF::affaire()->select($return["echeancier"]["id_affaire"], "affaire");
      return $return;
    }
    return $lignes;   
  }




}
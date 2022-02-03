<?
/**
* Classe Societé
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../societe.class.php";
class societe_cleodis extends societe {
  /*--------------------------------------------------------------*/
  /*                   Constructeurs                              */
  /*--------------------------------------------------------------*/
  public function __construct() {
    parent::__construct();
    $this->table = "societe";
    unset($this->colonnes['panel']['facturation_fs']);

    /*-----------Quick Insert-----------------------*/
    $this->quick_insert = array('societe'=>'societe');

    $this->colonnes['fields_column'] = array(
      'societe.societe'
      ,'societe.tel' => array("tel"=>true)
      ,'societe.fax' => array("tel"=>true)
      ,'societe.email'
      ,'societe.ville'
      ,"societe.nom_commercial"
      ,"societe.code_client"
      ,'logo'=> array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70,"renderer"=>"uploadFile")
    );

    // Panel prinicpal
    $this->colonnes['primary'] = array(
      "code_client"
      ,"ref"
      ,"id_famille"=>array("listeners"=>array("change"=>"ATF.changeFamille"))
      ,"code_client_partenaire"
      ,"nom"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
        "societe"
        ,"nom_commercial"
      ))
      ,'mauvais_payeur'
      ,'contentieux_depuis'
    );

    //Reinitialise les panels poru remettre dans l'ordre
    $panel = $this->colonnes['panel'];
    $this->colonnes['panel'] = array();


    $this->colonnes['panel']['societe_fs'] = array(
      "sirens"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
        "siren"
        ,"siret"
      ))
      ,"avis_credit"
      ,"cs_avis_credit"
      ,"lastScoreDate"
      ,"score"
      ,"cs_score"
      ,"contentieux"
      ,"id_owner"
      ,"etat"
      ,"id_assistante"
      ,"id_filiale"
      ,"date_creation"
      ,"relation"
      ,"joignable"

    );
    $this->panels['societe_fs'] = array('nbCols'=>2,'collapsible'=>false,'visible'=>true);

    $this->colonnes['panel']['particulier_fs'] = array(
      "client"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
           "particulier_civilite"
          ,"particulier_nom"
          ,"particulier_prenom"
      )),
      "particulier_portable",
      "particulier_fixe",
      "particulier_fax",
      "particulier_email"
    );
    $this->panels['particulier_fs'] = array('nbCols'=>2,'collapsible'=>false,'visible'=>true);




    foreach ($panel as $key => $value) {
      $this->colonnes['panel'][$key] = $value;
    }


    $this->colonnes['panel']['fidelite'] = array(
      'num_carte_fidelite',
      'dernier_magasin'
    );
    $this->panels['fidelite'] = array('nbCols'=>2,'collapsible'=>true,'visible'=>false);

    $this->colonnes['panel']['optin'] = array(
      'optin_offre_commerciales',
      'optin_offre_commerciale_partenaire'
    );
    $this->panels['optin'] = array('nbCols'=>2,'collapsible'=>true,'visible'=>false);


    /* Définition statique des clés étrangère de la table */
    $this->onglets = array(
       'contact'=>array('opened'=>true)
      ,'affaire'=>array('opened'=>true)
      ,'formation_devis'
      ,'suivi'=>array('opened'=>true)
      ,'devis'
      ,'commande'
      ,'tache'
      ,'parc'
      ,'ged'
      ,'pdf_societe'
      ,'user'
      ,'societe'=>array('field'=>'societe.id_filiale')/*,'societe_domaine'*/
    );
    // Infos codifiées
    $this->colonnes['panel']['codes_fs']["les_codes"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
      "code_groupe"
      ,"code_fournisseur"
    ));

    // Facturation
    $this->colonnes['panel']['facturation_fs']["ref_tva"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
      "reference_tva"
      ,"tva"
      ,"ville_rcs"
    ));
    $this->colonnes['panel']['facturation_fs']["banque_ref"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
      "IBAN"
      ,"BIC"
    ));
    $this->colonnes['panel']['facturation_fs'][]="RIB";
    $this->colonnes['panel']['facturation_fs'][]="RUM";
    $this->colonnes['panel']['facturation_fs'][]="divers_2";
    $this->colonnes['panel']['facturation_fs'][]="nom_banque";
    $this->colonnes['panel']['facturation_fs'][]="ville_banque";
    $this->colonnes['panel']['facturation_fs'][]='rum';

    // Adresses en +
    $this->colonnes['panel']["coordonnees_supplementaires_fs"]["adresse_siege_social"]=array("xtype"=>"textarea");

    $this->colonnes['panel']['adresse_complete_fs']["id_contact_signataire"]=array("xtype"=>"int","numberfield"=>8);

    // Portail
    $this->colonnes['panel']['portail_fs'] = array(
      "divers_3"
      ,"id_accompagnateur"
    );
    $this->panels['portail_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);
    $this->colonnes['panel']["structure_secteur_fs"]["portail"]=array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'portail_fs');
    $this->colonnes['panel']["structure_secteur_fs"][] = "lastaccountdate";
    $this->colonnes['panel']["structure_secteur_fs"][] = "receivables";
    $this->colonnes['panel']["structure_secteur_fs"][] = "securitieandcash";
    $this->colonnes['panel']["structure_secteur_fs"][] = "operatingincome";
    $this->colonnes['panel']["structure_secteur_fs"][] = "netturnover";
    $this->colonnes['panel']["structure_secteur_fs"][] = "operationgprofitless";
    $this->colonnes['panel']["structure_secteur_fs"][] = "financialincome";
    $this->colonnes['panel']["structure_secteur_fs"][] = "financialcharges";

    $this->colonnes['panel']['autres'] = array("id_apporteur","id_fournisseur","id_prospection","id_campagne");

    $this->colonnes['panel']['delai_rav'] = array(
      "fournisseur_delai_rav"
      ,"fournisseur_delai_livraison"
      ,"fournisseur_delai_installation"
      ,"fournisseur_arav_orange"
      ,"fournisseur_arav_rouge"
    );

    $this->colonnes['panel']['delai_fournisseur'] = array(
       "fournisseur_nbj_livraison"
      ,"fournisseur_nbj_installation"
    );

    $this->panels['delai_rav'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);
    $this->panels['delai_fournisseur'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);

    $this->colonnes['panel']['deploiement'] = array(
       "delai_rav_panel"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'delai_rav')
      ,"delai_fournisseur_panel"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'delai_fournisseur') );
    $this->panels['deploiement'] = array("visible"=>true);


    $this->colonnes['bloquees']['select'] = array('joignable');


    if(ATF::$codename == "cleodisbe"){

      $this->colonnes['primary']["sirens"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
        "num_ident"
        ,"reference_tva"
      ));

      $this->colonnes['panel']['facturation_fs']["ref_tva"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array("tva"));

    }

    $this->fieldstructure();


    unset($this->colonnes['panel']['facturation_fs']["solde_et_relance"]);
    $this->checkAndRemoveBadFields('caracteristiques');
    $this->checkAndRemoveBadFields('facturation_fs');

    $this->foreign_key["id_apporteur"] = "societe";
    $this->foreign_key["id_fournisseur"] = "societe";
    $this->foreign_key["id_contact_signataire"] = "contact";
    $this->foreign_key["id_prospection"] = "contact";
    $this->foreign_key["id_assistante"] = "user";
    $this->foreign_key["id_owner"] = "user";

    $this->files["logo"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);




    // Pouvoir modifier massivement
    $this->no_update_all = false;

    // on montre que pour joindre la table domaine, on passe par une table de jointure qui est societe_domaine, si on créé un filtre dans le module société
    $this->listeJointure['domaine']="societe_domaine";

    // Droits sur méthodes Ajax
    $this->addPrivilege("getParc");
    $this->addPrivilege("getParcVente");
    $this->addPrivilege("getTreePanel");
    $this->addPrivilege("getChildren");
    $this->addPrivilege("export_atol");
    $this->addPrivilege("getInfosFromCREDITSAFE");

    $this->addPrivilege("autocompleteFournisseursDeCommande");
    $this->addPrivilege("autocompleteAvecAdresse");
    $this->addPrivilege("autocompleteAvecFiliale");
    $this->addPrivilege("autocompleteFournisseurFormationDevis");
    $this->addPrivilege("importProspect","insert");
    $this->addPrivilege("autocompleteSociete");

    $this->autocomplete = array(
      "field"=>array("societe.societe","societe.nom_commercial","societe.code_client")
      ,"show"=>array("societe.societe","societe.nom_commercial","societe.code_client")
      ,"popup"=>array("societe.societe","societe.nom_commercial","societe.code_client")
      ,"view"=>array("societe.societe","societe.nom_commercial","societe.code_client")
    );


  }


  /** Fonction qui génère les résultat pour les champs d'auto complétion société pour CLEOSCOPE
  * @authorMorgan FLEURQUIN <mfleurquin@absystech.fr>
  */
  public function _ac($get,$post) {
    $this->q->reset()->setLimit(10);


    // On ajoute les champs utiles pour l'autocomplete
    $this->q->addField("id_societe")->addField("societe")->addField("ref")->addField("societe");

    if ($get['q']) {
      $this->q->setSearch($get["q"]);
    }

    // Clause globale
    $this->q->where("etat","actif");
    $this->q->addOrder("societe","ASC");
    return $this->select_all();
  }



  public function getOpca(){
    $this->q->reset()->where("societe.etat", "actif");
    return $this->select_all();
  }

  /*
  * Surcharge de l'export
  * Permet de formater les champs textes de CreditSafe pour pouvoir faire les manip dans les fichiers Excel
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  */
  public function export(&$infos,&$s){

    $champsFormate = array( "societe.receivables",
                "societe.securitieandcash",
                "societe.operatingincome",
                "societe.netturnover",
                "societe.operationgprofitless",
                "societe.financialincome",
                "societe.financialcharges"
                );
    foreach ($champsFormate as $key => $value) {
      if(!array_key_exists($value, $infos[0])){ unset($champsFormate[$key]); }
    }
    foreach ($infos as $k => $v) {
      foreach ($champsFormate as $key => $value) { $infos[$k][$value] = str_replace(" ", "", $infos[$k][$value]); }
    }
    parent::export($infos,$s);
  }




  /** Permet d'exporter les sociétés ATOL ayant au moins 1 bon de commande
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @param array $infos : contient le nom de l'onglet
    */
    public function export_atol($infos,$testUnitaire="false", $reset="true"){

    if($testUnitaire == "true"){
      $donnees = $infos;
    }else{
      $query_select = "SELECT societe.* FROM societe, bon_de_commande
               WHERE societe.id_societe = bon_de_commande.id_societe
               AND societe.divers_3 = 'Atol'
               AND societe.id_societe IN (SELECT id_societe FROM bon_de_commande)
               GROUP BY societe.id_societe";

      $donnees = ATF::db()->sql2array($query_select);
    }

    require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
    require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
    $fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
    $workbook = new PHPExcel;

    //premier onglet
    $worksheet_auto = new PHPEXCEL_ATF($workbook,0);
    $worksheet_auto->sheet->setTitle('ATOL');
    $sheets=array("auto"=>$worksheet_auto);

    $row_data = array(
                 "A" => 'Code magasin Atol'
                ,"B" => "Raison Sociale"
                ,"C" => "Civilité"
                ,"D" => "Nom"
                ,"E" => "Prénom"
                ,"F" => "Adresse"
                ,"G" => "Adresse 2"
                ,"H" => "Adresse 3"
                ,"I" => "Code Postal"
                ,"J" => "Ville"
                ,"K" => "Email"
                ,"L" => "Téléphone"
            );
    $i=0;

        foreach($row_data as $col=>$titre){
        $sheets['auto']->write($col."1", $titre);
        $sheets["auto"]->sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $row_data = array();
    $k=0;
       foreach ($donnees as $key => $value) {
          $row_data[$k] = array(
             "A" => $value["code_client"]." "
            ,"B" => $value["societe"]
            ,"C" => ATF::contact()->select($value["id_contact_signataire"], "civilite")
            ,"D" => ATF::contact()->select($value["id_contact_signataire"], "nom")
            ,"E" => ATF::contact()->select($value["id_contact_signataire"], "prenom")
            ,"F" => $value["adresse"]
            ,"G" => $value["adresse_2"]
            ,"H" => $value["adresse_3"]
            ,"I" => $value["cp"]." "
            ,"K" => $value["ville"]
            ,"J" => ATF::contact()->select($value["id_contact_signataire"], "email")
            ,"L" => $value["tel"]." "
      );
      $k++;
        }

        if($row_data){
          $row_auto=2;
          foreach ($row_data as $k => $v) {
            foreach($v as $col=>$valeur){
          $sheets['auto']->write($col.$row_auto, $valeur);
        }
        $row_auto++;
      }
        }



    $writer = new PHPExcel_Writer_Excel5($workbook);

    $writer->save($fname);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition:inline;filename=export_ATOL.xls');
    header("Cache-Control: private");
    $fh=fopen($fname, "rb");
    fpassthru($fh);
    unlink($fname);
    PHPExcel_Calculation::getInstance()->__destruct();

  }


  /**
  * Méthode qui retourne les parcs dispo pour les Avenants et les AR
  * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
  * @param array $ligne_affaires
  */
  public function getParc(&$infos){
    $id_societe=$this->decryptId($infos["id_societe"]);

    $this->q->reset() ->addField("affaire.id_affaire")
             ->addField("affaire.ref")
             ->addField("affaire.affaire")
             ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
             ->addJointure("affaire","id_affaire","commande","id_affaire",NULL,NULL,NULL,NULL,"INNER")
             ->addCondition("affaire.id_societe",$id_societe,NULL,1)
             ->addCondition("affaire.id_filiale",$id_societe,"XOR",1)
             ->addCondition("commande.etat","non_loyer",NULL,2)
             ->addCondition("commande.etat","mis_loyer","OR",2)
             ->addCondition("commande.etat","mis_loyer_contentieux","OR",2)
             ->addCondition("commande.etat","prolongation_contentieux","OR",2)
             ->addCondition("commande.etat","prolongation","OR",2)
             ->addCondition("commande.etat","restitution_contentieux","OR",2)
             ->addCondition("commande.etat","restitution","OR",2);

    if($infos["type"]=="avenant"){
      $this->q->addCondition("affaire.nature","avenant","AND",NULL,"!=");
    }

    if($affaire=parent::sa("affaire.id_affaire")){
      foreach($affaire as $key=>$item){
        //Parc de l'affaire
        ATF::parc()->q->reset()->addCondition("parc.id_affaire",$item["affaire.id_affaire_fk"])
                     ->addCondition("parc.existence","actif","AND")
                     ->addCondition("parc.etat","loue","AND",1)
                     ->addCondition("parc.etat","reloue","OR",1)
                     ->addOrder("parc.id_parc","asc");

        $parc=ATF::parc()->sa();

        if($ligne_affaire=$this->formateGetParc($parc,$item,$infos["id_affaire"])){
          $ligne_affaires[]=$ligne_affaire;
        }
      }
    }
    $infos['display'] = true;

    return json_encode($ligne_affaires);
  }

  /**
  * Méthode qui formate les données pour qu'elles soient comprises par le treepanel
  * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
  * @param array $ligne_affaire
  */
  public function formateGetParc($parc,$item,$id_affaire_courante=false){

    if($parc){
      $id_affaire=ATF::affaire()->cryptId($item["affaire.id_affaire_fk"]);
      foreach($parc as $k=>$i){
        //Il faut tester si ce parc n'est pas déjà en inactif dans une affaire (c'est à dire en attente de devenir actif)
        //Exemple : Une affaire B vient AR une affaire A, si je fais un avenant C sur l'affaire A je ne dois pas récupérer les parcs déjà récupérer par l'affaire B sauf si le devis de B est perdu
        ATF::parc()->q->reset()
                ->addCondition("id_affaire",$i["id_affaire"],"AND",false,">")
                ->addCondition("serial",$i["serial"],"AND")
                ->addOrder("id_parc","asc");

        if($i["provenance"]){
          ATF::parc()->q->addCondition("id_affaire",$i["provenance"],"AND",false,"!=");
        }

        if($id_affaire_courante){
          ATF::parc()->q->addCondition("id_affaire",$id_affaire_courante,"AND",false,"!=");
        }

        $otherParc=ATF::parc()->sa();

        $parc_libre=true;
        if(count($otherParc)>0){
          foreach($otherParc as $k_=>$i_){
            if(ATF::affaire()->select($i_["id_affaire"],"etat")!="perdue"){
              $parc_libre=false;
            }
          }
        }

        if($parc_libre){
          if($i["provenance"]){
            $suffix=" - Parc provenant de l'affaire ".$item["affaire.affaire"]." (".$item["affaire.ref"].")";
          }else{
            $suffix="";
          }
          $produit=ATF::produit()->select($i["id_produit"]);
          $ligne_parc[]=array(
            "text"=>$i["libelle"]." ".$i["ref"]." (".$i["serial"].") - '".ATF::$usr->trans($i["etat"])."'".$suffix
            ,"id"=>"parc_".$i["id_parc"]
            ,"leaf"=>true
            ,"icon"=>ATF::$staticserver."images/blank.gif"
            ,"checked"=>false
            ,"id_produit_fk"=>$i["id_produit"]
            ,"produit"=>$i["libelle"]
            ,"ref"=>$produit["ref"]
            ,"type"=>$produit["type"]
            ,"serial"=>$i["serial"]
            ,"quantite"=>1
            ,"visibilite_prix"=>"visible"
            ,"id_affaire_provenance"=>$item["affaire.id_affaire_fk"]
            ,"id_parc"=>$i["id_parc"]
          );
        }
      }

      if ($ligne_parc) {
        $ligne_affaire=array(
          "text"=>$item["affaire.ref"]." ".$item["affaire.affaire"]
          ,"id"=>"affaire_".$item["affaire.id_affaire_fk"]
          ,"leaf"=>false
          ,"href"=>"javascript:window.open('affaire-select-".$id_affaire.".html');"
          ,"cls"=>"folder"
          ,"expanded"=>false
          ,"adapter"=>NULL
          ,"children"=>$ligne_parc
          ,"checked"=>false
        );
      }
      return $ligne_affaire;
    }else{
      return false;
    }
  }

  /**
  * Méthode qui retourne les parcs dispo pour les ventes
  * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
  * @param array $ligne_affaires
  */
  public function getParcVente(&$infos){
    $id_societe=$this->decryptId($infos["id_societe"]);

    $this->q->reset()->addOrder("affaire.id_affaire","asc")
             ->addField("affaire.id_affaire")
             ->addField("affaire.ref")
             ->addField("affaire.affaire")
             ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
             ->addJointure("affaire","id_affaire","commande","id_affaire",NULL,NULL,NULL,NULL,"INNER")
             ->addCondition("affaire.id_societe",$id_societe,NULL,1)
             ->addCondition("commande.etat","arreter",false,false,"!=")
             ->addCondition("affaire.id_filiale",$id_societe,"XOR",1);

    if($affaire=parent::sa()){
      foreach($affaire as $key=>$item){
        //Parc de l'affaire
        ATF::parc()->q->reset()->addCondition("parc.id_affaire",$item["affaire.id_affaire_fk"])
                     ->addCondition("parc.existence","actif","AND")
                     ->addCondition("parc.etat","broke","AND");
        $parc1=ATF::parc()->sa();

        if($ligne_affaire=$this->formateGetParc($parc1,$item,$infos["id_affaire"])){
          $ligne_affaires[]=$ligne_affaire;
        }
      }
    }

    $this->q->reset()->addOrder("affaire.id_affaire","asc")
             ->addField("affaire.id_affaire")
             ->addField("affaire.ref")
             ->addField("affaire.affaire")
             ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
             ->addJointure("affaire","id_affaire","commande","id_affaire",NULL,NULL,NULL,NULL,"INNER")
             ->addCondition("affaire.id_societe",$id_societe,NULL,1)
             ->addCondition("commande.etat","arreter")
             ->addCondition("affaire.id_filiale",$id_societe,"XOR",1);

    if($affaire=parent::sa()){
      foreach($affaire as $key=>$item){
        //Parc de l'affaire
        ATF::parc()->q->reset()
                ->addCondition("parc.id_affaire",$item["affaire.id_affaire_fk"])
                ->addCondition("parc.existence","actif");

        $parc2=ATF::parc()->sa();

        if($ligne_affaire=$this->formateGetParc($parc2,$item,$infos["id_affaire"])){
          $ligne_affaires[]=$ligne_affaire;
        }
      }
    }

    $infos['display'] = true;

    return json_encode($ligne_affaires);
  }





  /**
  * Autocomplete retournant seulement les fournisseurs ayant des produits dans les ligne de la commande passée en paramètre,
  * des lignes qui ne sont pas reprise (sans id_affaire_provenance)
  * @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
  * @param array $infos ($_POST habituellement attendu)
  * string $infos[recherche]
  * @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
  * @return string HTML de retour
  */

  public function autocompleteFournisseursDeCommande($infos,$reset=true) {
    if ($reset) {
      $this->q->reset();
    }
    $this->q
      ->from("societe","id_societe","commande_ligne","id_fournisseur")
      ->addGroup("societe.id_societe")
      ->addField("societe.id_contact_signataire")
      ->addField(array("CONCAT(societe.id_contact_signataire)"=>array("alias"=>"id_contact_signataire_fk","nosearch"=>true)))
      ->where("societe.fournisseur","oui");
    return parent::autocomplete($infos,false);
  }

  /**
  * Permet de retourner l'adresse d'une societe
  * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
  * @param array $infos ($_POST habituellement attendu)
  * @param string $infos[recherche]
  * @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
  * @return string HTML de retour
  */
  public function autocompleteAvecAdresse($infos,$reset=true) {
    if ($reset) {
      $this->q->reset();
    }
    $this->q
      ->addField("adresse")
      ->addField("ville")
      ->addField("cp")
      ->where("societe.fournisseur","oui");
    return $this->autocomplete($infos,false);
  }
  /**
  * Permet de retourner l'adresse d'une societe
  * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
  * @param array $infos ($_POST habituellement attendu)
  * string $infos[recherche]
  * @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
  * @return string HTML de retour
  */
  public function autocompleteAvecFiliale($infos,$reset=true) {


    if ($reset) {
      $this->q->reset();
    }
    $this->q
       ->addCondition("societe","%cleodis%",NULL,false,"LIKE")
       ->addField("tva");
    $autocomplete= $this->autocomplete($infos,false);
    return $autocomplete;
  }

  /**
  * Mise à jour des infos
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
  * @param array &$s La session
  * @param array $files $_FILES
  * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
  */
  public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

    //Si on insere un particulier, il faut un Nom Prénom et civilité et on renseigne le champs société pour les listing
    if($infos['label_societe']['famille'] === "Foyer"){
        if(!$infos['societe']["particulier_civilite"]){
          throw new errorATF("Le champs civilite est obligatoire pour un particulier!",878);
        } elseif(!$infos['societe']["particulier_nom"]){
          throw new errorATF("Le champs nom est obligatoire pour un particulier!",878);
        }elseif(!$infos['societe']["particulier_prenom"]){
          throw new errorATF("Le champs prenom est obligatoire pour un particulier!",878);
        }else{
          $infos["societe"]["societe"] = $infos['societe']["particulier_civilite"]." ".$infos['societe']["particulier_nom"]." ".$infos['societe']["particulier_prenom"];
        }
    }


    if($infos["societe"]["siret"] != NULL){
      //On check si le siret existe déja
      $this->q->reset()->where("siret",$infos["societe"]["siret"],"AND")->where("id_societe",$this->decryptId($infos["societe"]["id_societe"]),"AND",false,"!=");
      if($this->select_all()){
        throw new errorATF("Une société existe déja avec le SIRET ".$infos["societe"]["siret"],878);
      }
    }

    // Vérification qu'il n'existe aucun autre parc d'existence active avec le même serial
    $this->infoCollapse($infos);

    // Si avis_credit change, on crée un suivi !
    $avis_credit = $this->select($infos["id_societe"],"avis_credit");

    $notifie = ATF::user()->getDestinataireFromConstante(' __NOTIFIE_AVIS_CREDIT_SOCIETE_UPDATE__');


    if (!preg_match("/".$this->select($infos["id_societe"],"id_owner")."/",$notifie)) {
      $notifie .= ",".$this->select($infos["id_societe"],"id_owner");
    }

    if ($infos["avis_credit"] && ($avis_credit !== $infos["avis_credit"])) {
      $suivi = array(
        "id_user"=>ATF::$usr->get('id_user')
        ,"id_societe"=>$infos['id_societe']
        ,"type_suivi"=>'Contentieux'
        ,"texte"=>"La société passe de l'avis crédit '".ATF::$usr->trans("societe_avis_credit_".$avis_credit)."' à '".ATF::$usr->trans("societe_avis_credit_".$infos["avis_credit"])."'"
        ,'public'=>'non'
        ,'suivi_societe'=>array(0=>ATF::$usr->getID())
        ,'suivi_notifie'=>$notifie
      );
      ATF::suivi()->insert($suivi);
    }

    // Si score change, on crée un suivi !
    $score = $this->select($infos["id_societe"],"score");
    if ($infos["score"] && $score!=$infos["score"]) {
      $suivi = array(
        "id_user"=>ATF::$usr->get('id_user')
        ,"id_societe"=>$infos['id_societe']
        ,"type_suivi"=>'Contentieux'
        ,"texte"=>"La société passe du score '".$score."' à '".$infos["score"]."'"
        ,'public'=>'non'
        ,'suivi_societe'=>array(0=>ATF::$usr->getID())
        ,'suivi_notifie'=>$notifie
      );
      ATF::suivi()->insert($suivi);
    }

    return parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
  }


  /**
  * Insert
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
  * @param array &$s La session
  * @param array $files $_FILES
  * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
  */
  public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

    if($infos["societe"]["siret"] != NULL){
      //On check si le siret existe déja
      $this->q->reset()->where("siret",$infos["societe"]["siret"]);
      if($this->select_all()){
        throw new errorATF("Une société existe déja avec le SIRET ".$infos["societe"]["siret"],878);
      }
    }

    //Si on insere un particulier, il faut un Nom Prénom et civilité et on renseigne le champs société pour les listing
    if($infos['label_societe']['id_famille'] === "Foyer"){
        if(!$infos['societe']["particulier_civilite"]){
          throw new errorATF("Le champs civilite est obligatoire pour un particulier!",878);
        } elseif(!$infos['societe']["particulier_nom"]){
          throw new errorATF("Le champs nom est obligatoire pour un particulier!",878);
        }elseif(!$infos['societe']["particulier_prenom"]){
          throw new errorATF("Le champs prenom est obligatoire pour un particulier!",878);
        }else{
          $infos["societe"]["societe"] = $infos['societe']["particulier_civilite"]." ".$infos['societe']["particulier_prenom"]." ".$infos['societe']["particulier_nom"];
        }
    }

    //Creation d'un Nouveau RUM automatique
    if(!$infos["societe"]['RUM']){
      $infos["societe"]['RUM'] = $this->create_rum();
      if($infos["societe"]['code_client']){

        if(strlen($infos["societe"]['code_client']) === 6){

          $infos["societe"]['RUM'] .= $infos["societe"]['code_client'];
        }elseif(strlen($infos["societe"]['code_client']) > 6){
          $infos["societe"]['RUM'] .= substr($infos["societe"]['code_client'], -6);
        }else{
          for ($i=0; $i < 6 - strlen($infos["societe"]['code_client']); $i++) {
            $infos["societe"]['RUM'] .= '0';
          }
          $infos["societe"]['RUM'] .= $infos["societe"]['code_client'];
        }
      }else{
        $infos["societe"]['RUM'] .= '000000';
      }
    }


    return parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
  }


  /**
   * Permet de générer un nouveau RUM automatique
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   * @return String RUM du type A123456
   */
  public function create_rum(){

    $prefixe = "A";

    //On recupere le dernier RUM automatique généré du type A123456
    //Recherche du max en base
    $this->q->reset()
      ->addField('MAX(SUBSTRING(RUM, 2,6))','max')
      ->addCondition('RUM',$prefixe.'%','OR',false,'LIKE');
    $result=$this->sa();

    if(isset($result[0]['max'])){
      $max = intval($result[0]['max'])+1;
    }else{
      $max = 1;
    }


    if($max<10){
      $ref.='00000'.$max;
    }elseif($max<100) {
      $ref.= '0000'.$max;
    }elseif($max<1000){
      $ref.='000'.$max;
    }elseif($max<10000){
      $ref.='00'.$max;
    }elseif($max<100000){
      $ref.='0'.$max;
    }elseif($max<1000000){
      $ref.=$max;
    }else{
      throw new errorATF(ATF::$usr->trans('RUM trop grand'),80853);
    }
    return $prefixe.$ref;


  }


  public function autocompleteFournisseurFormationDevis($infos,$reset=true,$count=false){
    if($reset){
      $this->q->reset();
    }

    $this->q->from("societe","id_societe","formation_devis_fournisseur","id_societe")
        ->from("formation_devis_fournisseur","id_formation_devis","formation_devis","id_formation_devis")
        ->where($infos["condition_field"],ATF::formation_devis()->decryptId($infos["condition_value"]))
        ->addField("societe.id_societe","id_societe")
        ->addField("societe.societe","nom");
    if($count){
      $this->q->setCount();
      $return = $this->select_all();
    }else{
      $return = parent::autocomplete($infos,false);
    }

    return $return;
  }

  /*
  Fonction non utilisée dans OPTIMA

  public function autocompleteFournisseurPrePaiement($infos,$reset=true,$count=false){
    if($reset){
      $this->q->reset();
    }


    $this->q->from("facture","id_commande","commande","id_commande")
        ->from("commande","id_commande","commande_ligne","id_commande")
        ->where("facture.id_commande",ATF::facture()->decryptId($infos["condition_value"]))
        ->addField("societe.id_societe","id_societe")
        ->addField("societe.societe","nom");
    if($count){
      $this->q->setCount();
      $return = $this->select_all();
    }else{
      $return = parent::autocomplete($infos,false);
    }

    return $return;
  }*/



  /**
  * Permet d'intégrer
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
  * @param array &$s La session
  * @param array $files $_FILES
  * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
  */
  public function importProspect(&$infos,&$s,$files=NULL) {
    $infos['display'] = true;
    $path = $files['file']['tmp_name'];


    if ($infos['id_owner']) $id_owner = ATF::user()->decryptId($infos['id_owner']);

    if ($infos['id_apporteur']) $id_apporteur = ATF::user()->decryptId($infos['id_apporteur']);

    if ($infos['id_prospection']) $id_prospection = ATF::contact()->decryptId($infos['id_prospection']);

    // Récupération des données du fichier temporaire
    $f = fopen($path,"r");

    // Vérification des colonnes
    $cols = fgetcsv($f, 1, ";");

    $societeInserted = $contactInserted = 0;

    ATF::db($this->db)->begin_transaction();
    $erreurs = array();

    $lineCompteur = 0;
    while (($data = fgetcsv($f, 10000, ";")) !== FALSE) {
      $lineCompteur++;
      if ($lineCompteur==1 || !$data[2]) continue;

      $data = array_map("utf8_encode",$data);

      // Gestion del'état :
      $etat = 'actif';
      /*switch ($data[24]) {
        case "Actif économiquement":
          $etat = 'actif';
        break;
        default:
          $etat = "inactif";
        break;
      }*/

      // Gestion del'effectif :
      switch ($data[29]) {
        case "Aucun salarié":
          $effectif = 1;
        break;
        case "3 à 5 salariés":
        case "6 à 9 salariés":
          $effectif = 10;
        break;
        case "10 à 19 salariés":
        case "20 à 49 salariés":
          $effectif = 50;
        break;
        case "50 à 99 salariés":
          $effectif = 100;
        break;
        case "100 à 199 salariés":
          $effectif = 500;
        break;
        default:
          $effectif = NULL;
        break;
      }


      $societe = array(
        "id_owner"=>$id_owner,
        "id_apporteur"=>$id_apporteur,
        "id_prospection"=>$id_prospection,
        "relation"=>$infos['relation'],
        "code_client"=>$data[0],
        "siret"=>$data[1],
        "societe"=>$data[2],
        "nom_commercial"=>$data[3],
        "adresse"=>$data[4],
        "adresse_2"=>$data[5],
        "cp"=>$data[6],
        "ville"=>$data[7],
        "adresse_3"=>$data[8],
        "siren"=>$data[10],
        "structure"=>$data[19],
        "tel"=>$data[21],
        "fax"=>$data[22],
        "etat"=>$etat,
        "capital"=>$data[26],
        "naf"=>$data[27],
        "activite"=>$data[28],
        "reference_tva"=>$data[33],
        "cs_score"=>$data[31],
        "cs_avis_credit"=>$data[34],
        "date_creation"=>date("Y-m-d",strtotime(str_replace("/","-",$data[23]))),
        "effectif"=>$effectif
      );

      if($data[12]) $societe["id_fournisseur"] = $data[12];

      $birthday = date("Y-m-d",strtotime(str_replace("/","-",$data[17])));
      $contact = array(
        "civilite"=>$data[14]?strtolower($data[14]):"m",
        "prenom"=>$data[15],
        "nom"=>$data[16],
        "anniversaire"=>$data[17]?$birthday:NULL,
        "fonction"=>$data[20],
      );


      try {
        $contact['id_societe'] = $this->i($societe);


        $societeInserted++;
        if(str_replace(" ", "", $contact["nom"])){
          $id_c = ATF::contact()->i($contact);

          $contactInserted++;
        }

      } catch (errorATF $e) {

        $msg = $e->getMessage();

        if (preg_match("/generic message : /",$msg)) {
          $tmp = json_decode(str_replace("generic message : ","",$msg),true);
          $msg = $tmp['text'];
        }

                if ($e->getErrno()==1062) {
                  if ($infos['ignore']) {
                      $warnings[$e->getErrno()." - Enregistrement(s) déjà existant(s)"] .= $lineCompteur.", ";
                  } else {
                      $erreurs[$e->getErrno()." - Enregistrement(s) déjà existant(s)"] .= $lineCompteur.", ";
                  }
                } else {
                    $erreurs[$e->getErrno()." - ".$msg] .= $lineCompteur.", ";
                }

      }


    }

    fclose($handle);

    if (!empty($erreurs)) {
      $return['errors'] = $erreurs;
      $return['success'] = false;
      ATF::db($this->db)->rollback_transaction();
    } else {
      $return['warnings'] = $warnings;
      $return['societeInserted'] = $societeInserted;
      $return['contactInserted'] = $contactInserted;
      $return['success'] = true;
      ATF::db($this->db)->commit_transaction();
    }

    return json_encode($return);
  }


  /**
  * Permet de connaitre l'investissement en cours d'un client
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer
  */
  public function getInfosInvestissement($id_societe) {
    $totalInvestissement = $totalRestant = 0;

    ATF::affaire()->q->reset()->where("affaire.id_societe",$id_societe)
                  ->where("affaire.etat","commande","OR")
                  ->where("affaire.etat","facture","OR");
    $affaires = ATF::affaire()->select_all();

    foreach ($affaires as $kaff => $vaff) {
      ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire",$vaff["affaire.id_affaire"])
                        ->addField("bon_de_commande.prix","prix")
                        ->addField("bon_de_commande.bon_de_commande","commande");
      $commandes = ATF::bon_de_commande()->select_all();
      $encours = 0;
      foreach ($commandes as $kcom => $vcom) {
        $totalInvestissement += $vcom["prix"];
        if($vcom["solde_ht"] < 0){  $encours += $vcom["solde_ht"];  }

      }

      ATF::facturation()->q->reset()->where("id_affaire",$vaff["affaire.id_affaire"])
                      ->addField("SUM(facturation.montant)","prix")
                      ->whereIsNull("facturation.id_facture");
      $facturations = ATF::facturation()->select_all();

      foreach ($facturations as $kfact => $vfact) { $totalRestant += $vfact["prix"]; }
    }
    return array("investissement"=>number_format($totalInvestissement,2,"," ," "), "restant"=>number_format($totalRestant+$encours,2,"," ," "));
  }


  /**
  * Permet de checker si un IBAN est correct
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer
  */
  public function checkIBAN($iban){
    $table_conversion = array("A"=>10,"B"=>11,"C"=>12,"D"=>13,"E"=>14,"F"=>15,"G"=>16,"H"=>17,"I"=>18,"J"=>19,"K"=>20,"L"=>21,"M"=>22,"N"=>23,"O"=>24,"P"=>25,"Q"=>26,"R"=>27,"S"=>28,"T"=>29,"U"=>30,"V"=>31,"W"=>32,"X"=>33,"Y"=>34,"Z"=>35);


    /*
    * Enlever les caractères indésirables (espaces, tirets)
    * Supprimer les 4 premiers caractères et les replacer à la fin du compte
    * Remplacer les lettres par des chiffres au moyen d'une table de conversion (A=10, B=11, C=12 etc.)
    * Diviser le nombre ainsi obtenu par 97.
    * Si le reste n'est pas égal à 1 l'IBAN est incorrect : Modulo de 97 égal à 1
    */
    if($iban){
      //Enlever les caractères indésirables (espaces, tirets)
      $iban = str_replace("-", "", $iban);
      $iban = str_replace(" ", "", $iban);


      //Supprimer les 4 premiers caractères et les replacer à la fin du compte
      $first = substr($iban, 0, 4);
      $iban = substr($iban, 4);

      $iban = $iban.$first;

      $char = "";

      //Remplacer les lettres par des chiffres au moyen d'une table de conversion (A=10, B=11, C=12 etc.)
      for($i=0;$i<strlen($iban); $i++){
        if(!is_numeric($iban[$i])){
          $char .= $table_conversion[$iban[$i]];
        }else{
          $char .= $iban[$i];
        }
      }

      //Diviser le nombre ainsi obtenu par 97
      if(bcmod($char , 97) != 1) throw new errorATF("IBAN incorrect", 500);

    }else{
      throw new errorATF("IBAN vide", 500);
    }
  }


  public function getUrlSign($id_affaire){
    $url = __SIGN_URL__."#!".ATF::$codename."?k=".$this->cryptId($id_affaire);
    return $url/*."&sref=".urlencode($url)*/;
  }
  public function _getUrlSign($get,$post){
    return $this->getUrlSign($get['id_affaire']);
  }
  /**
  * Appel Sell & Sign, verification de l'IBAN, envoi du mandat SEPA PDF
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer
  */
  public function _signAndGetPDF($post,$get){
    $tel  = $post["tel"];
    $bic  = $post["bic"];
    $iban = $post["iban"];
    if (strlen($post["id"])!=32) {
      throw new Exception('Identifiant non valide.', 500);
    }
    $id_affaire = $post["id"];

    $id_societe = ATF::affaire()->select($id_affaire,"id_societe");
    if (!$id_societe) {
      throw new Exception('Aucune information pour cet identifiant.', 500);
    }
    ATF::societe()->u(array("id_societe"=>$id_societe, "BIC"=>$bic , "IBAN"=>$iban));

    //Si il n'y a pas de num telephone sur la société, on enregistre ce numéro
    if(ATF::societe()->select($id_societe, "tel") === NULL) {
      ATF::societe()->u(array("id_societe"=>$id_societe, 'tel'=>$tel));
    }

    $societe = ATF::societe()->select($id_societe);
    $contact = ATF::contact()->select($societe["id_contact_signataire"]);

    $this->checkIBAN($iban);

    ATF::contact()->u(array("id_contact"=>$societe["id_contact_signataire"], "gsm"=>$tel));

    //On stocke les infos de signature sur l'affaire
    ATF::affaire()->u(array('id_affaire'=>$id_affaire,
                            'tel_signature'=> $tel,
                            'mail_signataire'=> $contact["email"],
                            'date_signature'=> date('Y-m-d H:i:s'),
                            'signataire'=> $contact["prenom"]." ".$contact["nom"].", ".$contact["fonction"]
                            )
                      );


    $pdf_mandat = ATF::pdf()->generic('mandatSellAndSign',$id_affaire,true);

    $return = array(
      "id_affaire"=>$this->decryptId($id_affaire),
      "civility"=>$contact["civilite"],
      "id_contact"=> $societe["id_contact_signataire"],
      "fonction"=> $contact["fonction"],
      "firstname"=>$contact["prenom"],
      "lastname"=>$contact["nom"],
      "address_1"=>$societe["adresse"],
      "address_2"=>$societe["adresse_2"]." ".$societe["adresse_3"],
      "postal_code"=>$societe["cp"],
      "city"=>$societe["ville"],
      "email"=>$contact["email"],
      "company_name"=>$societe["societe"],
      "ref"=>ATF::$codename.$societe["code_client"],
      "country"=>$societe["id_pays"],
      "cell_phone"=>$tel,
      "pdf_mandat"=> base64_encode($pdf_mandat) // base64
    );
    return $return;
  }

  /**
  * Appel Sell & Sign, retourne les infos du client à partir de l'id_affaire
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
  * @param array $post["id_affaire"]
  */
  public function _signGetInfosOnly($post){
    if (strlen($post["id"])!=32) {
      throw new Exception('Identifiant non valide.', 500);
    }
    $id_societe = ATF::affaire()->select($post["id"],"id_societe");
    if (!$id_societe) {
      throw new Exception('Aucune information pour cet identifiant.', 500);
    }
    $societe = ATF::societe()->select($id_societe);
    $contact = ATF::contact()->select($societe["id_contact_signataire"]);
    $return = array(
      "civility"=>$contact["civilite"],
      "firstname"=>$contact["prenom"],
      "lastname"=>$contact["nom"],
      "email"=>$contact["email"],
      "tel"=>$contact["gsm"],
      "company_name"=>$societe["societe"],
      "ref"=>ATF::$codename.$societe["code_client"],
      "IBAN"=>$societe["IBAN"],
      "BIC"=>$societe["BIC"]
    );
    return $return;
  }

  public function getCodeClient($site_associe, $prefixe = "TO"){
    //Recherche du max en base
    $this->q->reset()
      ->addField('MAX(SUBSTRING(code_client FROM -4))','max')
      ->addCondition('code_client',$prefixe.'%','OR',false,'LIKE');
    $result=$this->sa();

    if(isset($result[0]['max'])){
      $max = intval($result[0]['max'])+1;
    }else{
      $max = 1;
    }

    if($max<10){
      $ref.='000'.$max;
    }elseif($max<100){
      $ref.='00'.$max;
    }elseif($max<1000){
      $ref.='0'.$max;
    }elseif($max<10000){
      $ref.=$max;
    }else{
      throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
    }
    return $prefixe.$ref;

  }


  /**
   * Permet l'insertion du client TOSHIBA si la société n'existe pas, de l'affaire et du comité CreditSafe
   * @param  [type] $get  [description]
   * @param  [type] $post [description]
   * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
   * @return [type]       [description]
   */
  public function _sendDataToshiba($get, $post){


    ATF::$usr->set('id_user',16);
    ATF::$usr->set('id_agence',1);
    $email = $post["email"];
    $fournisseur = 6241;
    $data = self::getInfosFromCREDITSAFE($post);


    ATF::type_affaire()->q->reset()->where("type_affaire", "normal");
    $type_affaireNormal = ATF::type_affaire()->select_row();

    if($data){
      $gerants = $data["gerant"];

      if($data["cs_score"] == "Note non disponible") unset($data["cs_score"]);
      if($data["cs_avis_credit"] == "Limite de crédit non applicable") unset($data["cs_avis_credit"]);


      /*ATF::societe()->q->reset()->where("societe",ATF::db($this->db)->real_escape_string($data["societe"]),"AND")
                                  ->where("adresse",ATF::db($this->db)->real_escape_string($data["adresse"]));*/
      ATF::societe()->q->reset()->where("siret",ATF::db($this->db)->real_escape_string($data["siret"]));
      $res = ATF::societe()->select_row();

      try {

          if($res){

            $id_societe = $res["id_societe"];
            if(!$res["code_client"]){
              $code_client = $this->getCodeClient("toshiba");
              ATF::societe()->u(array("id_societe"=>$id_societe,
                                      "code_client"=>$code_client
                                     ));
            }

            if($res['adresse'] != $data["adresse"] || $res['cp'] != $data["cp"] || $res['ville'] != $data["ville"]){
                ATF::societe()->u(array("id_societe"=>$id_societe,
                                        "adresse"=>$data["adresse"],
                                        "cp"=>$data["cp"],
                                        "ville"=>$data["ville"]
                                     ));
            }


          } else {

            $code_client = $this->getCodeClient("toshiba");
            $data["code_client"]= $code_client;
            $data_soc = $data;

            $data_soc["id_apporteur"]   = 28531; //Apporteur d'affaire TOSHIBA
            $data_soc["id_fournisseur"] = $fournisseur; //Fournisseur ALSO


            unset($data_soc["nb_employe"],$data_soc["resultat_exploitation"],$data_soc["capitaux_propres"],$data_soc["dettes_financieres"],$data_soc["capital_social"], $data_soc["gerant"]);

            $id_societe = $this->insert(array("societe"=>$data_soc));

            $this->u(array("id_societe"=> $id_societe, "id_apporteur" => 28531, "id_fournisseur" => $fournisseur));

            $res['id_societe'] = $id_societe;

          }

          if($gerants){
            foreach ( $gerants as $key => $value) {
              ATF::contact()->q->reset()->where("LOWER(nom)", ATF::db($this->db)->real_escape_string(strtolower($value["nom"])),"AND")
                                        ->where("LOWER(prenom)", ATF::db($this->db)->real_escape_string(strtolower($value["prenom"])),"AND")
                                        ->where("id_societe", $id_societe,"AND");
              $gerant[$key] = $contact;
              $c = ATF::contact()->select_row();

              //Si le contact n'exite pas dans optima, on l'insert
              if(!$c){
                $contact = array( "nom"=>$value["nom"],
                                  "prenom"=>$value["prenom"],
                                  "fonction"=>$value["fonction"],
                                  "email"=>$email,
                                  "id_societe"=> $id_societe,
                                  "est_dirigeant"=>"oui"
                                );
                $gerant[$key] = $contact;
                $gerant[$key]["id_contact"] = ATF::contact()->insert( $contact );
              } else {
                //Sinon on le met à jour
                $gerant[$key] = array(  "nom"=>$c["nom"],
                                        "prenom"=>$c["prenom"],
                                        "fonction"=>$value["fonction"],
                                        "gsm"=>$c["gsm"],
                                        "email"=>$c["email"],
                                        "id_societe"=> $id_societe,
                                        "id_contact"=>$c["id_contact"]
                                      );
                ATF::contact()->u(array("id_contact"=>$c["id_contact"], "fonction"=>$value["fonction"], "est_dirigeant"=>"oui"));
              }
            }
          }else{
            //Si Credit Safe n'a retourné aucun dirigeant, on en cré un en attendant
            $contact = array( "nom"=>"GERANT",
                              "email"=>$email,
                              "id_societe"=> $id_societe
                          );
            $gerant[0] = $contact;
            $gerant[0]["id_contact"] = ATF::contact()->insert( $contact );
          }

          $pack = ATF::pack_produit()->select($post["id_pack_produit"]);

          $devis = array(
                  "id_societe" => $id_societe,
                  "type_contrat" => "lld",
                  "validite" => date("d-m-Y", strtotime("+1 month")),
                  "tva" => __TVA__,
                  "devis" => $pack["nom"],
                  "date" => date("d-m-Y"),
                  "type_devis" => "normal",
                  "id_contact" => $gerant[0]["id_contact"],
                  "prix_achat"=>0,
                  "id_type_affaire" => $type_affaireNormal["id_type_affaire"]);
          $values_devis =array();

          $montantLoyer = $duree = 0;

          $loyer = array();
          $produits = array();
          $produitsAfterTri = array();
          ATF::pack_produit_ligne()->q->reset()->where('id_pack_produit',$post["id_pack_produit"]);
          $pack_pro_ligne = ATF::pack_produit_ligne()->select_all();
          // tri dans l'ordre croissant
          usort($pack_pro_ligne, function ($item1, $item2) {
            // sensible à la casse ! [1,3,100,10] => [1,10,100,3]
            // return strcmp($item1['ordre'],$item2['ordre']);
            if ($item1['ordre'] ==$item2['ordre']) {
              return 0;
            }
            return ($item1['ordre'] < $item2['ordre']) ? -1 : 1;
          });

          // si une provenance est présente on récupère les infos de cette société
          if($post["provenance"]){
            // On récupère l'identifiant du revendeur à partir du ?lead
            $this->q->reset()
                    ->where("lead",$post['provenance'])
                    ->AndWhere('revendeur','oui');
            $revendeur = $this->select_row();

            // On récupère l'identifiant du partenaire à partir du ?lead
            $this->q->reset()
                    ->where("lead",$post['provenance']);
            if($partenaire = $this->select_row()){
              $devis["id_partenaire"] = $partenaire['id_societe'];
            }
          }

          // maintenant il faut appliquer cet ordre aux pack produits
          foreach ($pack_pro_ligne as $k => $v) {
            foreach ($post["produits"] as $key => $value) {
              if($key == $v['id_produit']){
                $produitsAfterTri[$key]= $value;
              }
            }
          }



          foreach ($produitsAfterTri as $key => $value) {
            if($value > 0){
              $produit = ATF::produit()->select($key);

              if($post["QteProduit"][$produit["id_produit"]]){
                $qte = $post["QteProduit"][$produit["id_produit"]];
              }else{
                $qte = $value*$post["selectQtePack"];
              }

              $loyer[0] = array(
                            "loyer__dot__loyer"=> $loyer[0]["loyer__dot__loyer"] + ($produit["loyer"] * $qte),
                            "loyer__dot__duree"=>$produit["duree"],
                            "loyer__dot__type"=>"engagement",
                            "loyer__dot__assurance"=>"",
                            "loyer__dot__frais_de_gestion"=>"",
                            "loyer__dot__frequence_loyer"=>"mois",
                            "loyer__dot__serenite"=>"",
                            "loyer__dot__maintenance"=>"",
                            "loyer__dot__hotline"=>"",
                            "loyer__dot__supervision"=>"",
                            "loyer__dot__support"=>"",
                            "loyer__dot__avec_option"=>"non"
                          );

                ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $post["id_pack_produit"])
                                                       ->where("id_produit", $produit["id_produit"]);
                $ligne = ATF::pack_produit_ligne()->select_row();

              if($revendeur){
                $fournisseur_produit_id = $revendeur['id_societe'];
                $fournisseur_produit = $revendeur['societe'];
              } else {
                // fournisseur par défaut
                //$fournisseur_produit = 'ALSO FRANCE';
                //$fournisseur_produit_id = "6241";

                $fournisseur_produit_id = "Fournisseur par defaut";
                $fournisseur_produit_id = $ligne["id_fournisseur"];
              }

              $visible = "oui";
              if($ligne["visible_sur_pdf"] === "non" || "visible" === "non") {
                $visible = "non";
              }


              $produits[] = array(
                                  "devis_ligne__dot__produit"=> $produit["produit"],
                                  "devis_ligne__dot__quantite"=>$qte,
                                  "devis_ligne__dot__type"=>"sans_objet",
                                  "devis_ligne__dot__ref"=>$produit["ref"],
                                  "devis_ligne__dot__prix_achat"=>$ligne["prix_achat"],
                                  "devis_ligne__dot__id_produit"=>$produit["id_produit"],
                                  "devis_ligne__dot__id_fournisseur"=>$fournisseur_produit,
                                  "devis_ligne__dot__visibilite_prix"=>"invisible",
                                  "devis_ligne__dot__date_achat"=>"",
                                  "devis_ligne__dot__commentaire"=>"",
                                  "devis_ligne__dot__neuf"=>"oui",
                                  "devis_ligne__dot__id_produit_fk"=>$produit["id_produit"],
                                  "devis_ligne__dot__id_fournisseur_fk"=>$fournisseur_produit_id,
                                  "devis_ligne__dot__visible"=>$visible
                                );

              $devis["prix_achat"] += ($ligne["prix_achat"] * $qte);

            }
          }
          $values_devis = array("loyer"=>json_encode($loyer), "produits"=>json_encode($produits));
          try {
             $id_devis = ATF::devis()->insert(array("devis"=>$devis, "values_devis"=>$values_devis));
          } catch (errorATF $e) {
            throw new errorATF($e ,500);
          }
          $devis = ATF::devis()->select($id_devis);

          switch ($post["provenance"]) {
            case 'd023ef3680189f828a53810e3eda0ecc':
              ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"], "site_associe"=>"toshiba","provenance"=>"toshiba"));
            break;

            case '7154b414c85f24ffefcefe53490c49bd':
              ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"], "site_associe"=>"toshiba","provenance"=>"la_poste"));
            break;

            default:
              ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"], "site_associe"=>"toshiba","provenance"=>"cleodis"));
            break;
          }
          ATF::affaire_etat()->insert(array(
                                        "id_affaire"=>$devis["id_affaire"],
                                        "etat"=>"reception_demande"
                                    ));

          $comite = array  (
                  "id_societe" => $id_societe,
                  "id_affaire" => $devis["id_affaire"],
                  "id_contact" => $gerant[0]["id_contact"],
                  "activite" => $data["activite"],
                  "id_refinanceur" => 4,
                  "date_creation" => $data["date_creation"],
                  "date_compte" => $data["lastaccountdate"],
                  "capitaux_propres" => $data["capitaux_propres"],
                  "note" => $data["cs_score"],
                  "dettes_financieres" => $data["dettes_financieres"],
                  "limite" => $data["cs_avis_credit"],
                  "ca" => $data["ca"],
                  "capital_social" => $data["capital_social"],
                  "resultat_exploitation" => $data["resultat_exploitation"],
                  "date" => date("d-m-Y"),
                  "description" => "Comite CreditSafe",
                  "suivi_notifie"=>array(0=>"")
              );



          $creation = new DateTime( $data["date_creation"] );
          $creation = $creation->format("Ymd");
          $past2Years = new DateTime( date("Y-m-d", strtotime("-2 years")) );
          $past2Years = $past2Years->format("Ymd");

          if($data["cs_score"] > 50 && $creation < $past2Years ){
            $comite["etat"] = "accepte";
            $comite["decisionComite"] = "Accepté automatiquement";
          }else{
            $comite["etat"] = "refuse";
            $comite["decisionComite"] = "Refusé automatiquement (Note < 50, ou ancienneté < 2ans";
          }

          $comite["reponse"] = date("Y-m-d");
          $comite["validite_accord"] = date("Y-m-d");



          try{
            ATF::comite()->insert(array("comite"=>$comite));
          }catch (errorATF $e) {
            throw new errorATF($e->getMessage() ,500);
          }


          if($comite["etat"]== "accepte"){
            //Création du comité CLEODIS
            $comite["description"] = "Comité CLEODIS";
            $comite["etat"] = "en_attente";
            $comite["reponse"] = NULL;
            $comite["validite_accord"] = NULL;
            ATF::comite()->insert(array("comite"=>$comite));

            return array("result"=>true,
                         "id_affaire"=> $devis["id_affaire"],
                         "id_crypt"=>ATF::affaire()->cryptId($devis["id_affaire"]),
                         "duree"=>$loyer[0]["loyer__dot__duree"],
                         "montant"=> $loyer[0]["loyer__dot__duree"] * $loyer[0]["loyer__dot__loyer"],
                         "loyer"=>$loyer[0]["loyer__dot__loyer"],
                         "siren"=>$data["siren"],
                         "gerants"=>$gerant,
                         "societe"=>ATF::societe()->select($id_societe),
                         "url_sign"=> $this->getUrlSign(ATF::affaire()->cryptId($devis["id_affaire"]))
                        );
          }
          return array("result"=>false ,
                       "societe"=>ATF::societe()->select($res['id_societe']));

      } catch (Exception $e) {

      }
    } else{
      throw new errorATF("erreurCS",404);
    }

  }
  /**
   * Permet de retourner les infos creditsafe de la societe
   * @param  [type] $get  [description]
   * @param  [type] $post [description]
   * @author Cyril Charlier <ccharlier@absystech.fr>
   * @return [type]       [description]
   */
  public function _infosCredisafePartenaire($get, $post){
    log::logger($post, "creditsafe");
    $utilisateur  = ATF::$usr->get("contact");
    $apporteur = $utilisateur["id_societe"];

    if($post["api"]) $api = true;
    if(!$post["langue"]) $post["langue"] = "FR";

    if(ATF::$codename == "cleodisbe"){
      $post["num_ident"] = $post["siret"];
    }
    $data = self::getInfosFromCREDITSAFE($post);

    if(!$data["societe"]){
      throw new errorATF("erreurCS",404);
    }

    log::logger($data, "creditsafe");

    if($data){
        $gerants = $data["gerant"];
        if($data["cs_score"] == "Note non disponible") unset($data["cs_score"]);
        if($data["cs_avis_credit"] == "Limite de crédit non applicable") unset($data["cs_avis_credit"]);
        /*ATF::societe()->q->reset()->where("societe",ATF::db($this->db)->real_escape_string($data["societe"]),"AND")
                                    ->where("adresse",ATF::db($this->db)->real_escape_string($data["adresse"]));*/
        if(ATF::$codename === "cleodisbe"){
          $data["num_ident"] = 'BE'.$post["num_ident"];

          ATF::societe()->q->reset()->where("num_ident",ATF::db($this->db)->real_escape_string($data["num_ident"]),"OR","siret")
                                    ->where("reference_tva",$data["reference_tva"],"OR","siret");
        }else{
          ATF::societe()->q->reset()->where("siret",ATF::db($this->db)->real_escape_string($data["siret"]));
        }

        $res = ATF::societe()->select_row();

        try {

            if($res){
                $id_societe = $res["id_societe"];

                if($res["langue"] !== $post["langue"]) $this->u(array("id_societe"=>$id_societe, "langue"=>$post["langue"]));

                if($res['adresse'] != $data["adresse"] || $res['cp'] != $data["cp"] || $res['ville'] != $data["ville"]){
                  ATF::societe()->u(array("id_societe"=>$id_societe,
                                          "adresse"=>$data["adresse"],
                                          "cp"=>$data["cp"],
                                          "ville"=>$data["ville"]
                                       ));
                }



            }else {
                log::logger("ICI societe inexistante" , "creditsafe");
                $data_soc = $data;

                if ($post['site_associe']=='boulangerpro') {
                  ATF::societe()->q->reset()->where("societe", "BOULANGER PRO", "AND", false, "LIKE");
                  $id_apporteur = ATF::societe()->select_cell();
                  $data_soc['id_apporteur'] = $id_apporteur;
                }

                $data_soc["langue"] = $post["langue"];

                unset($data_soc["nb_employe"],$data_soc["resultat_exploitation"],$data_soc["capitaux_propres"],$data_soc["dettes_financieres"],$data_soc["capital_social"], $data_soc["gerant"]);
                $id_societe = $this->insert(array("societe" => $data_soc));
                if($apporteur) $this->u(array("id_societe"=> $id_societe, "id_apporteur" => $apporteur, "id_fournisseur" => $apporteur));
                if($post["site_associe"]) $this->u(array("id_societe"=> $id_societe, "code_client" => $code_client));
            }


            if($gerants){
              foreach ( $gerants as $key => $value) {
                ATF::contact()->q->reset()->where("LOWER(nom)", ATF::db($this->db)->real_escape_string(strtolower($value["nom"])),"AND")
                                          ->where("LOWER(prenom)", ATF::db($this->db)->real_escape_string(strtolower($value["prenom"])),"AND")
                                          ->where("id_societe", $id_societe,"AND");
                $gerant[$key] = $contact;
                $c = ATF::contact()->select_row();


                //Si le contact n'exite pas dans optima, on l'insert
                if(!$c){
                  $contact = array( "nom"=>$value["nom"],
                                    "prenom"=>$value["prenom"],
                                    "fonction"=>$value["fonction"],
                                    "id_societe"=> $id_societe,
                                    "est_dirigeant"=>"oui"
                                  );
                  $gerant[$key] = $contact;
                  $gerant[$key]["id_contact"] = ATF::contact()->insert( $contact );
                } else {
                  //Sinon on le met à jour
                  $gerant[$key] = array(  "nom"=>$c["nom"],
                                          "prenom"=>$c["prenom"],
                                          "fonction"=>$value["fonction"],
                                          "gsm"=>$c["gsm"],
                                          "id_societe"=> $id_societe,
                                          "id_contact"=>$c["id_contact"]
                                        );
                  ATF::contact()->u(array("id_contact"=>$c["id_contact"], "fonction"=>$value["fonction"], "est_dirigeant"=>"oui"));
                }
              }
            }else{
              //Si Credit Safe n'a retourné aucun dirigeant, on en cré un en attendant
              $contact = array( "nom"=>"GERANT",
                                "id_societe"=> $id_societe
                            );
              $gerant[0] = $contact;
              $gerant[0]["id_contact"] = ATF::contact()->insert( $contact );
            }

            return array("result"=>true ,
                "societe"=>ATF::societe()->select($id_societe),
                "gerants"=>$gerant
            );
        } catch (errorSQL $e) {
            log::logger("====================================================================", "creditsafe");
            log::logger("ERREUR SQL : Déclenchée dans la fonction ".__CLASS__."/".__FUNCTION__, "creditsafe");
            log::logger($e->getMessage(), "creditsafe");
            log::logger($data, "creditsafe");
            throw $e;
        } catch (ATFerror $e) {
            log::logger("====================================================================", "creditsafe");
            log::logger("ERREUR ATF : Déclenchée dans la fonction ".__CLASS__."/".__FUNCTION__, "creditsafe");
            log::logger($data, "creditsafe");
            throw new errorATF("erreurCS inside",500);
        }
    } else{
        log::logger("====================================================================", "creditsafe");
        log::logger("ERREUR : Aucune donnée dans DATA dans la fonction ".__CLASS__."/".__FUNCTION__, "creditsafe");
        log::logger($data, "creditsafe");
        log::logger($post, "creditsafe");

        throw new errorATF("erreurCS : Il n'y a aucun retour de créditsafe",500);
    }

  }
  public function _comiteCleodis ($get, $post){
    $decision = $post['action'] == "valider" ? "accepte" : "refuse"; // on set la decision en fonction de l'action envoyé
    $decisionAffaireEtat = $post['action'] == "valider" ? "comite_cleodis_valide" : "comite_cleodis_refuse"; // pareil pour la timeline

    // au cas ou il y aurait un changement de format d'id transmis
    $id_affaire =  strlen($post["id_affaire"]) === 32 ?  ATF::affaire()->decryptId($post["id_affaire"]) : $post['id_affaire'];
    ATF::comite()->q->reset() // on récupére le comite cleodis concerné
      ->where("id_affaire",$id_affaire, 'AND')
      ->where("description", "Comité CLEODIS", 'AND')
      ->where("etat", "en_attente");
    $comite = ATF::comite()->select_row();

    try {
      // on met à jour l'état
      ATF::comite()->u(array("id_comite"=>$comite['id_comite'], "etat"=>$decision, "decisionComite"=>"Accepté manuellement"));
      ATF::affaire()->u(array("id_affaire"=>$id_affaire, "etat_comite"=>$decision));
      // ainsi que la table affaire etat pour la timeline
      ATF::affaire_etat()->insert(array(
          "id_affaire"=>$id_affaire,
          "etat"=> $decisionAffaireEtat,
          "id_user"=>ATF::$usr->get('id_user')
      ));
      if ($decision === "accepte")
        $this->_createContratToshiba(false, array("id_affaire"=>$id_affaire));
      return true;
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function _comiteSGEF ($get, $post){
    ATF::comite()->q->reset()->where("id_affaire",$post["id_affaire"]);
    $comiteCS = ATF::comite()->select_row();

    $comite = $comiteCS;

    $comite["id_refinanceur"] = "7c414d4d3e01579eebf055c12c5f7ccd";
    $comite["description"] = "Comite SGEF";
    $comite["suivi_notifie"] = array(0=>"");

    if($post["resultat"] == "true"){
      $comite["etat"] = "accepte";
      $comite["decisionComite"] = "Accepté automatiquement";
    }elseif($post["resultat"] === "etude") {
      $comite["etat"] = "en_attente";
      $comite["decisionComite"] = "Comité à l'étude";
    }else{
      $comite["etat"] = "refuse";
      $comite["decisionComite"] = "Refusé automatiquement";
    }

    unset($comite["id_comite"]);
    try{
       ATF::comite()->insert(array("comite"=>$comite));
    }catch (errorATF $e) {
      throw new errorATF($e->getMessage() ,500);
    }
  }

  public function _createContratToshiba($get , $post){

    ATF::$usr->set('id_user',16);
    ATF::$usr->set('id_agence',1);

    $id_affaire = ATF::affaire()->decryptId($post["id_affaire"]);


    ATF::devis()->q->reset()->where("id_affaire", $id_affaire);
    $devis = ATF::devis()->select_row();


    ATF::devis_ligne()->q->reset()->where("id_devis", $devis["id_devis"]);
    $lignes = ATF::devis_ligne()->select_all();

    $commande =array(
            "commande" => $devis["devis"],
            "type" => "prelevement",
            "id_societe" => $devis["id_societe"],
            "date" => date("d-m-Y"),
            "id_affaire" => $id_affaire,
            "id_devis" => $devis["id_devis"],
            "prix_achat" => $devis["prix_achat"]
        );

    $produits = array();

    foreach ($lignes as $key => $value) {
      $fournisseur = ATF::societe()->select($value["id_fournisseur"]);

      $produits[] = array(
          "commande_ligne__dot__produit"=>$value["produit"],
          "commande_ligne__dot__quantite"=>$value["quantite"],
          "commande_ligne__dot__ref"=>$value["ref"],
          "commande_ligne__dot__id_fournisseur"=>$fournisseur["societe"],
          "commande_ligne__dot__id_fournisseur_fk"=>$fournisseur["id_societe"],
          "commande_ligne__dot__prix_achat"=>$value["prix_achat"],
          "commande_ligne__dot__id_produit"=>$value["produit"],
          "commande_ligne__dot__id_produit_fk"=>$value["id_produit"],
          "commande_ligne__dot__visible"=>$value["visible"]

        );
    }
    $values_commande = array( "produits" => json_encode($produits));

    ATF::commande()->insert(array("commande"=>$commande , "values_commande"=>$values_commande));
  }


  /**
   * Permet de mettre à jour le contact signataire / ou créer un nouveau contact pour etre signataire
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   * @param  [type] $get  [description]
   * @param  [type] $post [description]
   */
  public function _updateGerant($get, $post) {

    $id_societe = ATF::affaire()->select($post["id_affaire"], "id_societe");

    if($post["id_contact"] === "0" || $post["id_contact"] === 0){


      ATF::contact()->q->reset()->where("id_societe", $id_societe)
                              ->where("contact.nom", "GERANT");
      $contact = ATF::contact()->select_row();
      if($contact){
        ATF::contact()->u(array("id_contact"=>$contact["id_contact"],
                                "nom"=>$post["nom_gerant"],
                                "prenom"=>$post["prenom_gerant"],
                                "gsm"=>$post["tel"],
                                "email"=>$post["email_gerant"]
                        ));
        ATF::societe()->u(array("id_societe"=>$id_societe, "id_contact_signataire"=>$contact["id_contact"]));
      } else {
        $new = array(
                        "nom"=>$post["nom_gerant"],
                        "prenom"=>$post["prenom_gerant"],
                        "gsm"=>$post["phone_gerant"],
                        "email"=>$post["email_gerant"],
                        "fonction"=>$post["fonction_gerant"],
                        "id_societe"=>$id_societe
                );

        $id_contact = ATF::contact()->i($new);
        ATF::societe()->u(array("id_societe"=>$id_societe, "id_contact_signataire"=>$id_contact,"id_contact_facturation"=>$id_contact));
      }
    }else{
      ATF::contact()->u(array("id_contact"=>$post["id_contact"],
                              "gsm"=>$post["tel"],
                              "fax"=>$post["fax"],
                              "email"=>$post["email"]
                      ));
      ATF::societe()->u(array("id_societe"=>$id_societe, "id_contact_signataire"=>$post["id_contact"]));
    }
  }

  /**
  * Retourne le préfixe utilisé, peut être surchargé
  * @author Yann GAUTHERON <ygautheron@absystech.fr>
  * @return string $prefix
  */
  public function create_ref_prefix($s){
    return $s['id_famille'] == 9 ? "P" : "S";
  }

  /**
  * Construit la référence de l'entité (spécifique à chaque Optima)
  * @author Jérémie Gwiazdowski <jgw@absystech.fr>
  * @author Yann GAUTHERON <ygautheron@absystech.fr>
  * @param array $s La session
  * @return string $ref la référence de l'entité
  */
  public function create_ref(&$s){
    $ref=$this->create_ref_prefix($s);
    $id_agence = ATF::$usr->get('id_agence');
    if (!$id_agence) {
      ATF::agence()->q->reset()->addField('id_agence')->addOrder('id_agence','asc')->setLimit(1);
      $id_agence = ATF::agence()->select_cell();
    }
    //Recherche agence + date
    $ref.=strtoupper(
      substr(
        ATF::agence()->select($id_agence,'agence'),0,2)
      ).date('ym');

    //Recherche du maximum
    $max=$this->get_max_ref($ref);
    if($max<10){
      $ref.='000'.$max;
    }elseif($max<100){
      $ref.='00'.$max;
    }elseif($max<1000){
      $ref.='0'.$max;
    }elseif($max<10000){
      $ref.=$max;
    }else{
      throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
    }

    return $ref;

  }

  /**
   * Recherche pour une société si il y a des contrats en contentieux et met à jour le champs mauvais payeur et contentieux depuis sur sa fiche
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   * @param  int $id_societe ID de la société
   */
  public function checkMauvaisPayeur($id_societe){
    log::logger("##########################" , "mauvais_payeur");
    log::logger("### ID SOCIETE -> ".$id_societe , "mauvais_payeur");

    $contentieux_depuis = null;

    ATF::commande()->q->reset()->where("id_societe", $id_societe, "AND")
                               ->where("etat", "%contentieux%", "AND", false, "LIKE");

    if($contentieux = ATF::commande()->sa()){
      ATF::societe()->u(array("id_societe"=> $id_societe, "mauvais_payeur"=> "oui"));

      ATF::facture()->q->reset()->where("id_societe", $id_societe)
                                ->where("etat", "impayee", "AND", "impayee")
                                ->whereIsNotNull("date_rejet", "AND", "impayee");
      $date_max_impayee = NULL;

      foreach (ATF::facture()->sa() as $key => $value) {
        if($value["date_periode_debut"]){
          if($date_max_impayee == NULL || str_replace("-", "", $date_max_impayee) > str_replace("-", "", $value["date_periode_debut"])){
            $date_max_impayee = $value["date_periode_debut"];
          }
        }else{
          if($date_max_impayee == NULL || str_replace("-", "", $date_max_impayee) > str_replace("-", "", $value["date"])){
            $date_max_impayee = $value["date"];
          }
        }
      }

      log::logger("###Max date => ".$date_max_impayee , "mauvais_payeur");
      log::logger("###Max date => ".date("Ym", strtotime($date_max_impayee)) , "mauvais_payeur");




      if((date("Ym") - date("Ym", strtotime($date_max_impayee))) <= 1 ){
        log::logger("### 1 mois ou moins" , "mauvais_payeur");
        $contentieux_depuis = "1_mois";
      }elseif((date("Ym") - date("Ym", strtotime($date_max_impayee))) <= 2 ){
        log::logger("### Entre 1 et 2 mois" , "mauvais_payeur");
        $contentieux_depuis = "2_mois";
      }else{
        log::logger("### Plus de 3 mois" , "mauvais_payeur");
        $contentieux_depuis = "plus_3_mois";
      }

      log::logger("### ".date("Ym") , "mauvais_payeur");
      log::logger("### ".date("Ym", strtotime($date_max_impayee)) , "mauvais_payeur");
      log::logger("### ".(date("Ym") - date("Ym", strtotime($date_max_impayee))), "mauvais_payeur");

      ATF::societe()->u(array("id_societe"=> $id_societe, "mauvais_payeur"=> "oui", "contentieux_depuis"=> $contentieux_depuis));
    }else{
      log::logger("### pas de contentieux" , "mauvais_payeur");
      ATF::societe()->u(array("id_societe"=> $id_societe, "mauvais_payeur"=> "non", "contentieux_depuis"=> $contentieux_depuis));
    }
  }


  public function demande_creation_compte_espace_client ($client, $url_front_espace_client, $id_commande=null){
    try{
      if(!$client){
        // On va chercher les infos clients à partir du contrat
        if(!$id_commande) throw new errorATF("ID Commande manquante");

        $id_commande = ATF::commande()->decryptId($id_commande);
        $id_client = ATF::commande()->select($id_commande , "id_societe");
        $dataSociete = ATF::societe()->select($id_client);

        if(ATF::societe()->select($id_client,"id_famille") == 9){
          $client = array("id_societe" => $dataSociete["id_societe"],
                          "nom" => $dataSociete["particulier_nom"],
                          "prenom" => $dataSociete["particulier_prenom"],
                          "email" => $dataSociete["particulier_email"],
                          "ref" => ATF::commande()->select($id_commande , "ref"),
                          "affaire" => ATF::commande()->select($id_commande , "id_affaire")
                      );
        } else {
          if ($dataSociete["id_contact_signataire"]) {
            $signataire = ATF::contact()->select($dataSociete["id_contact_signataire"]);
            if ($signataire["email"]) {
              $client = array("id_societe" =>$dataSociete["id_societe"],
                              "nom" => $signataire["nom"],
                              "prenom" => $signataire["prenom"],
                              "email" => $signataire["email"],
                              "ref" => ATF::commande()->select($id_commande , "ref"),
                              "affaire" => ATF::commande()->select($id_commande , "id_affaire")
                          );
            }
          } else if ($dataSociete['email']) {
            $client = array("id_societe" => $dataSociete["id_societe"],
            "email" => $dataSociete["email"],
            "ref" => ATF::commande()->select($id_commande , "ref"),
            "affaire" => ATF::commande()->select($id_commande , "id_affaire")
            );
          }
        }
      }


      if(!$url_front_espace_client) {
        try {
          $url_front_espace_client = ATF::espace_client_conseiller()->getUrlFront();
        } catch (errorATF $e) {
          throw $e;
        }
      }

      if (ATF::$codename === "bdomplus") {
        ATF::societe()->q->reset()->where("siret", "52933929300043");
        $partenaire = ATF::societe()->select_row();
        $colors = array(
          "dominant" => "#FD5300",
          "footer" => "#161C5F",
          "links" => "#161C5F",
          "titles" => "#161C5F"
        );
      } else {
        ATF::societe()->q->reset()->where("siret", "45307981600055");
        $partenaire = ATF::societe()->select_row();
        $colors = array(
          "dominant" => "#94c030",
          "footer" => "#94c030",
          "links" => "#23527c",
          "titles" => "#23527c"
        );
      }

      // Envoyer le mail uniquement si on a un email dans $client
      if ($client['email']) {
        $infos_mail = array(
          "recipient" => $client["email"],
          "objet" => "Création de votre compte espace client",
          "template" => "demande_creation_compte_espace_client",
          "client" => $client,
          "lien_espace_client" => $url_front_espace_client . "/register",
          "colors" => $colors,
          "partenaire"=> $partenaire
        );

        $mail = new mail( $infos_mail );
        if($mail->send()){
            ATF::societe()->u(array("id_societe"=> $client["id_societe"] , "date_envoi_mail_creation_compte" => date("Y-m-d")));
            ATF::suivi()->insert(array(
              "id_societe"=>$client["id_societe"],
              "type"=>"email",
              "texte"=>"Envoi du mail de création de compte client à ".$client["email"],
            ));
        }else{
            log::logger("------------------------------------------------------", "error_mail_creation_compte");
            log::logger("Probleme lors de l'envoi du mail de création de compte", "error_mail_creation_compte");
            log::logger($mail, "error_mail_creation_compte");
            ATF::suivi()->insert(array(
              "id_societe"=>$client["id_societe"],
              "type"=>"email",
              "texte"=>"Erreur lors de l'envoi du mail de création de compte à " . $client['email']. " (voir les logs error_mail_creation_compte)",
            ));
        }

      }
    } catch(errorATF $e){
        throw $e;
    }
  }


};

class societe_cleodisbe extends societe_cleodis {
  public function __construct() {
    parent::__construct();
    $this->table = "societe";

    unset($this->colonnes['panel']['delai_rav'],
        $this->colonnes['panel']['delai_fournisseur'],
        $this->colonnes['panel']['deploiement']);


    $this->fieldstructure();
  }
};

class societe_cap extends societe_cleodis {
  public function __construct() {
    parent::__construct();
    $this->table = "societe";

    $this->colonnes['primary']["code_regroupement"] ="";

    unset($this->colonnes['panel']['delai_rav'],
        $this->colonnes['panel']['delai_fournisseur'],
        $this->colonnes['panel']['deploiement'],
        $this->colonnes['primary']["id_assistante"]);


    $this->fieldstructure();
    $this->addPrivilege("envoiCourrier");
  }

  public function envoiCourrier($infos) {
    ATF::_s("ids",explode("|",$infos['ids']));
    ATF::_s("societe_source",$infos['societe_source']);
    return true;
  }

};


class societe_midas extends societe_cleodis {
  public function __construct() {
    parent::__construct();
    $this->table = "societe";

    $this->colonnes['fields_column'] = array(
      'societe.code_client'
      ,'societe.societe'
      ,'societe.nom_commercial'
      ,'societe.id_owner'
      ,'actif'=>array("custom"=>true,"renderer"=>"actif")
    );
    $this->foreign_key['id_owner'] =  "user";

    $this->onglets = array(
      'contact'=>array('opened'=>true)
      ,'affaire'=>array('opened'=>true)
      ,'suivi'=>array('opened'=>true)
    );

    $this->fieldstructure();

    unset($this->files["facturation_rib"]);
  }

  /** On affiche que les sociétés midas
  * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
  */
  public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
    //si plusieurs commandes, a partir du moment où il y en a une en loy ou pro, retourner 'ok'
    ATF::commande()->q->reset()
            ->addCondition("commande.etat","mis_loyer","OR")
            ->addCondition("commande.etat","prolongation","OR")
            ->setToString();
    $subquery=ATF::commande()->sa();

    $this->q->addField("IF(com.etat='mis_loyer' OR com.etat='prolongation'
                ,'ok'
                ,'mort'
              )","actif")
        ->addJointure("societe","id_societe","com","id_societe","com",NULL,NULL,NULL,"left",false,$subquery)
        ->addCondition("societe.code_client","M%",NULL,false,"LIKE")->addCondition("societe.divers_3","Midas")
        ->addCondition("societe.etat","actif")
        ->addGroup("societe.id_societe");
    if(!$this->q->view['tri']['champs'])$this->q->addOrder("societe.code_client","DESC");
    return parent::select_all($order_by,$asc,$page,$count);
  }

};



class societe_bdomplus extends societe_cleodis {

  public function getCodeClient($site_associe, $prefixe = "TO"){
    //Recherche du max en base
    $this->q->reset()
      ->addField('MAX(SUBSTRING(code_client FROM -8))','max')
      ->addCondition('code_client',$prefixe.'%','OR',false,'LIKE');
    $result=$this->sa();

    if(isset($result[0]['max'])){
      $max = intval($result[0]['max'])+1;
    }else{
      $max = 1;
    }

    if($max<10){
      $ref.='0000000'.$max;
    }elseif($max<100){
      $ref.='000000'.$max;
    }elseif($max<1000){
      $ref.='00000'.$max;
    }elseif($max<10000){
      $ref.='0000'.$max;
    }elseif($max<100000){
      $ref.='0000'.$max;
    }elseif($max<1000000){
      $ref.='00'.$max;
    }elseif($max<10000000){
      $ref.='0'.$max;
    }elseif($max<100000000){
      $ref.=$max;
    }else{
      throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
    }
    return $prefixe.$ref;

  }

};

class societe_boulanger extends societe_cleodis { };


class societe_assets extends societe_cleodis {

  /**
  * Construit la référence de l'entité (spécifique à chaque Optima)
  * @author Jérémie Gwiazdowski <jgw@absystech.fr>
  * @author Yann GAUTHERON <ygautheron@absystech.fr>
  * @param array $s La session
  * @return string $ref la référence de l'entité
  */
  public function create_ref(&$s){

    $ref = "DI";

    //Recherche du maximum
    $max=$this->get_max_ref($ref);

    if($max<10){
      $ref.='00000'.$max;
    }elseif($max<100){
      $ref.='0000'.$max;
    }elseif($max<1000){
      $ref.='000'.$max;
    }elseif($max<10000){
      $ref.='00'.$max;
    }elseif($max<100000){
      $ref.='0'.$max;
    }else{
      throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
    }
    return $ref;

  }


};

class societe_go_abonnement extends societe_cleodis {

  /**
  * Construit la référence de l'entité (spécifique à chaque Optima)
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $s La session
  * @return string $ref la référence de l'entité
  */
  public function create_ref(&$s){

    $ref = "GO";

    //Recherche du maximum
    $max=$this->get_max_ref($ref);

    if($max<10){
      $ref.='00000'.$max;
    }elseif($max<100){
      $ref.='0000'.$max;
    }elseif($max<1000){
      $ref.='000'.$max;
    }elseif($max<10000){
      $ref.='00'.$max;
    }elseif($max<100000){
      $ref.='0'.$max;
    }else{
      throw new errorATF(ATF::$usr->trans('ref_too_high'),80853);
    }
    return $ref;

  }

};
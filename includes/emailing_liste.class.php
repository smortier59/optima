<?
/** 
* Classe emailing_liste, gère les listes de diffusion
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
*/
class emailing_liste extends emailing {

	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes['fields_column'] = array(	
			'emailing_liste.emailing_liste'
			,'emailing_liste.description'
			,'emailing_liste.sollicitation'
			,'emailing_liste.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
			,'emailing_liste.tracking'=>array("width"=>120,"align"=>"center")
			,'emailing_liste.last_tracking'=>array("width"=>100,"align"=>"center")
			,'emailing_liste.erreur'=>array("width"=>120,"align"=>"center")
		);		
		$this->colonnes['primary'] = array(
			"emailing_liste"
			,"description"
			,"etat"								
		);

		$this->colonnes['panel']['infosSPM'] = array(
			"sollicitation"
			,"tracking"
			,"last_tracking"
			,"erreur"
		);
		$this->panels['infosSPM'] = array("visible"=>true);

		$this->colonnes['bloquees']['insert'] = 
		$this->colonnes['bloquees']['update'] = 	array(	
			"etat"
			,"sollicitation"
			,"tracking"
			,"last_tracking"
			,"erreur"
		);
												
		$this->fieldstructure();
		
		$this->onglets = array('emailing_job','emailing_liste_contact');
		$this->formExt=true; 
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Liste_de_diffusion";
		$this->addPrivilege("getNodesToFill");
	}
	
    public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {

        $this->infoCollapse($infos);
        if ($infos['selNodes']) {
            $selNodes = explode(",",$infos['selNodes']);
            foreach ($selNodes as $k=>$i) {
                $t = explode("_",$i);
                $nodes[$t[0]][] = $t[1];
            }
        }
        unset($infos['selNodes']);
        // insertion de la liste
        try {
            $ctDoublon=0;
            ATF::db($this->db)->begin_transaction();
            $id_el = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
            // Insertion des contact via un filtre
            if ($nodes['source']) {
                foreach ($nodes['source'] as $k=>$i) {
                    foreach (ATF::emailing_contact()->ss("id_emailing_source",$i) as $k_=>$i_) {
                        try {
                            ATF::emailing_liste_contact()->insert(array(
                                "id_emailing_liste"=>$id_el
                                ,"id_emailing_contact"=>$i_["id_emailing_contact"]
                            ));
                        } catch (errorSQL $e) {
                            if ($e->getErrno()==1062) {
                                $ctDoublon++;
                            }
                        }
                    }
                }
            }
            
            if ($nodes['filtre']) {
                foreach ($nodes['filtre'] as $k=>$i) {
                    ATF::emailing_contact()->q->reset()->setFilter(ATF::filtre_optima()->select($i,'options'));
                    foreach (ATF::emailing_contact()->sa() as $k_=>$i_) {
                        try {
                            ATF::emailing_liste_contact()->insert(array(
                                "id_emailing_liste"=>$id_el
                                ,"id_emailing_contact"=>$i_["id_emailing_contact"]
                            ));
                        } catch (error $e) {
                            if ($e->getErrno()==1062) {
                                $ctDoublon++;
                            }
                        }
                    }
                }
            }
            
            if ($nodes['liste']) {
                foreach ($nodes['liste'] as $k=>$i) {
                    ATF::emailing_liste_contact()->q->reset()->where("id_emailing_liste",$i);
                    foreach (ATF::emailing_liste_contact()->sa() as $k_=>$i_) {
                        try {
                            ATF::emailing_liste_contact()->insert(array(
                                "id_emailing_liste"=>$id_el
                                ,"id_emailing_contact"=>$i_["id_emailing_contact"]
                            ));
                        } catch (error $e) {
                            if ($e->getErrno()==1062) {
                                $ctDoublon++;
                            }
                        }
                    }
                }
            }
        } catch (error $e) {
            ATF::db($this->db)->rollback_transaction(true);
            throw $e;
        }
        ATF::db($this->db)->commit_transaction();
        ATF::$msg->addNotice($ctDoublon." " .ATF::$usr->trans("doublonNonEnregistre",$this->table));
        return $id_el;
    }
        
        
    public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
        $this->infoCollapse($infos);
        if ($infos['selNodes']) {
            $selNodes = explode(",",$infos['selNodes']);
            foreach ($selNodes as $k=>$i) {
                $t = explode("_",$i);
                $nodes[$t[0]][] = $t[1];
            }
        }
        unset($infos['selNodes']);
        // modification de la liste
        $id_el = $this->decryptId($infos['id_emailing_liste']);
        try {
            $ctDoublon=0;
            ATF::db($this->db)->begin_transaction();
            parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
            // Insertion des contact via un filtre
            if ($nodes['source']) {
                foreach ($nodes['source'] as $k=>$i) {
                    foreach (ATF::emailing_contact()->ss("id_emailing_source",$i) as $k_=>$i_) {
                        try {
                            ATF::emailing_liste_contact()->insert(array(
                                "id_emailing_liste"=>$id_el
                                ,"id_emailing_contact"=>$i_["id_emailing_contact"]
                            ));
                        } catch (errorSQL $e) {
                            if ($e->getErrno()==1062) {
                                $ctDoublon++;
                            }
                        }
                    }
                }
            }
            
            if ($nodes['filtre']) {
                foreach ($nodes['filtre'] as $k=>$i) {
                    ATF::emailing_contact()->q->reset()->setFilter(ATF::filtre_optima()->select($i,'options'));
                    foreach (ATF::emailing_contact()->sa() as $k_=>$i_) {
                        try {
                            ATF::emailing_liste_contact()->insert(array(
                                "id_emailing_liste"=>$id_el
                                ,"id_emailing_contact"=>$i_["id_emailing_contact"]
                            ));
                        } catch (error $e) {
                            if ($e->getErrno()==1062) {
                                $ctDoublon++;
                            }
                        }
                    }
                }
            }
            
            if ($nodes['liste']) {
                foreach ($nodes['liste'] as $k=>$i) {
                    ATF::emailing_liste_contact()->q->reset()->where("id_emailing_liste",$i);
                    foreach (ATF::emailing_liste_contact()->sa() as $k_=>$i_) {
                        try {
                            ATF::emailing_liste_contact()->insert(array(
                                "id_emailing_liste"=>$id_el
                                ,"id_emailing_contact"=>$i_["id_emailing_contact"]
                            ));
                        } catch (error $e) {
                            if ($e->getErrno()==1062) {
                                $ctDoublon++;
                            }
                        }
                    }
                }
            }
        } catch (error $e) {
            ATF::db($this->db)->rollback_transaction(true);
            throw $e;
        }
        ATF::db($this->db)->commit_transaction();
        ATF::$msg->addNotice($ctDoublon." " .ATF::$usr->trans("doublonNonEnregistre",$this->table));
        return $id_el;
    }
    
	/** 
	* Retourne false car impossibilité
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-04-2011
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		return true;
		$sel = $this->select($id);

		if (__BYPASS__===true) return true;
		
		//Si on la liste est fermée
		if ($sel['etat']=='close') {
			throw new error(ATF::$usr->trans("impossible_modifer_liste_close",$this->table),8822);
		}
		return true;
	}
	
	/** 
	* Retourne false car impossibilité
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 05-04-2011
	* @return boolean
	*/
	public function getNodesToFill(&$infos,&$s,$files=NULL,&$cadre_refreshed){
		if (ATF::_g("vide")) {
			return "[]";	
		}
		$node = array(
			"source"=>array(
				"text"=>"Sources"
				,"cls"=>"folder"
				,"id"=>"source"
			),
			"filtre"=>array(
				"text"=>"Filtres de contact d'emailing"
				,"cls"=>"folder"
				,"id"=>"filtre"
			),
			"liste"=>array(
				"text"=>"Liste de diffusion existante"
				,"cls"=>"folder"
				,"id"=>"liste"
			)
		);
		
		ATF::emailing_source()->q->reset();
		if ($source = ATF::emailing_source()->select_all()) {
			foreach ($source as $src) {
				ATF::emailing_contact()->q->reset()
													->where('emailing_contact.id_emailing_source',$src["emailing_source.id_emailing_source"])
													->Where("opt_in","oui")
													->WhereIsNotNull("email")
													->setCountOnly();
				$count = ATF::emailing_contact()->sa();
				$r['source'][]=array(
					"text"=>ATF::emailing_source()->nom($src["emailing_source.id_emailing_source"])." (".$count.")"
					,"id"=>"source_".$src["emailing_source.id_emailing_source"]
					,"cls"=>"file"
					,"adapter"=>"source"
					,"leaf"=>true
				);
			}
		}
		
		ATF::filtre_optima()->q->reset()
										->where('id_module',120)
										->where('type','public');
		if ($filtre = ATF::filtre_optima()->sa()) {
			foreach ($filtre as $f) {
				ATF::emailing_contact()->q->reset()->setFilter(ATF::filtre_optima()->select($f["id_filtre_optima"],'options'))->setCountOnly();
				$r['filtre'][]=array(
					"text"=>$f["filtre_optima"]." (".ATF::emailing_contact()->sa().")"
					,"id"=>"filtre_".$f["id_filtre_optima"]
					,"cls"=>"file"
					,"adapter"=>"filtre"
					,"leaf"=>true
				);
			}
		}
		
		ATF::emailing_liste()->q->reset();
		if ($liste = ATF::emailing_liste()->sa()) {
			foreach ($liste as $l) {
				$r['liste'][]=array(
					"text"=>$l["emailing_liste"]." (".ATF::emailing_liste_contact()->nbMail($l["id_emailing_liste"]).")"
					,"id"=>"liste_".$l["id_emailing_liste"]
					,"cls"=>"file"
					,"adapter"=>"liste"
					,"leaf"=>true
				);
			}
		}
		
		if ($r['source']) {
			$node['source']['children'] = $r['source'];
		} else {
			$node['source']['children'] = array();
		}
		if ($r['filtre']) {
			$node['filtre']['children'] = $r['filtre'];
		} else {
			$node['filtre']['children'] = array();
		}
		if ($r['liste']) {
			$node['liste']['children'] = $r['liste'];
		} else {
			$node['liste']['children'] = array();
		}
		
		$infos['display'] = true;
		return "[".json_encode($node['source']).",".json_encode($node['filtre']).",".json_encode($node['liste'])."]";
	}
};
?>

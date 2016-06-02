ATF.changeCmu = function(el,val,lastVal) {		
	if(val == "oui"){
		Ext.ComponentMgr.get('comboadherent[tpn]').show();
	}else{
		Ext.ComponentMgr.get('comboadherent[tpn]').hide();
		Ext.ComponentMgr.get('comboadherent[tpn]').setValue("");
	}
}

ATF.getAge = function(el,val,lastVal) {
	var res = el.value.split("-");
	var d = new Date();
	d = d.getFullYear()  - res[2];
	var tranche = "NC";	
	
	if(d < 18){
		tranche = "A : -18 ans";
	}else if(d < 26){
		tranche = "B : 18-25 ans";
	}else if(d < 41){
		tranche = "C : 26-40 ans";
	}else if(d < 61){
		tranche = "D : 41-60 ans";
	}else if(d > 60){
		tranche = "E : + 60 ans";
	}	
	Ext.ComponentMgr.get("comboadherent[tranche_age]").setValue(tranche);
	
}

ATF.civilite = function(el,val,lastVal) {	
	var parent = document.getElementById("x-form-el-adherent[nom_jeune_fille]").parentNode;
	if(val == "Mr"){
		parent.hide();
		Ext.ComponentMgr.get('comboadherent[sexe]').setValue("M");		
	}else{
		parent.show();	
		Ext.ComponentMgr.get('comboadherent[sexe]').setValue("F");
	}	
}


ATF.updateRessource = function(el,val,lastVal) {
	var sommeAdherent = 0.00;
	var sommeConjoint = 0.00;
	
	if(val == ""){
		val = 0;
	}
	
	{$element_adherent = Array(
		"adherent_ressource[salaire]","adherent_ressource[pension]", 
		"adherent_ressource[indemnite]", "adherent_ressource[ijss]",
		"adherent_ressource[assedic]","adherent_ressource[rmi]",
		"adherent_ressource[primes]","adherent_ressource[retraite]",
		"adherent_ressource[rsa]","adherent_ressource[autre]",
		"adherent_ressource[alloc_fam]","adherent_ressource[alloc_log]",
		"adherent_ressource[apl]", "adherent_ressource[alloc_parent_iso]",
		"adherent_ressource[alloc_adulte_handi]","adherent_ressource[pension_ali]",
		"adherent_ressource[presta_comp]", "adherent_ressource[autre_revenu]"		
	)}
	
	{$element_conjoint = Array(
		"adherent_ressource[salaire_conjoint]","adherent_ressource[pension_conjoint]", 
		"adherent_ressource[indemnite_conjoint]", "adherent_ressource[ijss_conjoint]",
		"adherent_ressource[assedic_conjoint]","adherent_ressource[rmi_conjoint]",
		"adherent_ressource[primes_conjoint]","adherent_ressource[retraite_conjoint]",
		"adherent_ressource[rsa_conjoint]","adherent_ressource[autre_conjoint]",
		"adherent_ressource[alloc_fam_conjoint]","adherent_ressource[alloc_log_conjoint]",
		"adherent_ressource[apl_conjoint]", "adherent_ressource[alloc_parent_iso_conjoint]",
		"adherent_ressource[alloc_adulte_handi_conjoint]","adherent_ressource[pension_ali_conjoint]",
		"adherent_ressource[presta_comp_conjoint]", "adherent_ressource[autre_revenu_conjoint]"		
	)}
		
	
	/****************  RESSOURCES ADHERENTS  ************************/
			
	{foreach from=$element_adherent item=item}
		if(el.name == "{$item}"){ 
			sommeAdherent = sommeAdherent + parseFloat(val); 
			if(val != 0){
				Ext.ComponentMgr.get("{$item}").setValue(val);
			}else{
				Ext.ComponentMgr.get("{$item}").setValue("");
			}
			
		}else{ 
			if(Ext.ComponentMgr.get("{$item}").value){
				if(Ext.ComponentMgr.get("{$item}").value != ""){
					sommeAdherent = sommeAdherent + parseFloat(Ext.ComponentMgr.get("{$item}").value); 
				}
			}
		}
	{/foreach}
	
	/****************  RESSOURCES CONJOINT  ************************/	
	{foreach from=$element_conjoint item=item}
		if(el.name == "{$item}"){ 
			sommeConjoint = sommeConjoint + parseFloat(val); 
			if(val != 0){
				Ext.ComponentMgr.get("{$item}").setValue(val);
			}else{
				Ext.ComponentMgr.get("{$item}").setValue("");
			}
		}else{ 
			if(Ext.ComponentMgr.get("{$item}").value){
				if(Ext.ComponentMgr.get("{$item}").value != ""){
					sommeConjoint = sommeConjoint + parseFloat(Ext.ComponentMgr.get("{$item}").value); 
				}
			}
		}
	{/foreach}
		
	Ext.ComponentMgr.get("adherent_ressource[total_mensuel]").setValue(sommeAdherent);
	Ext.ComponentMgr.get("adherent_ressource[total_mensuel_conjoint]").setValue(sommeConjoint);
	
	sommeAdherent = sommeAdherent*12;
	sommeConjoint = sommeConjoint*12;
	Ext.ComponentMgr.get("adherent_ressource[total_annuel]").setValue(sommeAdherent);
	Ext.ComponentMgr.get("adherent_ressource[total_conjoint]").setValue(sommeConjoint);		
}



ATF.updateCharge = function(el,val,lastVal) {
		
	var sommeAdherent = 0.00;
	var sommeConjoint = 0.00;
	var sommeImpaye = 0.00;
	
	if(val == ""){
		val = 0;
	}
	
	{$charge_adherent = Array(
		"adherent_charge[impot]", "adherent_charge[taxe_fonciere]", "adherent_charge[taxe_habitation]",					
		"adherent_charge[redevance]", "adherent_charge[indus]",	"adherent_charge[loyer]",	
		"adherent_charge[remb_pret_immo]", "adherent_charge[electricite]",	"adherent_charge[gaz]",	
		"adherent_charge[autre_chauffage]",	"adherent_charge[eau]",	"adherent_charge[assu_logement]","adherent_charge[internet]",		
		"adherent_charge[autre_habi]", "adherent_charge[assu_auto]", "adherent_charge[entretien_carbu]", "adherent_charge[abo_tec]", 
		"adherent_charge[autre_dep]", "adherent_charge[frais_gestion]", "adherent_charge[acces_compte]", "adherent_charge[mutuelle]",
		"adherent_charge[mobile]","adherent_charge[pel]", "adherent_charge[assurance_vie]", "adherent_charge[livret_epargne]",
		"adherent_charge[contrat_obseque]", "adherent_charge[autre_banque]", "adherent_charge[pension_ali]",
		"adherent_charge[courses]", "adherent_charge[habillement]", "adherent_charge[transports]", "adherent_charge[cantine]",
		"adherent_charge[soins]","adherent_charge[cigarette]", "adherent_charge[presse]"	
	)}
	
	{$charge_conjoint = Array(
		"adherent_charge[impot_conjoint]", "adherent_charge[taxe_fonciere_conjoint]", "adherent_charge[taxe_habitation_conjoint]",					
		"adherent_charge[redevance_conjoint]", "adherent_charge[indus_conjoint]",	"adherent_charge[loyer_conjoint]",	
		"adherent_charge[remb_pret_immo_conjoint]", "adherent_charge[electricite_conjoint]",	"adherent_charge[gaz_conjoint]",	
		"adherent_charge[autre_chauffage_conjoint]",	"adherent_charge[eau_conjoint]",	"adherent_charge[assu_logement_conjoint]","adherent_charge[internet_conjoint]",		
		"adherent_charge[autre_habi_conjoint]", "adherent_charge[assu_auto_conjoint]", "adherent_charge[entretien_carbu_conjoint]", "adherent_charge[abo_tec_conjoint]", 
		"adherent_charge[autre_dep_conjoint]", "adherent_charge[frais_gestion_conjoint]", "adherent_charge[acces_compte_conjoint]", "adherent_charge[mutuelle_conjoint]",
		"adherent_charge[mobile_conjoint]","adherent_charge[pel_conjoint]", "adherent_charge[assurance_vie_conjoint]", "adherent_charge[livret_epargne_conjoint]",
		"adherent_charge[contrat_obseque_conjoint]", "adherent_charge[autre_banque_conjoint]", "adherent_charge[pension_ali_conjoint]",
		"adherent_charge[courses_conjoint]", "adherent_charge[habillement_conjoint]", "adherent_charge[transports_conjoint]", "adherent_charge[cantine_conjoint]",
		"adherent_charge[soins_conjoint]","adherent_charge[cigarette_conjoint]", "adherent_charge[presse_conjoint]"	
	)}
	
	{$charge_charge = Array(
		"adherent_charge[impot_charge]", "adherent_charge[taxe_fonciere_charge]", "adherent_charge[taxe_habitation_charge]", "adherent_charge[amende]",					
		"adherent_charge[redevance_charge]", "adherent_charge[indus_charge]",	"adherent_charge[loyer_charge]",	
		"adherent_charge[remb_pret_immo_charge]", "adherent_charge[electricite_charge]",	"adherent_charge[gaz_charge]",	
		"adherent_charge[autre_chauffage_charge]",	"adherent_charge[eau_charge]",	"adherent_charge[assu_logement_charge]","adherent_charge[internet_charge]",		
		"adherent_charge[autre_habi_charge]", "adherent_charge[assu_auto_charge]", "adherent_charge[entretien_carbu_charge]", "adherent_charge[abo_tec_charge]", 
		"adherent_charge[autre_dep_charge]", "adherent_charge[frais_gestion_charge]", "adherent_charge[acces_compte_charge]", "adherent_charge[mutuelle_charge]",
		"adherent_charge[mobile_charge]","adherent_charge[pel_charge]", "adherent_charge[assurance_vie_charge]", "adherent_charge[livret_epargne_charge]",
		"adherent_charge[contrat_obseque_charge]", "adherent_charge[autre_banque_charge]", "adherent_charge[pension_ali_charge]",
		"adherent_charge[courses_charge]", "adherent_charge[habillement_charge]", "adherent_charge[transports_charge]", "adherent_charge[cantine_charge]",
		"adherent_charge[soins_charge]","adherent_charge[cigarette_charge]", "adherent_charge[presse_charge]"	
	)}	
	
	/****************  RESSOURCES ADHERENTS  ************************/
			
	{foreach from=$charge_adherent item=item}
		if(el.name == "{$item}"){ 
			sommeAdherent = sommeAdherent + parseFloat(val); 
			if(val != 0){
				Ext.ComponentMgr.get("{$item}").setValue(val);
			}else{
				Ext.ComponentMgr.get("{$item}").setValue("");
			}
			
		}else{ 
			if(Ext.ComponentMgr.get("{$item}").value){
				if(Ext.ComponentMgr.get("{$item}").value != ""){
					sommeAdherent = sommeAdherent + parseFloat(Ext.ComponentMgr.get("{$item}").value); 
				}
			}
		}
	{/foreach}
	
	/****************  RESSOURCES CONJOINT  ************************/	
	{foreach from=$charge_conjoint item=item}
		if(el.name == "{$item}"){ 
			sommeConjoint = sommeConjoint + parseFloat(val); 
			if(val != 0){
				Ext.ComponentMgr.get("{$item}").setValue(val);
			}else{
				Ext.ComponentMgr.get("{$item}").setValue("");
			}
		}else{ 
			if(Ext.ComponentMgr.get("{$item}").value){
				if(Ext.ComponentMgr.get("{$item}").value != ""){
					sommeConjoint = sommeConjoint + parseFloat(Ext.ComponentMgr.get("{$item}").value); 
				}
			}
		}
	{/foreach}
	
	/****************  RESSOURCES IMPAYE  ************************/	
	{foreach from=$charge_charge item=item}
		if(el.name == "{$item}"){ 
			sommeImpaye = sommeImpaye + parseFloat(val); 
			if(val != 0){
				Ext.ComponentMgr.get("{$item}").setValue(val);
			}else{
				Ext.ComponentMgr.get("{$item}").setValue("");
			}
		}else{ 
			if(Ext.ComponentMgr.get("{$item}").value){
				if(Ext.ComponentMgr.get("{$item}").value != ""){
					sommeImpaye = sommeImpaye + parseFloat(Ext.ComponentMgr.get("{$item}").value); 
				}
			}
		}
	{/foreach}		


	Ext.ComponentMgr.get("adherent_charge[total_mensuel]").setValue(sommeAdherent);
	Ext.ComponentMgr.get("adherent_charge[total_mensuel_conjoint]").setValue(sommeConjoint);
	Ext.ComponentMgr.get("adherent_charge[total_impaye]").setValue(sommeImpaye);
	
	sommeAdherent = sommeAdherent*12;
	sommeConjoint = sommeConjoint*12;
	Ext.ComponentMgr.get("adherent_charge[total_annuel]").setValue(sommeAdherent);
	Ext.ComponentMgr.get("adherent_charge[total_conjoint]").setValue(sommeConjoint);	
}
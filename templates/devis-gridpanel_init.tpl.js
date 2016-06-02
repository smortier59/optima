{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"devis.etat")}

{* Pdf Devis *}
ATF.renderer.devisPdf=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.data.devis__dot__etat!='bloque' || '{ATF::$usr->get(id_profil)}'=='1') {
			if (filetype) {
				var id = record.data[table+'__dot__id_'+table];
				return '<a href="'+table+'-select-'+field+'-'+id+'.dl" alt="'+ATF.usr.trans("popup_download",table)+'">'+
					'<img src="http://static.absystech.net/images/icones/'+filetype+'.png" class="icone" />'+
					'</a>';
			} else {
				return '<img src="http://static.absystech.net/images/icones/warning.png" />';
			}
		}
	}
};

ATF.renderer.comiteExpand=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.data.devis__dot__etat!='perdu') {
			var id = record.data[table+'__dot__id_'+table];
			return '<a href="comite-insert.html,id_devis='+id+'" onclick="ATF.goTo(\'comite-insert.html,id_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};

{* Bouton devis-vers-commande *}
{if ATF::$usr->privilege('commande','insert')}
	ATF.renderer.devisExpand=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.devis__dot__etat=="attente") {
				return '<a href="commande-insert.html,id_devis='+id+'" onclick="ATF.goTo(\'commande-insert.html,id_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
				{if ATF::$usr->get("id_profil")==1}
					} else if (record.data.devis__dot__etat=="bloque") {
						return '<a href="javascript:;" onclick="ATF.tpl2div(\'devis,valider.ajax\',\'id_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/valid.png" height="16" width="16" alt="" /></a>';
				{/if}
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};
{/if}


ATF.renderer.CreerFacture=function(table,field) {	
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		return '<a href="facture-insert.html,id_devis='+id+'" onclick="ATF.goTo(\'facture-insert.html,id_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
	}
};



{if ATF::$usr->privilege('devis','update')}
	

	{* Bouton devis-perdu *}
	ATF.renderer.devisPerdu=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.devis__dot__etat=="attente"  || record.data.devis__dot__etat=="bloque") {
				return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',perdu.ajax\',\'id_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};


	{* Bouton devis-perdu *}
		ATF.renderer.devisAnnule=function(table,field) {
			return function(filetype, meta, record, rowIndex, colIndex, store) {
				var id = record.data[table+'__dot__id_'+table];
				if (record.data.devis__dot__etat=="attente"  || record.data.devis__dot__etat=="bloque") {
					return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',annule.ajax\',\'id_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
				} else {
					return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
				}
			}
		};
	
{/if}
{/strip}
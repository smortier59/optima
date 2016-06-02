{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"formation_devis.etat")}

{* Pdf formation_devis *}
ATF.renderer.formation_devisPdf=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.data.formation_devis__dot__etat!='bloque' || '{ATF::$usr->get(id_profil)}'=='1') {
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

{* Bouton formation_devis-vers-formation_commande *}
{if ATF::$usr->privilege('formation_commande','insert')}
	ATF.renderer.formation_devisExpand=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.formation_devis__dot__etat=="attente") {
				return '<a href="formation_commande-insert.html,id_formation_devis='+id+'" onclick="ATF.goTo(\'formation_commande-insert.html,id_formation_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
				{if ATF::$usr->get("id_profil")==1}
					} else if (record.data.formation_devis__dot__etat=="bloque") {
						return '<a href="javascript:;" onclick="ATF.tpl2div(\'formation_devis,valider.ajax\',\'id_formation_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/valid.png" height="16" width="16" alt="" /></a>';
				{/if}
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};
{/if}

{if ATF::$usr->privilege('formation_devis','update')}
	{* Bouton formation_devis-perdu *}
	ATF.renderer.formation_devisPerdu=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.formation_devis__dot__etat=="attente"  || record.data.formation_devis__dot__etat=="bloque") {
				return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',perdu.ajax\',\'id_formation_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};

	{* Bouton formation_devis-perdu *}
	ATF.renderer.formation_devisAnnule=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.formation_devis__dot__etat=="attente"  || record.data.formation_devis__dot__etat=="bloque") {
				return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',annule.ajax\',\'id_formation_devis='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};
{/if}
{/strip}
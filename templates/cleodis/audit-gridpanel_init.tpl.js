{if ATF::$usr->privilege('mandat','insert')}
	ATF.renderer.auditExpand=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var id = record.data[table+'__dot__id_'+table];
			if (record.data.audit__dot__etat=="attente") {
				return '<a href="mandat-insert.html,id_audit='+id+'" onclick="ATF.goTo(\'mandat-insert.html,id_audit='+id+'\'); return false;"><img src="{ATF::$staticserver}images/icones/expand.png" height="16" width="16" alt="" /></a>';
			} else {
				return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
		}
	};
{/if}


ATF.renderer.auditPerdu=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		if (record.data.audit__dot__etat=="attente"  || record.data.audit__dot__etat=="bloque") {
			return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\''+table+',perdu.ajax\',\'id_audit='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};
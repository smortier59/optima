{strip}

ATF.renderer.sendTeamviewer=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		if (record.data.contact__dot__email !== null ) {
			return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\'contact,sendMailTeamViewer.ajax\',\'id_contact='+id+'\');"><img src="{ATF::$staticserver}images/teamviewer.png" height="16" width="16" alt="" /></a>';
		}else{
			return '';
		}
	}
};

{/strip}
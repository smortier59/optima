{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowCmd")}

ATF.renderer.pdfMission=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_mission_fk'];
		var html = "";

		html += '<a href="attestationSecurite-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" /> Att.Sécu';
		html += '</a><br /><hr>';

		html += '<a href="attestationEmploi-'+id+'.pdf" target="_blank">';
		html += '<img src="{ATF::$staticserver}images/icones/pdf.png" /> Att.Emploi';
		html += '</a><br /><hr>';

		return '<div id="'+idDiv+'">'+html+'</div>';
	}
};

ATF.renderer.actionsMissionligne=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsMissionLigne = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var etat = record.data[table+'__dot__etat'];
			
			{* En attente, on peut tout faire *}
			if (etat=="en_attente") {
				var allowValid = true;
				var allowCancel = true;		
			{* Valide, on ne peut pas valider encore une fois	 *}	
			} else if (etat=="valide") {
				var allowValid = false;
				var allowCancel = true;				
			{* Canceled, on ne peut pas cancel encore une fois. *}
			} else if (etat=="annule") {
				var allowValid = true;
				var allowCancel = false;				
			} else {
				var allowValid = false;
				var allowCancel = false;				
			}

			var btnValid = {
				xtype:'button',
				id:"btnValid"+id,
				buttonText: '',
				buttonOnly: true,
				iconCls: 'valid',
				cls:'floatLeft',
				disabled:!allowValid,
				tooltip: '{ATF::$usr->trans("valid_personnel",$current_class->table)}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax("mission_ligne,valid.ajax,id="+id,null,{ onComplete : function() {
								store.reload();
							} });
							return false;
						}
					}
				}
			};

			var btnCancel = {
				xtype:'button',
				id:"btnCancel"+id,
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCancel',
				cls:'floatLeft',
				disabled:!allowCancel,
				tooltip: '{ATF::$usr->trans("cancel_personnel",$current_class->table)}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax("mission_ligne,cancel.ajax,id="+id,null,{ onComplete : function() {
								store.reload();
							} });
							return false;
						}
					}
				}
			};

			
			(function(){
				var params = {
					renderTo: idDivActionsMissionLigne,
					items:[btnValid,btnCancel]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
	
			
			return '<div class="left" id="'+idDivActionsMissionLigne+'"></div>';
		}
	}
};

{/strip}
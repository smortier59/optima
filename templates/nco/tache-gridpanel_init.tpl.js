{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowValid")}

ATF.renderer.actionsTaches=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {

		var idDivActionsTaches = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var allowValid = record.data['allowValid'];
		var btnValid = {
			xtype:'button',
			id:"valid",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'valid',
			disabled:!allowValid,
			tooltip: '{ATF::$usr->trans("valider")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					if(confirm('{ATF::$usr->trans(creer_tache,suivi)}')){
						ATF.ajax(
							'tache,valid.ajax'
							,'id_tache='+id
							,{ onComplete: function (obj) { 

								ATF.goTo('tache-insert.html,id_societe='+record.data[table+'__dot__id_societe_fk']);
							} }
						);
					}else{	
						ATF.ajax('tache,valid.ajax','id_tache='+id);
						store.reload();
					}
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDivActionsTaches,
				layout:'fit',
				items:[btnValid]
				
			};
			var p = new Ext.Container(params);
		}).defer(25);

		
		return '<div class="left" id="'+idDivActionsTaches+'"></div>';
			
	}
};

{if ATF::$usr->privilege('tache','insert')}
	ATF.renderer.relanceTache=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
	
			var idDivActionsTachesRelance = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var allowValid = record.data['allowValid'];
			var test='';
	
			if (record.data['allowValid']) {

				var btnRelance = {
					xtype:'button',
					id:"fouet",
					iconCls: 'btnFouet',
					buttonText: '',
					buttonOnly: true,
					disabled:!allowValid,
					tooltip: '{ATF::$usr->trans("relancer")}',
					tooltipType:'title',
					listeners: {
						'click': function(fb, v){
							if(confirm('{ATF::$usr->trans(relance_tache,tache)}')){
								ATF.ajax(
									'tache,relance.ajax'
									,'id_tache='+id
								);
							}
						}
					}
				};
				
				(function(){
					var params = {
						renderTo: idDivActionsTachesRelance,
						layout:'fit',
						items:[btnRelance]
						
					};
					var p = new Ext.Container(params);
				}).defer(25);
			}
			return '<div class="left" id="'+idDivActionsTachesRelance+'"></div>';
				
		}

	};
{/if}

{/strip}
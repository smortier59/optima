{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"id_hotline")}

ATF.renderer.actionsODM=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDiv = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var id_hotline = record.data[table+'__dot__id_hotline_fk'];
			var etat = record.data[table+'__dot__etat'];
			
			var btnRetourInter = {
				xtype:'button',
				id:"RetourInter",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnExpand',
				cls:'floatLeft',
				disabled:etat!="en_cours",
				tooltip: '{ATF::$usr->trans(valid_intervention,ordre_de_mission)|escape:javascript}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo("#hotline_interaction-insert.html,id_hotline="+id_hotline+"&id_ordre_de_mission="+id);
							return false;
						}
					}
				}
			};
			
			(function(){
				var params = {
					renderTo: idDiv,
					items:[btnRetourInter]
				};
				var p = new Ext.Container(params);
			}).defer(25);
	
			return '<div class="left" id="'+idDiv+'"></div>';
		}
	}
};

{/strip}
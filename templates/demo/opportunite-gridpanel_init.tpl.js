{strip}
{* Ajout du champ nécessaire pour ce renderer *}

ATF.renderer.toAffaire=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivViewCommand = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			
			if(record.data["opportunite__dot__etat"] == "en_cours"){				
				var btnAffaire = {
					xtype:'button',
					id:"btnAffaire",
					buttonText: '',
					buttonOnly: true,
					iconCls: 'btnAffaire',
					tooltip: '{ATF::$usr->trans("Passer en affaire")}',
					tooltipType:'title',
					listeners: {
						'click': function(fb, v){
							ATF.ajax(table+',toAffaire.ajax','id_opportunite='+id); 
							store.reload();						
						}
					}
				};
			}else{
				var btnAffaire = {}
			}
			
			
		
			
			(function(){
				var params = {
					renderTo: idDivViewCommand,
					items:[btnAffaire]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);

			
			return '<div class="left" id="'+idDivViewCommand+'"></div>';
		}
	}
};
{/strip}
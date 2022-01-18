{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowCF")}
{util::push($fieldsKeys,"allowLivraison")}
{util::push($fieldsKeys,"allowFacture")}
{util::push($fieldsKeys,"allowCancel")}
{util::push($fieldsKeys,"allowCheckFacture")}
{util::push($fieldsKeys,"id_affaire")}



ATF.renderer.actionsCmd=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsCmd = Ext.id();
			
			var id = record.data[table+'__dot__id_'+table];
			var idAffaire = record.data['id_affaire'];
			var allowCF = record.data['allowCF'];
			var allowLivraison = record.data['allowLivraison'];
			var allowFacture = record.data['allowFacture'];
			var allowCheckFacture = record.data['allowCheckFacture'];
			var allowCancel = record.data['allowCancel'];
					
			var btnCF = {
				xtype:'button',
				id:"btnCommandeFournisseur",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCommandeFournisseur',
				cls:'floatLeft',
				disabled:!allowCF,
				tooltip: '{ATF::$usr->trans("creerCommandeFournisseur")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo("bon_de_commande-insert.html,id_commande="+id);
							return false;
						}
					}
				}
			};
			var btnLivraison = {
				xtype:'button',
				id:"btnLivraison",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnLivraison',
				cls:'floatLeft',
				disabled:!allowLivraison,
				tooltip: '{ATF::$usr->trans("creerLivraison")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo('livraison-insert.html,id_commande='+id+'&id_affaire='+idAffaire); 
							return false;
						}
					}
				}
			};
			var btnFacture = {
				xtype:'button',
				id:"btnFacture",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnFacture',
				cls:'floatLeft',
				disabled:!allowFacture,
				tooltip: '{ATF::$usr->trans("creerFacture")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo('facture-insert.html,id_commande='+id); 
							return false;
						}
					}
				}
			};		
			ATF.log(allowCheckFacture);	
			ATF.log(record.data);	
			var btnCheckFacturee = {
				xtype:'button',
				id:"btnCheckFacturee",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'valid',
				cls:'floatLeft',
				disabled:!allowCheckFacture,
				tooltip: '{ATF::$usr->trans("declarerFacturee")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('commande,setInfos.ajax','id_commande='+id+'&etat=facturee');
							store.reload();
						}
					}
				}
			};
			var btnCancel = {
				xtype:'button',
				id:"btnCancel",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCancel',
				disabled:!allowCancel,
				tooltip: '{ATF::$usr->trans("annuler")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax(table+',annulee.ajax','id_commande='+id); 
							store.reload();
						}
					}
				}
			};
			
		
			
			(function(){
				var params = {
					renderTo: idDivActionsCmd,
					items:[btnCF,btnLivraison,btnFacture,btnCheckFacturee,btnCancel]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
	
			
			return '<div class="left" id="'+idDivActionsCmd+'"></div>';
		}
	}
};
{/strip}
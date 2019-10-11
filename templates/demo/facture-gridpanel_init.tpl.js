{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowRelance")}
{util::push($fieldsKeys,"allowPDFRelance")}
{util::push($fieldsKeys,"allowSolde")}
{util::push($fieldsKeys,"allowFactureInteret")}

ATF.renderer.actionsFacture=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsFacture = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var allowRelance = record.data['allowRelance'];
			var allowPDFRelance = record.data['allowPDFRelance'];
			var allowSolde = record.data['allowSolde'];
			var allowFactureInteret = record.data['allowFactureInteret'];
	
			var btnPDFRelance = {
				xtype:'button',
				id:"PDFrelance",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnPDF',
				cls:'floatLeft',
				disabled:!allowPDFRelance,
				tooltip: '{ATF::$usr->trans("visionnerRelance")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						window.open("relance-"+id+".pdf");
					}
				}
			};
	
			var btnRelance = {
				xtype:'button',
				id:"relance",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnRelance',
				cls:'floatLeft',
				disabled:!allowRelance,
				tooltip: '{ATF::$usr->trans("creerRelance")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('relance,generate.ajax','id_facture='+id);
							store.reload();
						}
					}
				}
			};
	
			var btnSolde = {
				xtype:'button',
				id:"solde",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnPaiementCreate',
				cls:'floatLeft',
				disabled:!allowSolde,
				tooltip: '{ATF::$usr->trans("creerPaiement")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v) {
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo('facture_paiement-insert.html,id_facture='+id+'&montant='+record.data['solde']);
							return false;
						}
					}
				}
			};
	
			(function(){
				var params = {
					renderTo: idDivActionsFacture,
					items:[btnPDFRelance,btnRelance,btnSolde]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
	
			
			return '<div class="left" id="'+idDivActionsFacture+'"></div>';
		}
	}
};

{/strip}
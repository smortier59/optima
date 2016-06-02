{strip}
{* Ajout du champ nécessaire pour ce renderer *}
/* Pour les Factures Frounisseurs */
{util::push($fieldsKeys,"factureFournisseurAllow")}
/* Pour les Factures Parc */
{util::push($fieldsKeys,"parcInsertionAllow")}

{if ATF::$usr->privilege('facture_fournisseur','insert')}
	ATF.renderer.expandToFactureFournisseur=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var idDiv = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var html = "";
			/* Prolongation Expand */
			if (record.data.factureFournisseurAllow) {
				html += '<a href="#facture_fournisseur-insert.html,id_bon_de_commande='+id+'">';
				html += '<img src="{ATF::$staticserver}images/module/16/facture_fournisseur.png" />';
				html += '</a>';
			} else {
				html += '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
			return '<div class="center" id="'+idDiv+'">'+html+'</div>';
		}
	};
{/if}

{if ATF::$usr->privilege('parc','insert')}
	ATF.renderer.parcInsertion=function(table,field) {
		return function(filetype, meta, record, rowIndex, colIndex, store) {
			var idDiv = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var html = "";
			/* Prolongation Expand */
			if (record.data.parcInsertionAllow) {
				html += '<a href="#parc-insert.html,id_bon_de_commande='+id+'">';
				html += '<img src="{ATF::$staticserver}images/module/16/parc.png" />';
				html += '</a>';
			} else {
				html += '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
			}
			return '<div class="center" id="'+idDiv+'">'+html+'</div>';
		}
	};
{/if}


{/strip}
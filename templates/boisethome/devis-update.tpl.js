var getLotNextId = function() {
	if (!window.pk_lot) window.pk_lot=0;
	window.pk_lot++;
	return 'n'+window.pk_lot;
};
var getLot = function() {
	return window.current_pk_lot;
};
var setLot = function(idx) {
	window.current_pk_lot = -1;

	var grid_produits = Ext.ComponentMgr.get('devis[produits]');
	if (grid_produits) {

		/* D'abord on vire les couleurs en surbrillance */
		var grid = Ext.ComponentMgr.get('devis[lots]');
		var store = grid.getStore();
		var records = store.getRange();
		for (var i = 0; i < records.length; i++) {
			$(grid.getView().getRow(i)).css('backgroundColor','');
		}

		if (idx>-1) {
			Ext.ComponentMgr.get('insertBtnProduit').setDisabled(false);

			/* Couleur en surbrillance */
			var grid = Ext.ComponentMgr.get('devis[lots]');
			$(grid.getView().getRow(idx)).css('backgroundColor','#FF9999');

			var rec = grid.store.getAt(idx);
			var label = rec.data.devis_lot__dot__libelle;
			grid_produits.setTitle('Produits du lot sélectionné : '+label);

			/* le pk_lot courant */
			window.current_pk_lot = rec.data.devis_lot__dot__id_devis_lot;

			/* On désélectionne un produit */
			setProduit(-1);

			/* Aucun produit sélectionné donc on cache tous les articles */
			var grid_article = Ext.ComponentMgr.get('devis[articles]');
			var store_article = grid_article.getStore();
			var records_article = store_article.getRange();
			for (var i = 0; i < records_article.length; i++) {
				$(grid_article.getView().getRow(i)).hide();
			}

			/* Afficher uniquement les lignes qui ont devis_lot_produit__dot__id_devis_lot == window.current_pk_lot */
			var grid_produit = Ext.ComponentMgr.get('devis[produits]');
			var store_produit = grid_produit.getStore();
			var records_produit = store_produit.getRange();
			for (var i = 0; i < records_produit.length; i++) {
				$(grid_produit.getView().getRow(i))[records_produit[i].data.devis_lot_produit__dot__id_devis_lot==window.current_pk_lot?'show':'hide']();
			}

		} else {
			Ext.ComponentMgr.get('insertBtnProduit').setDisabled(true);

			grid_produits.setTitle("Sélectionnez d'abord un lot !");
		}
	}
};
var getProduitNextId = function() {
	if (!window.pk_produit) window.pk_produit=0;
	window.pk_produit++;
	return 'n'+window.pk_produit;
};
var getProduit = function() {
	return window.current_pk_produit;
};
var setProduit = function(idx) {
	window.current_pk_produit = -1;

	var grid_articles = Ext.ComponentMgr.get('devis[articles]');
	if (grid_articles) {
		/* D'abord on vire les couleurs en surbrillance */
		var grid = Ext.ComponentMgr.get('devis[produits]');
		var store = grid.getStore();
		var records = store.getRange();
		for (var i = 0; i < records.length; i++) {
			$(grid.getView().getRow(i)).css('backgroundColor','');
		}

		if (idx>-1) {
			/* Couleur en surbrillance */
			var grid = Ext.ComponentMgr.get('devis[produits]');
			$(grid.getView().getRow(idx)).css('backgroundColor','#FF9999');

			var rec = grid.store.getAt(idx);
			var label = rec.data.devis_lot_produit__dot__produit;
			grid_articles.setTitle('Articles du produit sélectionné : '+label);

			/* le pk_produit courant */
			window.current_pk_produit = rec.data.devis_lot_produit__dot__id_devis_lot_produit;

			Ext.ComponentMgr.get('insertBtnArticle').setDisabled(false);

			/* Afficher uniquement les lignes qui ont devis_lot_produit__dot__id_devis_lot_produit == window.current_pk_produit */
			var grid_article = Ext.ComponentMgr.get('devis[articles]');
			var store_article = grid_article.getStore();
			var records_article = store_article.getRange();
			for (var i = 0; i < records_article.length; i++) {
				$(grid_article.getView().getRow(i))[records_article[i].data.devis_lot_produit_article__dot__id_devis_lot_produit==window.current_pk_produit?'show':'hide']();
			}

		} else {
			Ext.ComponentMgr.get('insertBtnArticle').setDisabled(true);
			grid_articles.setTitle("Sélectionnez d'abord un produit !");
		}
	}
};

var loadArticles = function(id_produit){
	$.ajax({
		type : "POST",
		url : '/article,autocomplete.ajax',
		data : { id_produit:id_produit, escapefieldstable:'devis_lot_produit_article' },
		dataType : 'json',
		success : function(data) {
			if (data && data.result) {
				var grid_produits = Ext.ComponentMgr.get('devis[produits]');
				var index = grid_produits.getSelectionModel().getSelectedCell();
				var produit = grid_produits.store.getAt(index[0]);
				var current_produit = produit.data.devis_lot_produit__dot__id_devis_lot_produit;

				var grid = Ext.ComponentMgr.get('devis[articles]');
				var store = grid.getStore();
				var theType = store.recordType;
				grid.stopEditing();
				$(data.result).each(function (k, o){
					o["devis_lot_produit_article__dot__id_devis_lot_produit"]=current_produit;
					var p = new theType(o);
					store.insert(k, p);
				});

				Ext.ComponentMgr.get('devis[articles]').refreshHiddenValues();
			}
		}
	});
};


setLot(-1);

{include file="generic-update.tpl.js"}

window.devisForm = formulaire;
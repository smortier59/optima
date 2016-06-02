[new Ext.tree.TreePanel({
	xtype: 'treepanel',
	id: "commandes_tree",
	split: true,
	useArrows:true,
	autoScroll:true,
	animate:true,
	enableDD:true,
	containerScroll: true,
	rootVisible: false,
	frame: true,
	loader:new Ext.tree.TreeLoader({ dataUrl: 'commande,getCommande_ligne.ajax' }),
	root: new Ext.tree.AsyncTreeNode({
		nodeType: 'async',
		expanded: true,
		id: 'source_tree'
	}),
	listeners: {
		'checkchange': function(node,checked){
			/* Cocher le parent si je suis le premier fils coché */
			if (checked && node.parentNode && !node.parentNode.checked && this.getChecked('id',node.parentNode).length===1) {
				node.parentNode.getUI().toggleCheck(checked);
			}
					
			/* Dérouler tous les parcs */
			if(checked){
				node.expand();
			}

			/* Cocher/Décocher tous les enfants */
			if (checked && this.getChecked('id',node).length===1 || !checked) {
				node.cascade(function(node,checked) {
					if (this!=node) {
						this.getUI().toggleCheck(checked);
					}
				},null,[node,checked]);
			}
			/* Mettre en gras */
			if (checked) {
				node.getUI().addClass('treeViewLayerChecked');
			} else {
				node.getUI().removeClass('treeViewLayerChecked');
			}

			/* Gérer le prix */
			var prix = 0;
			if(node.parentNode.attributes.children){
				for (var i = 0; i < node.parentNode.attributes.children.length; i++) {
					if(node.parentNode.attributes.children[i].checked){
						prix+=((node.parentNode.attributes.children[i].prix*1)*(node.parentNode.attributes.children[i].quantite*1));
					}
				}
				var prixmaj=true;
			}
			
			if(prixmaj==true){
				Ext.ComponentMgr.get('bon_de_commande[prix]').setValue(ATF.formatNumeric(prix));
				Ext.ComponentMgr.get('bon_de_commande[prix_cleodis]').setValue(ATF.formatNumeric(prix));
			}

			Ext.getCmp('commandes').setValue(this.getChecked('id').join(','));
		}
	}
}),{
	xtype:'hidden',
	name: 'commandes',
	id: 'commandes',
	value: ''
}]
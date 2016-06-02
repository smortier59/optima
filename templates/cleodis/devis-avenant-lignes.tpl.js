{$id_societe=$requests.id_societe|default:$requests.devis.id_societe}
{if $requests.id_affaire || $requests.devis.id_affaire}
	{$id_affaire=$requests.id_affaire|default:$requests.devis.id_affaire}
{/if}
[new Ext.tree.TreePanel({
	xtype: 'treepanel',
	id: "avenant_tree",
	split: true,
	useArrows:true,
	autoScroll:true,
	animate:true,
	enableDD:true,
	containerScroll: true,
	rootVisible: false,
	frame: true,
	loader:new Ext.tree.TreeLoader({ dataUrl: 'societe,getParc.ajax,type=avenant&id_societe={$id_societe}{if $id_affaire}&id_affaire={$id_affaire}{/if}' }),
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
			
			/* Update la valeur postée */
			Ext.getCmp('avenant').setValue(this.getChecked('id').join(','));		}
	}
}),{
	xtype:'hidden',
	name: 'avenant',
	id: 'avenant',
	value: ''
}]
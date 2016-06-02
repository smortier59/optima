{$id_societe=ATF::_r('id_societe')}
{if ATF::_r('id_exa_directeur') }
	{$exa_directeur=ATF::_r('id_exa_directeur')}
{/if}

[new Ext.tree.TreePanel({
	xtype: 'treepanel',
	id: "restriction_geo_tree",
	split: true,
	useArrows:true,
	autoScroll:true,
	animate:true,
	enableDD:true,
	containerScroll: true,
	rootVisible: false,
	frame: true,
	loader:new Ext.tree.TreeLoader({ dataUrl: 'societe,getTreePanel.ajax,type=restriction_geo&id_societe={$id_societe}{if $exa_directeur}&exa_directeur={$exa_directeur}{/if}' }),
	root: new Ext.tree.AsyncTreeNode({
		nodeType: 'async',
		expanded: true,
		id: 'source_tree'
	}),
	listeners: {
		'checkchange': function(node,checked){			
			
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
			Ext.getCmp('restriction_geo').setValue(this.getChecked('id').join('-'));		}
	}
}),{
	xtype:'hidden',
	name: 'restriction_geo',
	id: 'restriction_geo',
	value: 	''
}]
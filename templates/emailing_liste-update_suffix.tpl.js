{strip}
{
	xtype: 'fieldset'
	,title: '{ATF::$usr->trans(fillChoice,$current_class->table)}'
	,bodyStyle:"align:center"
	,items: [
		{
			xtype:'label'
			,html:"{ATF::$usr->trans(fillChoiceExplication,$current_class->table)}<br><br>"
		}
		,
		{
			xtype:'treepanel',
			id:'treeForChoice',
			animate:true, 
			autoScroll:true,
			loader:new Ext.tree.TreeLoader({ dataUrl: 'emailing_liste,getNodesToFill.ajax' }),
			root: new Ext.tree.AsyncTreeNode({
				nodeType: 'async',
				text: 'Source',
				draggable: false,
				expanded: true,
				id: 'source'
			}),
			listeners :{
				dragdrop:function(tree,node,ev){
					if (ev.tree.id=="treeForSelection" && Ext.ComponentMgr.get('emailing_liste[selNodes]')) {
						if (Ext.ComponentMgr.get('emailing_liste[selNodes]').getValue()) {
							Ext.ComponentMgr.get('emailing_liste[selNodes]').setValue(Ext.ComponentMgr.get('emailing_liste[selNodes]').getValue()+","+node.id);
						} else {
							Ext.ComponentMgr.get('emailing_liste[selNodes]').setValue(node.id);
						}
					}
					
				}
			},
			rootVisible: false,
			baseCls:"tree",
			checkable:false,
			enableDD:true,
			containerScroll: true,
			border: false,
			width: 350,
			height: 500,
			dropConfig: { appendOnly:true }
		}
		,
		{
			xtype:'treepanel',
			id:'treeForSelection',
			animate:true,
			autoScroll:true,
			containerScroll: true,
			root: new Ext.tree.AsyncTreeNode({
				text: 'Selection',
				draggable: false,
				expanded: true,
				id: 'selection'
			}),
			loader: new Ext.tree.TreeLoader({
				dataUrl:'emailing_liste,getNodesToFill.ajax,vide=1',
			}),			
			listeners :{
				dragdrop:function(tree,node,ev){
					if (ev.tree.id=="treeForChoice" && Ext.ComponentMgr.get('emailing_liste[selNodes]')) {
						var s = Ext.ComponentMgr.get('emailing_liste[selNodes]').getValue();
						Ext.ComponentMgr.get('emailing_liste[selNodes]').setValue(s.replace(node.id,"").replace(",,",","));
					}
					
				}
			},
			baseCls:"tree",
			border: false,
			width: 350,
			height: 500,
			enableDD:true,
			dropConfig: { appendOnly:true }
		}
	]

},{
	hidden: true,
	xtype: 'textfield',
	id:"emailing_liste[selNodes]",
	name:"emailing_liste[selNodes]"
}{/strip}

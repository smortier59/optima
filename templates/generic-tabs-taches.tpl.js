/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.QuickTips.init();

var xg = Ext.grid;


// Init de la hauteur du contenu du panel, le problème est que l'on ne peut pas utilisé le plugins fittoparent car quand on créer l'objet ici il n'a pas encore de parent.
var h = $('#dashBoardTabs').height()-30;
var nbElmt = 1;
{if ATF::$usr->get('custom','dashBoard','tache','late')}
	nbElmt++;
{/if}
{if ATF::$usr->get('custom','dashBoard','tache','mine')}
	nbElmt++;
{/if}
var hElmt = h/nbElmt;


/*
 * PREPARATION DES DONNEES
 */
//Taches en retard
var storeTL = new Ext.data.GroupingStore({
	proxy: new Ext.data.HttpProxy({
		url:'tache,tacheLate.ajax'
		,method:'POST'
	}),
	reader: new Ext.data.ArrayReader({
			root:'result'							 
        	,totalProperty: 'totalCount'
		}, [
	   { name: 'societe', mapping: 'societe' },
	   { name: 'tache', mapping: 'tache' },
	   { name: 'tpsRestant', mapping: 'tpsRestant' },
	   { name: 'id', mapping: 'id_tache' }
	])
})
storeTL.load({ params:{ start:0,limit:5 } });  

//Taches imminentes
var storeTI = new Ext.data.GroupingStore({
	id:'tachesImminentes',
	proxy: new Ext.data.HttpProxy({
		url:'tache,tachesImminentes.ajax'
		,method:'POST'
	}),
	reader: new Ext.data.ArrayReader({
			root:'result'							 
        	,totalProperty: 'totalCount'
		}, [
	   { name: 'date', mapping: 'date' },
	   { name: 'societe', mapping: 'societe' },
	   { name: 'tache', mapping: 'tache' },
	   { name: 'id', mapping: 'id_tache' }
	]),
	groupField:'date'
})
storeTI.load({ params:{ start:0,limit:5 } });  

/*
 * FONCTIONS UTILES
 */
setActions = function (t, a, r) {
	switch (a) {
		case 'goto':
			return '<img class="icon16 link" src="{ATF::$staticserver}images/icones/'+a+'.png" onclick="window.location =\' '+t+'-select-'+r.data.id+'.html\'"';
		break;
		default:
			return '<img class="icon16 link" src="{ATF::$staticserver}images/icones/'+a+'.png" onclick="ATF.ajax(\''+t+','+a+'.ajax\',\'id='+r.data.id+'\')" />&nbsp;&nbsp;';
		break;
	}
}

/*
 * CREATION DE L'OBJET GLOBAL
 */
new Ext.Panel({
	height:h
	,id:'gridTache'
	,items: [
		{if ATF::$usr->get('custom','dashBoard','tache','late')}
			{
				xtype:'grid'
				,store: storeTL
				,title:"{ATF::$usr->trans('tacheLateTitle','dashBoard_tache')}"
				,columns: [
					{ header: "{ATF::$usr->trans('societe','tache')}", width: 200, sortable: false, dataIndex: 'societe' },
					{ header: "{ATF::$usr->trans('detail','tache')}", width: 400, sortable: false, dataIndex: 'tache' },
					{ header: "{ATF::$usr->trans('retard','tache')}", width: 50, sortable: false, dataIndex: 'tpsRestant' },
					{ header: "{ATF::$usr->trans('actions','tache')}", width: 50, sortable: false, dataIndex: 'id', renderer: function(value, metadata, record) { 
							return setActions('tache', 'valid' ,record)+setActions('tache', 'cancel' ,record)+setActions('tache', 'goto' ,record); 
						} 
					}
			 
				]
				,autoExpandColumn:true
				,height:hElmt
				,width:'100%'
				,viewConfig: {
					forceFit: true
				}
				,bbar: new Ext.PagingToolbar({
					pageSize: 5,
					store: storeTL,
					displayInfo: true
				})
				,iconCls: 'icon-grid'
			},
		{/if}
		{
			layout:'column'
			,height:hElmt
			,items: [  
				{
					columnWidth:1
					,height:hElmt
					,items: [ {
						xtype:'grid'
						,id:"{ATF::$usr->trans('tacheImminenteTitle','dashBoard_tache')}"
						,store: storeTI
						,columns: [
							{ header: "{ATF::$usr->trans('date','tache')}", width: 50, sortable: false, dataIndex: 'date' },
							{ header: "{ATF::$usr->trans('societe','tache')}", width: 200, sortable: false, dataIndex: 'societe' },
							{ header: "{ATF::$usr->trans('detail','tache')}", width: 400, sortable: false, dataIndex: 'tache' },
							{ header: "{ATF::$usr->trans('actions','tache')}", width: 50, sortable: false, dataIndex: 'id', renderer: function(value, metadata, record) { 
									return setActions('tache', 'valid' ,record)+setActions('tache', 'cancel' ,record)+setActions('tache', 'goto' ,record); 
								} 
							}
					 
						]
						,height:hElmt
						,view: new Ext.grid.GroupingView({
							forceFit:true,
							groupTextTpl: '{ldelim}text{rdelim} ({ldelim}[values.rs.length]{rdelim})'
							,emptyText: 'No data found bis.' 
							,emptyGroupText: 'No data found.' 
					
						})
						,bbar: new Ext.PagingToolbar({
							pageSize: 5,
							store: storeTI,
							displayInfo: true
						})
						,iconCls: 'icon-grid'
					} ]
				}
				{if ATF::$usr->get('custom','dashBoard','tache','calendar')}
					,{
						width:191
						,items:[{
								title: "{ATF::$usr->trans('chooseDate','dashBoard_tache')}",
								items: [ {
									xtype:	'datepicker'
									,startDay : 1
									,listeners: {
										'select': function(node, dd){ 
											storeTI.proxy=new Ext.data.HttpProxy({
												url: 'tache,tachesImminentes.ajax,date='+dd.dateFormat("Y-m-d")
												,method:'POST'
											});
											storeTI.reload();
										}
									} 
										
								} ]
							}]
					}
				{/if}
			]
		}
	]
});
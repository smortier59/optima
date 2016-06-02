/*
 * PREPARATION DES DONNEES
 */
var h = $('#dashBoardTabs').height()-30;

//Taches en retard
var store = new Ext.data.GroupingStore({
	proxy: new Ext.data.HttpProxy({
		url:'hotline,selectForDashBoard.ajax'
		,method:'POST'
	}),
	reader: new Ext.data.ArrayReader({
			root:'result'							 
        	,totalProperty: 'totalCount'
		}, [
	   { name: 'id', mapping: 'id' },
	   { name: 'id_societe', mapping: 'id_societe' },
	   { name: 'societe', mapping: 'societe' },
	   { name: 'hotline', mapping: 'hotline' },
	   { name: 'etat', mapping: 'etat' },
	   { name: 'priorite', mapping: 'priorite', type:'int' },
	   { name: 'avancement', mapping: 'avancement' }
	])
	,groupField:'priorite'

})
store.load({ params:{ start:0,limit:10 } });  

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
new Ext.grid.GridPanel({
	height:h
	,id:'gridHotline'
	,store: store
	,columns: [
		{ header: "{ATF::$usr->trans('id_hotline','hotline')}", width: 50, sortable: false, dataIndex: 'id' },
		{ header: "{ATF::$usr->trans('societe','hotline')}", width: 90, sortable: false, dataIndex: 'societe' },
		{ header: "{ATF::$usr->trans('hotline','hotline')}", width: 300, sortable: false, dataIndex: 'hotline' },
		{ header: "{ATF::$usr->trans('etat','hotline')}", width: 50, sortable: false, dataIndex: 'etat' },
		{ header: "{ATF::$usr->trans('priorite','hotline')}", width: 50, sortable: false, dataIndex: 'priorite' },
		{ header: "{ATF::$usr->trans('avancement','hotline')}", width: 50, sortable: false, dataIndex: 'avancement' },
		
		{ header: "{ATF::$usr->trans('actions','hotline')}", width: 50, sortable: false, dataIndex: 'id', renderer: function(value, metadata, record) { 
				return setActions('hotline', 'goto' ,record); 
			} 
		}
 
	]
	,autoExpandColumn:true
	,width:'100%'
	,viewConfig:new Ext.grid.GroupingView({
		forceFit:true,
		groupTextTpl: '{ldelim}text{rdelim} ({ldelim}[values.rs.length]{rdelim})'
		,emptyText: 'No data found bis.' 
		,emptyGroupText: 'No data found.' 
	})
	,bbar: new Ext.PagingToolbar({
		pageSize: 10,
		store: store,
		displayInfo: true
	})
	,iconCls: 'icon-grid'
});
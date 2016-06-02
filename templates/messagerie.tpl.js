Ext.namespace("Ext.ux");
Ext.QuickTips.init();

Ext.onReady(function(){
	var xg = Ext.grid;

    // shared reader
	var store = new Ext.data.JsonStore({
		url : 'messagerie,getAll.ajax',
		root : 'result',
		fields : [
			{ name: 'subject', type:'string' },
			{ name: 'from', type:'string' },
			{ name: 'to', type:'string' },
			{ name: 'date', type:'date' },
			{ name: 'message_id', type:'string' },
			{ name: 'size', type:'int' },
			{ name: 'uid', type:'int' },
			{ name: 'msgno', type:'int' },
			{ name: 'recent', type:'int' },
			{ name: 'flagged', type:'int' },
			{ name: 'answered', type:'int' },
			{ name: 'deleted', type:'int' },
			{ name: 'seen', type:'int' },
			{ name: 'draft', type:'int' },
			{ name: 'udate', type:'int' },
			{ name: 'body', type:'string' }
		]
	});
	store.load();

    ////////////////////////////////////////////////////////////////////////////////////////
    // Grid 1
    ////////////////////////////////////////////////////////////////////////////////////////
    // row expander
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
			'{literal}',
            '<p><b>Body:</b> {body}</p><br>',
			'{/literal}'
        )
    });

    var grid1 = new xg.GridPanel({
        store: store,
        cm: new xg.ColumnModel({
            defaults: {
                width: 50,
                sortable: true
            },
            columns: [
                expander,
                { id:'subject',header: "subject", width: 40, dataIndex: 'subject' },
                { header: "from", dataIndex: 'from' },
                { header: "to", dataIndex: 'to' },
                { header: "date", renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'date' },
                { header: "size", dataIndex: 'size' }
            ]
        }),
        viewConfig: {
            forceFit:true
        },    
		layout:'fit',    
        height: 600,
        plugins: expander,
        collapsible: true,
        animCollapse: false,
        title: 'Expander Rows, Collapse and Force Fit',
        iconCls: 'icon-grid',
        renderTo: 'webmailDiv'
    });
	
	
});


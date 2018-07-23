{strip}
{* Ajout des champs pas forcément dans la vue, nécessaires pour ce renderer *}
{util::push($fieldsKeys,"parc.existence")}
{util::push($fieldsKeys,"parc.date_achat")}

ATF.renderer.parc_recuperation=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {

		if(record.data["parc__dot__existence"] == "actif"){
			var idDivActionsRecupParc = Ext.id();
			var id = record.data[table+'__dot__id_'+table];

			var btnretourEnStock = {
				xtype:'button',
				id:"retourEnStock",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'retourEnStock',
				tooltip: '{ATF::$usr->trans("Récupérer un matériel du client")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if(confirm('Etes-vous sur de recuperer ce parc?')){
							ATF.ajax('parc,retourEnStock.ajax','id_parc='+id);
							store.reload();
						}
					}
				}
			};

			(function(){
				var params = {
					renderTo: idDivActionsRecupParc,
					layout:'fit',
					items:[btnretourEnStock]

				};
				var p = new Ext.Container(params);
			}).defer(25);

			return '<div class="left" id="'+idDivActionsRecupParc+'"></div>';
		}else{
			return '<div class="left" id="'+idDivActionsRecupParc+'"><img src="{ATF::$staticserver}images/icones/no_2.png" /></div>';

		}
	}
};

ATF.renderer.parc_existant=function(table,field) {
        return function(filetype, meta, record, rowIndex, colIndex, store) {
                var idDiv = Ext.id();
                var id = record.data[table+'__dot__id_'+table];

                if(record.data["parc__dot__existence"] == "actif"){
                        var btnRelocationParc = {
                                xtype:'button',
                                id:"relocationParc"+id,
                                buttonText: '',
                                buttonOnly: true,
                                iconCls: 'relocationParc',
                                cls:'floatLeft',
                                tooltip: '{ATF::$usr->trans("Affecter un matériel présent en stock")}',
                                tooltipType:'title',
                                listeners: {
                                        'click': function(fb, v){

                                                        var parc_existant = new Ext.data.JsonStore({
                                                                url : 'parc,getAllParcAttenteRelocation.ajax',
                                                                root : 'result',
                                                storeId: 'autreFactureStore',
                                                idProperty: 'id',
                                                autoLoad: true,
                                                fields: [
                                                                        { name:'value', mapping: 'id_parc' },
                                                                        { name:'text', mapping: 'text' }
                                                                ]

                                                        });


                                                        var hiddenField = new Ext.form.Hidden({
                                                            name: 'comboDisplay'
                                                        });

                                                        var form = new Ext.FormPanel({
                                                                        frame:true,
                                                                        autoHeight:true,
                                                                        id:'myForm'+id,
                                                                        name:'myFormName'+id,
                                                                        title: '',
                                                                        bodyStyle:'padding:5px 5px 0',
                                                                        items: [
                                                                                {
                                                                                         xtype: "combo"
                                                                                        ,fieldLabel: "parc en attente de relocation"
                                                                                        ,name:"parc_existant"
                                                                                        ,store: parc_existant
                                                                                        ,displayField: "text"
                                                                                        ,mode: "local"
                                                                                        ,anchor: '100%'

                                                                                        ,listeners: {
                                                                                        select: function(combo, record) {
                                                                                            hiddenField.setValue(record.data['value']);
                                                                                        }
                                                                                    }
                                                                                },
                                                                                hiddenField
                                                                        ],
                                                                        buttons: [{
                                                                                 text: 'Ok'
                                                                                ,handler: function(a,b,c,d){
                                                                                        Ext.getCmp('myForm'+id).getForm().submit({
                                                                                                submitEmptyText:false,
                                                                                                method  : 'post',
                                                                                                waitMsg : '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
                                                                                                waitTitle :'{ATF::$usr->trans(loading)|escape:javascript}',
                                                                                                url     : 'extjs.ajax',
                                                                                                params: {
                                                                                                        'extAction':'parc'
                                                                                                        ,'extMethod':'relocationParc'
                                                                                                        ,'id':id
                                                                                                }
                                                                                                ,success:function(form, action) {
                                                                                                        ATF.ajax_refresh(action.result,true);
                                                                                                        Ext.getCmp('myForm'+id).destroy();
                                                                                                        Ext.getCmp('mywindow'+id).destroy();
                                                                                                        store.reload();
                                                                                                }
                                                                                                ,timeout:3600
                                                                                        });
                                                                                }
                                                                        },
                                                                        {
                                                                                text: 'Annuler',
                                                                                handler: function(){
                                                                                        Ext.getCmp('myForm'+id).destroy();
                                                                                        Ext.getCmp('mywindow'+id).destroy();
                                                                                }
                                                                        }]
                                                        });


                                                        var height = 700;
                                                        var width = 700;
                                                        new Ext.Window({
                                                                title: '{ATF::$usr->trans("Parc provenant du stock : ")}',
                                                                id:'mywindow'+id,
                                                                plain:true,
                                                                bodyStyle:'padding:5px;',
                                                                buttonAlign:'center'
                                                        });
                                                        if (form) {
                                                                Ext.getCmp('mywindow'+id).add(form);
                                                                height += 400;
                                                                width = 800;
                                                        }
                                                        if (!Ext.getCmp('mywindow'+id)) {
                                                        }
                                                        Ext.getCmp('mywindow'+id).setHeight(height);
                                                        Ext.getCmp('mywindow'+id).setWidth(width);
                                                        Ext.getCmp('mywindow'+id).show();
                                        }
                                }
                        };

                        (function(){
                                var params = {
                                        renderTo: idDiv,
                                        items:[btnRelocationParc]

                                };
                                var p = new Ext.Container(params);
                        }).defer(25);
                        return '<div class="left" id="'+idDiv+'"></div>';

                }else{
                        return '<div class="left" id="'+idDiv+'"><img src="{ATF::$staticserver}images/icones/no_2.png" /></div>';

                }
        }
};


{/strip}
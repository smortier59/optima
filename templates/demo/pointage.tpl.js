/*!
 * Ext JS Library 3.4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */
Ext.onReady(function(){
	Ext.QuickTips.init();
    // shorthand alias
    var fm = Ext.form;
	
	var currentMonth = (new Date()).getMonth();
	var currentYear = (new Date()).getFullYear();
	var currentView = 'day';
	
	var fillTotal = function(e,r,store) {
		if (e.record.json[e.field]) {
			e.record.json[e.field] = parseFloat(e.value);
		}
		if (e.record.json['label_'+e.field]) {
			e.record.json['label_'+e.field] = parseFloat(e.value);
		}
		r[0].set(e.field,parseFloat(e.value));
		var semaine = Math.floor((e.column-2)/8);
		var indexTot = semaine*8+2+8;

		r[0].set('total'+semaine,parseFloat(r[0].get('total'+semaine))-parseFloat(e.originalValue)+parseFloat(e.value));
		
		fillTotauxLigne(r);
		
		fillTotauxBottom(e,semaine);
		
//		fillTotalTaches();
	}
	
	var fillTotauxLigne = function (r) {
		// Gestion des totaux de la ligne
		var total = 0;
		var totalWE = 0;
		var totalSE = 0;
		for (i=1; i<32; i++) {
			if (i<10) i = "0"+i;
			if (parseFloat(r[0].data[i])) {
				total = parseFloat(total) + parseFloat(r[0].data[i]);
			}
			
			var d = new Date(currentYear, currentMonth, i, 0, 0, 0, 0);
			
			if (d.getDay()==0 || d.getDay()==6) {
				totalWE = parseFloat(totalWE) + parseFloat(r[0].data[i]);
			} else {
				totalSE = parseFloat(totalSE) + parseFloat(r[0].data[i]);
			}
		}
		r[0].set('total',parseFloat(total));
		r[0].set('totalWeek',parseFloat(totalWE));
		r[0].set('totalSemaine',parseFloat(totalSE));
	}
	
	var fillTotauxBottom = function (e,semaine) {
		// Gestion des totaux du bas
		var c = grid.store.getTotalCount()-1;
		var last = grid.store.getRange(c,c);

		last[0].set(e.field,parseFloat(last[0].get(e.field))-parseFloat(e.originalValue)+parseFloat(e.value));
		last[0].set('total'+semaine,parseFloat(last[0].get('total'+semaine))-parseFloat(e.originalValue)+parseFloat(e.value));
		last[0].set('total',parseFloat(last[0].get('total'))-parseFloat(e.originalValue)+parseFloat(e.value));
	}
	
	var fillTotalTaches = function (e,r,store) {
		
	}
	
	var formatLabel = function(value) {
		var v = String(value).split(".");
		var label = new String();
		if (v.length == 2 && parseInt(v[1])) {
			label = parseInt(v[0]);
			if (v[1]==25) {
				label += "&frac14;";
			} else if (v[1]==50 || v[1]==5) {
				label += "&frac12;";
			} else {
				label += "&frac34;";
			}
		} else {
			label = parseInt(value);
		}
		return label;
	}
	
	var formatColumns = function(currentYear, currentMonth) {
		var columns = [{
			id: 'user',
			header: '{ATF::$usr->trans("user")}',
			align:'left',
			dataIndex: 'user',
			width: 110,
			fixed: true,
			renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				metaData.css = "cellUser";
				return value;
			}
		}]
		
		var cntTot = 0;
		// Une colonne pour chaque jour

		var maxDay = new Date(currentYear, currentMonth+1, 0).getDate()+1;
		for (i=1; i<maxDay; i++) {
			date = new Date(currentYear, currentMonth, i)
			if (date.getMonth() == currentMonth) {
				columns.push(
					{
						header: date.format('D')+"<br>"+date.format('d'),
						dataIndex: date.format('d'),
						id:date.format('d'),
						width: 32,
						align:'center',
						fixed: true,
						// use shorthand alias defined above
						editor: new fm.NumberField({
							allowBlank: false,
							selectOnFocus: true,
						}),
						renderer: function(value, metaData, record, rowIndex, colIndex, store) {
							var type = 'type_label_'+this.id;

							if (record.json.rowId=='total') {
								metaData.css = "cellTotal";
							} else if (Ext.getCmp('SplitButtonForTypeMOE').getText() == record.json[type] && Ext.getCmp('SplitButtonForTypeMOE').getText()!='Total') {
								metaData.css = 'blue';
							} else if (record.json[type] && Ext.getCmp('SplitButtonForTypeMOE').getText()!='Total') {
								metaData.css = 'grey';
							} else {
								metaData.css = 'black';
							}

							return value?formatLabel(value):"0";
						}
					}
				);
				if (date.format('D')=="Dim") {
					columns.push(
						{
							header: "<b>{ATF::$usr->trans('total')}<br>{ATF::$usr->trans('semaine')|truncate:3:''}</b>",
							dataIndex: "total"+cntTot,
							id:"total"+cntTot,
							fixed: true,
							renderer: function(value, metaData, record, rowIndex, colIndex, store) {
								metaData.css = "cellTotal";
								return value?formatLabel(value):"0";
							},
							align:'center',
							width: 50
						}
					);
					cntTot = cntTot +1;
				}
			}
		}
		/* Total des jours de la semaine */
		columns.push(
			{
				header: "<b>{ATF::$usr->trans('SE')}</b>",
				dataIndex: "totalSemaine",
				align:'center',
				fixed: true,
				renderer: function(value, metaData, record, rowIndex, colIndex, store) {
					metaData.css = "cellTotal";
					return value?formatLabel(value):"0";
				},
				width: 50
			}
		);
		/* Total des jours du Week end */
		columns.push(
			{
				header: "<b>{ATF::$usr->trans('WE')}</b>",
				dataIndex: "totalWeek",
				align:'center',
				fixed: true,
				renderer: function(value, metaData, record, rowIndex, colIndex, store) {
					metaData.css = "cellTotal";
					return value?formatLabel(value):"0";
				},
				width: 50
			}
		);
		/* Total de tous les jours */
		columns.push(
			{
				header: "<b>{ATF::$usr->trans('total')}</b>",
				dataIndex: "total",
				align:'center',
				fixed: true,
				renderer: function(value, metaData, record, rowIndex, colIndex, store) {
					metaData.css = "cellTotal";
					return value?formatLabel(value):"0";
				},
				width: 50
			}
		);
		
		return columns;
	}
	
	    // the column model has information about grid columns
    // dataIndex maps the column to the specific data field in
    // the data store (created below)
    var cm = new Ext.grid.ColumnModel(formatColumns(currentYear, currentMonth));


	var store = new Ext.data.JsonStore({
		url : 'pointage,getPointageByUser.ajax',
		baseParams:{
			'month':currentMonth
			,'year':currentYear
			,'daynight':currentView
		},
		root : 'result',
		fields : [
			{ name: 'id' },
			{ name: 'id_user' },
			{ name: 'user' },
			{ name: 'total' },
			{ name: 'totalSemaine' },
			{ name: 'totalWeek' },
			{ name: 'total0' },
			{ name: 'total1' },
			{ name: 'total2' },
			{ name: 'total3' },
			{ name: '01', type:'string' },
			{ name: '02', type:'string' },
			{ name: '03', type:'string' },
			{ name: '04', type:'string' },
			{ name: '05', type:'string' },
			{ name: '06', type:'string' },
			{ name: '07', type:'string' },
			{ name: '08', type:'string' },
			{ name: '09', type:'string' },
			{ name: '10', type:'string' },
			{ name: '11', type:'string' },
			{ name: '12', type:'string' },
			{ name: '13', type:'string' },
			{ name: '14', type:'string' },
			{ name: '15', type:'string' },
			{ name: '16', type:'string' },
			{ name: '17', type:'string' },
			{ name: '18', type:'string' },
			{ name: '19', type:'string' },
			{ name: '20', type:'string' },
			{ name: '21', type:'string' },
			{ name: '22', type:'string' },
			{ name: '23', type:'string' },
			{ name: '24', type:'string' },
			{ name: '25', type:'string' },
			{ name: '26', type:'string' },
			{ name: '27', type:'string' },
			{ name: '28', type:'string' },
			{ name: '29', type:'string' },
			{ name: '30', type:'string' },
			{ name: '31', type:'string' }
		],
		listeners: {
			beforeload:function() {
			}
		}

	});

	var storeIS = new Ext.data.JsonStore({
		url : 'user,autocomplete.ajax',
		root : 'result',
		fields : [
			{ name: 'value', mapping:'0' },
			{ name: 'text', mapping:'1' }
		]
	});

	var storeTache = new Ext.data.JsonStore({
		url : 'pointage_tache,autocomplete.ajax',
		root : 'result',
		fields : [
			{ name: 'value', mapping:'0' },
			{ name: 'text', mapping:'1' }
		]
	});
	storeTache.load();
	
	var comboTache = new Ext.form.ComboBox({
		xtype: 'combo',
		store: storeTache,
		displayField: 'text',
		valueField: 'value',
		mode: 'local',
		triggerAction: 'all',
		emptyText:'{ATF::$usr->trans("tacheChoice","pointage")}',
		selectOnFocus:true
    });
	
	var planificationWindow = new Ext.Window({
		title: '{ATF::$usr->trans("planifier_le_pointage","pointage")}',
		id:'planificationWindow',
		width: 800,
		height:550,
		plain:true,
		bodyStyle:'padding:5px;',
		buttonAlign:'center',
		closeAction:'hide',
		items: new Ext.FormPanel({
			frame:true,
			autoHeight:true,
			id:'planificationForm',
			name:'planificationFormName',
			title: '',
			bodyStyle:'padding:5px 5px 0',
			items: [{
				xtype: 'itemselector',
				name: 'pointage[userSelector]',
				id: 'pointage[userSelector]',
				fieldLabel: '{ATF::$usr->trans("userChoice","pointage")}',
				imagePath: '{ATF::$staticserver}images/extjs/',
				multiselects: [{
					width: 310,
					height: 300,
					id:'availableUser',
					store: storeIS,
					displayField: 'text',
					valueField: 'value'
				},{
					width: 310,
					height: 300,
					id:'selectedUser',
					store: [['','']],
					tbar:[{
						text: '{ATF::$usr->trans("vider")}',
						handler:function(){
							Ext.getCmp('planificationForm').getForm().findField('pointage[userSelector]').reset();
						}
					}]
				}]
			},{
				xtype: 'checkboxgroup',
				fieldLabel: '{ATF::$usr->trans("jours_affectes","pointage")}',
				items: [
					{ boxLabel: '{ATF::$usr->trans("lundi")|truncate:3:""}', name: 'lundi', inputValue:1, checked: true },
					{ boxLabel: '{ATF::$usr->trans("mardi")|truncate:3:""}', name: 'mardi', inputValue:1, checked: true },
					{ boxLabel: '{ATF::$usr->trans("mercredi")|truncate:3:""}', name: 'mercredi', inputValue:1, checked: true },
					{ boxLabel: '{ATF::$usr->trans("jeudi")|truncate:3:""}', name: 'jeudi', inputValue:1, checked: true },
					{ boxLabel: '{ATF::$usr->trans("vendredi")|truncate:3:""}', name: 'vendredi', inputValue:1, checked: true },
					{ boxLabel: '{ATF::$usr->trans("samedi")|truncate:3:""}', name: 'samedi', inputValue:1 },
					{ boxLabel: '{ATF::$usr->trans("dimanche")|truncate:3:""}', name: 'dimanche', inputValue:1 }
				]
			},{
				xtype: 'combo',
				name: 'pointage[tache]' ,
				id: 'pointage[tache]',
				store: storeTache,
				displayField: 'text',
				valueField: 'value',
				mode: 'local',
				triggerAction: 'all',
				emptyText:'{ATF::$usr->trans("tacheChoice","pointage")}',
				selectOnFocus:true,
				fieldLabel:'{ATF::$usr->trans("tache","pointage")}'
			},{
                xtype: 'compositefield',
                fieldLabel: '{ATF::$usr->trans("temps","pointage")}',
                msgTarget : 'side',
                defaults: { xtype: 'spinnerfield' },
				items: [
                    {
						name: 'pointage[temps]' 
						,id: 'pointage[temps]'
						,fieldLabel:'{ATF::$usr->trans("temps")}'
						,value:7
						,minValue: 0
						,maxValue: 12
						,allowDecimals: false
						,incrementValue:0.25
						,accelerate: true
                    }
                ]
            },{
                xtype: 'compositefield',
                fieldLabel: '{ATF::$usr->trans("date","pointage")}',
                msgTarget : 'side',
                anchor    : '-20',
                defaults: {
                    xtype: 'datefield',
					flex: 1,
					format:'Y-m-d'
                },
                items: [
                    {
                        name : 'pointage[startDate]',
						emptyText:'{ATF::$usr->trans("startDate","pointage")}'
                    },
                    {
                        name : 'pointage[endDate]',
						emptyText:'{ATF::$usr->trans("endDate","pointage")}'
                    }
                ]
            },{
                xtype: 'radiogroup',
                fieldLabel: '{ATF::$usr->trans("daynight","pointage")}',
				items: [
					{ boxLabel: '{ATF::$usr->trans("day")}', name: 'pointage[daynight]', inputValue:'day', checked: true },
					{ boxLabel: '{ATF::$usr->trans("night")}', name: 'pointage[daynight]', inputValue:'night' }
				]
            }],
	
			buttons: [{
				text: '{ATF::$usr->trans("ok")}',
				handler: function(){
					Ext.getCmp('planificationForm').getForm().submit({
						submitEmptyText:false,
						method  : 'post',
						waitMsg : 'Mise à jour de l\'élément en cours...',
						waitTitle : 'Chargement',
						url     : 'extjs.ajax',
						params: {
							'extAction':'{$current_class->table}'
							,'extMethod':"planification"
						}
						,success:function(form, action) {
							Ext.getCmp('planificationWindow').hide();
							store.reload();
						}
						,failure:function(form, action) {
							var title='Problème';
							if (action.failureType === Ext.form.Action.CONNECT_FAILURE){
								Ext.Msg.alert(title, 'Server reported:'+action.response.status+' '+action.response.statusText);
							} else if (action.failureType === Ext.form.Action.SERVER_INVALID){
								Ext.Msg.alert(title, action.result.errormsg);
							} else if (action.failureType === Ext.form.Action.CLIENT_INVALID){
								Ext.Msg.alert(title, "Un champs est mal renseigné");
							} else if (action.failureType === Ext.form.Action.LOAD_FAILURE){
								Ext.Msg.alert(title, "Un champs est mal renseigné");
							}
						}
						,timeout:3600
					});
				}
			},{
				text: '{ATF::$usr->trans("annuler")}',
				handler: function(){
					Ext.getCmp('planificationForm').destroy();
					Ext.getCmp('planificationWindow').hide();
				}
			}]
		})
	});
	
	var gridView = new Ext.grid.GridView({ 
		getRowClass : function (row, index) { 
			return row.json.cls; 
		} 
	});

    var grid = new Ext.grid.EditorGridPanel({
        store: store,
        cm: cm,
		sm: new Ext.grid.CellSelectionModel(),
        renderTo: 'sheet',
        height: 800,
		cls:'totalPointage',
        title: '{ATF::$usr->trans("feuille_de_pointage","pointage")} '+date.format("F Y"),
        frame: true,
		view: gridView,
        clicksToEdit: true,
/*        viewConfig: {
            forceFit: true
        },
*/		listeners:{
			'cellclick': function(grid,row,col,event) {
				var record = grid.getStore().getAt(row);
				var type = 'type_label_'+grid.getColumnModel().getColumnId(col);
				if (!record.json[type]) {
					Ext.getCmp('SplitButtonForAffectationMOE').setDisabled(false);
				} else {
					Ext.getCmp('SplitButtonForAffectationMOE').setDisabled(true);
				}
			},
			'afteredit': function(e){
				var r = this.store.getRange(e.row,e.row);
				Ext.Ajax.request({
					url: 'pointage,updateTemps.ajax',
					success: function(a,b) {
						fillTotal(e,r,this.store);
						fillTotalTaches(e,r,this.store);
					},
					failure:  function(a,b) {
						ATF.log("FAILURE");
						ATF.log(a);
						ATF.log(b);  
					},
					params: { 
						id_pointage: r[0].json['id_'+e.field],
						id_user: r[0].data.id_user,
						year: currentYear,
						month: currentMonth+1,
						day: e.field,
						daynight: currentView,
						temps: e.value
					},
					scope:this
				});
				
				

			}
		},
        tbar: [{
			iconCls : 'icon-dayNight',
            text: '{ATF::$usr->trans("day_night","pointage")}',
            handler : function(){
				if (currentView=='day') {
					currentView = 'night';
				} else {
					currentView = 'day';
				}
				// Reconfiguration des paramètres POST
				grid.store.baseParams = {
					'month':currentMonth
					,'year':currentYear
					,'daynight':currentView
				};
				// Reload du store
				grid.store.load();
				grid.reconfigure(grid.store, new Ext.grid.ColumnModel(formatColumns(currentYear, currentMonth)));
				
            }
        },"-",{
			iconCls : 'icon-prev',
			scale : 'medium',
            handler : function(){
				currentMonth = currentMonth - 1;
				// Reconfiguration des paramètres POST
				grid.store.baseParams = {
					'month':currentMonth
					,'year':currentYear
					,'daynight':currentView
				};
				// Reload du store
				grid.store.load();
				grid.reconfigure(grid.store, new Ext.grid.ColumnModel(formatColumns(currentYear, currentMonth)));
				
				date = new Date(currentYear, currentMonth, 1)
				Ext.getCmp('btnMonth').setText(date.format("F"));
            }
        },{
            text: date.format("F"),
			id:"btnMonth",
            handler : function(){
            }
        },{
			iconCls : 'icon-next',
			scale : 'medium',
            handler : function(){
				currentMonth = currentMonth +1;
				// Reconfiguration des paramètres POST
				grid.store.baseParams = {
					'month':currentMonth
					,'year':currentYear
					,'daynight':currentView
				};
				
				// Reload du store
				grid.store.load();
				
				grid.reconfigure(grid.store, new Ext.grid.ColumnModel(formatColumns(currentYear, currentMonth)));

				date = new Date(currentYear, currentMonth, 1)
				Ext.getCmp('btnMonth').setText(date.format("F"));
            }
        },"-",{
			iconCls : 'icon-prev',
			scale : 'medium',
            handler : function(){
				currentYear = currentYear - 1;
				// Reconfiguration des paramètres POST
				grid.store.baseParams = {
					'month':currentMonth
					,'year':currentYear
					,'daynight':currentView
				};
				// Reload du store
				grid.store.load();
				
				grid.reconfigure(grid.store, new Ext.grid.ColumnModel(formatColumns(currentYear, currentMonth)));

				date = new Date(currentYear, currentMonth, 1)
				Ext.getCmp('btnYear').setText(date.format("Y"));
            }
        },{
            text: date.format("Y"),
			id:"btnYear",
            handler : function(){
            }
        },{
			iconCls : 'icon-next',
			scale : 'medium',
            handler : function(){
				currentYear = currentYear +1;
				// Reconfiguration des paramètres POST
				grid.store.baseParams = {
					'month':currentMonth
					,'year':currentYear
				};
				// Reload du store
				grid.store.load();
				
				grid.reconfigure(grid.store, new Ext.grid.ColumnModel(formatColumns(currentYear, currentMonth)));

				date = new Date(currentYear, currentMonth, 1)
				Ext.getCmp('btnYear').setText(date.format("Y"));
            }
        },"-",{
            xtype:'splitbutton',
            text: '{ATF::$usr->trans("total","pointage")}',
			id:'SplitButtonForTypeMOE',
            iconCls: 'icon-typeMOE',
			listeners : {
				'afterrender': function (el) {
					{foreach ATF::pointage_tache()->saTaches() as $k=>$i}
						this.menu.add({
							text:"{$i['pointage_tache']}",
							handler: function(e,j) {
								Ext.getCmp('SplitButtonForTypeMOE').setText("{$i['pointage_tache']}");
								grid.store.reload();
							}
						});
					{/foreach}
				}
			},
            menu: [
				{ text: '{ATF::$usr->trans("total")}', handler: function(e,j) {
					Ext.getCmp('SplitButtonForTypeMOE').setText("Total");
					grid.checkAndChangeClassCSS('totalPointage');
				}}
			]
        },"-",{
            xtype:'splitbutton',
            text: '{ATF::$usr->trans("affectation","pointage")}',
			disabled:true,
			id:'SplitButtonForAffectationMOE',
            iconCls: 'icon-affectationMOE',
			listeners : {
				'afterrender': function (el) {
					{foreach ATF::pointage_tache()->saTaches() as $k=>$i}
						this.menu.add({
							text:"{$i['pointage_tache']}",
							id:"{$i['id_pointage_tache']}",
							handler: function(e,j) {
								// Cellules selectionnées
								var cellsSel = grid.getSelectionModel().selections;
								// Les colonnes du grid
								var cm = grid.getColumnModel();
								var idPointage = {};

								//Pour chaque selection
								for(i=0; i<cellsSel.length; i++) {
									
									// Ligne qui correspond a l'enregistrement dans le store
									var r = store.getRange(cellsSel[i][0],cellsSel[i][0]);
									// Grâce a l'ID de la colonne, on récupère le numéro du jour
									var currentDay = cm.getColumnId(cellsSel[i][1]);

									// On met l'id pointage s'il existe pour faire l'update
									if (r[0].json['id_'+currentDay]) {
										idPointage[i] = r[0].json['id_'+currentDay];
									}
								}
								if (idPointage) {
									Ext.Ajax.request({
										url: 'pointage,updateTache.ajax',
										success: function(a,b) {
											store.reload();
	
										},
										failure:  function(a,b) {
	
										},
										params: { 
											id_pointage: $H(idPointage).toJSON(),
											id_pointage_tache: e.id
										},
										scope:this
									});
								}
							}
						});
					{/foreach}
				}
			},
            menu: [ ]
        },"-",{
            text: '{ATF::$usr->trans("planification","pointage")}',
			id:'buttonPlanification',
            iconCls: 'icon-buttonPlanification',
			listeners: {
				'click': function(el,ev) {
					planificationWindow.show();
				}
			}
        },"->",{
            text: '{ATF::$usr->trans("export")}',
			id:'export',
            iconCls: 'icon-export',
			listeners: {
				'click': function(el,ev) {
					window.location='pointage,exportXLS.ajax,year='+currentYear+'&month='+(currentMonth)+'&daynight='+currentView;
				}
			}
        },{
            text: '{ATF::$usr->trans("refresh")}',
			id:'refresh',
            iconCls: 'icon-refresh',
			listeners: {
				'click': function(el,ev) {
					store.reload();
				}
			}
        }]
    });

    // manually trigger the data store load
    store.load();
    storeIS.load();
});
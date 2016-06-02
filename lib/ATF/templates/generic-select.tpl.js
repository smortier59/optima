{strip}
	{$id_crypt = $smarty.get["id_{$current_class->table}"]|default:$requests[$current_class->table]["id_{$current_class->table}"]}
	{$id = $current_class->decryptId($id_crypt)}
	{$module = $current_class->name()}

	/* Le select et l update étant maitnenant les mêmes, il ne faut pas faire de différence en tant que récupération de colonnes */
	{if ATF::_r('event')=="select"}
		{$event="update"}
	{else if ATF::_r('event')}
		{$event = ATF::_r('event')}
	{/if}

	var event = "{$event}";

	{if ATF::$html->template_exists("`$current_class->table`-update.tpl.js")}
		{include file="`$current_class->table`-update.tpl.js" formName=formulaire idForm="`$current_class->table`-`$id`" notitle=true}
	{else}
		{include file="generic-update.tpl.js" formName=formulaire idForm="`$current_class->table`-`$id`" notitle=true}
	{/if}

	ATF.unsetFormIsActive();

	ATF.basicInfo = new Ext.FormPanel(formulaire);





	var buttonTpl = new Ext.Template(
		'<div class="button" id="{literal}{id}{/literal}" {literal}onclick="{onclick}"{/literal} style="text-align:center; font-weight:bold; color: white; margin:5px;">',
		    '<div id="left">',
		        '<img src="{ATF::$staticserver}images/icones/{literal}{icone}{/literal}.png" align="" style="width:16px; height:16px" />',
		    '</div>',
		    '<div id="middle" {literal}{style}{/literal}',
		    	'<span>',
		        	'{literal}{text}{/literal}',
		        '</span>',
		    '</div>',
		    '<div id="right"></div>',
		'</div>'
	);


	var collapseView = function(v,event) {
		ATF.basicInfo.getForm().items.each(function(field){
			if (!v) {
				field.setVisible(true);
			} else if (field.xtype=="compositefield") {
				for (var i=0; i<field.items; i++) {
					if (v && !field.items[i].getValue()) {
						field.items[i].setVisible(false);
					}
				}
			} else if (v && !field.getValue()) {
				if (event=="insert") {
					/* Si on est en insert, on laisse toujours visible les champs du primary */
					if (field.findParentByType('fieldset') && field.findParentByType('fieldset').id!="panel_primary") {
						field.setVisible(false);
					}
				} else {
					field.setVisible(false);
				}

			}

		});

		var divs = document.getElementsByTagName("fieldset"), item;
		for (var i=0; i<divs.length; i++) {
			var id = divs[i].id;
			if (id.indexOf("panel_")==0) {
				if (id != "panel_primary") {
					Ext.getCmp(id).setVisible(!v);
				}
			}
		}
	};

	var collapseHandler = function(el,v) {
		collapseView(v,"{$event}");
	};

	var editHandler = function (el,v) {
		el.setVisible(false);
		Ext.getCmp("btnEditSave{$id}").setVisible(true);
		Ext.getCmp("btnEditCancel{$id}").setVisible(true);
		Ext.getCmp("buttonGroupEdit{$id}").doLayout();
		ATF.basicInfo.getForm().items.each(function(field){
			field.setReadOnly(false);

			if (field.items && field.items.length) {

				if (field.items.each!=undefined) {
					field.items.each(function(f){
						try {
							f.setReadOnly(false);
						} catch (e) {
							ATF.log('Impossible de faire le setReadOnly sur ce champ :');
							ATF.log(f);
						}

					});
				}
			}

		});
		ATF.setFormIsActive();
	};


	var insertHandler = function () {
		ATF.goTo("{$current_class->table}-insert.html");
	};

	var deleteHandler = function (el,v) {

		Ext.Msg.confirm(
			'Confirmation',
			'{ATF::$usr->trans(Etes_vous_sur)|escape:javascript}',
			function (value) {
				if (value=="yes") {
					ATF.deleteLoadMask.show();
					ATF.tpl2div(
						'{$current_class->name()|urlencode},delete.ajax',
						'id={$id_crypt}',
						{
							onComplete : function(){
								ATF.deleteLoadMask.hide();
							}
						}
					);
				}

			}
		);

	};

	var cancelEditHandler = function (el,v) {
		el.setVisible(false);
		Ext.getCmp("btnEditSave{$id}").setVisible(false);
		Ext.getCmp("btnEdit{$id}").setVisible(true);
		Ext.getCmp("buttonGroupEdit{$id}").doLayout();
		ATF.basicInfo.getForm().items.each(function(field){
			field.setReadOnly(true);
		});
		ATF.unsetFormIsActive();
	};

	var saveHandler = function (el,v) {
		var redirect=false;
		ATF.basicInfo.getForm().submit({
			submitEmptyText:false,
			method  : 'post',
			waitMsg : '{ATF::$usr->trans(updating_element)|escape:javascript}',
			waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
			url     : 'extjs.ajax',
			params: {
				'{$current_class->table}[id_{$current_class->table}]':'{$identifiant}'
				,'extAction':'{$current_class->table}'
				,'extMethod':"{$event}"
			}
			,success:function(form, action) {
				if (action.result.error.length) {
					ATF.extRefresh(action);
				} else {

					if (event=="insert") {
						ATF.goTo("{$current_class->table}-select-");
						ATF.extRefresh(action);
					} else {
						el.setVisible(false);
						Ext.getCmp("btnEditCancel{$id}").setVisible(false);
						Ext.getCmp("btnEdit{$id}").setVisible(true);
						Ext.getCmp("buttonGroupEdit{$id}").doLayout();
						form.items.each(function(field){
							field.setReadOnly(true);
						});
						ATF.unsetFormIsActive();
					}


				}
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
	};


	var mainPanel = new Ext.Panel({
		renderTo:{if $renderTo}{$renderTo}{else}'main'{/if},
		layout:'anchor',
		id:'containerFiche{$current_class->table}{$id}',
		/*monitorResize: true,*/
		border: true,
		{if !$noStyle}
			style: {
				marginRight: '10px',
				marginLeft: '25px'
			},
		{/if}
		items:[
			{
				xtype: 'panel',
				border: false,
				id:'HeaderFiche{$current_class->table}{$id}',
				defaults: {
					border: false
				},
				style: {
					backgroundColor: '#fff' ,
					verticalAlign: 'middle',
					padding : '5px 25px 0 10px',
				},
				items: [{
					html:'<img src="{ATF::$staticserver}images/module/48/{$module}.png" width="32">',
					width:35,
					style: {
						float: 'left'
					}
				}
				{if !$id}
					,{
						html:'<span class="ficheTitle">Nouvel enregistrement</span>',
						style: {
							float: 'left'
						}
					}
					,{
						html:'<span id="extraDataInsert"></span>',
						style: {
							float: 'left',
							marginLeft: '10px'
						}
					}


				{else}
					{if ATF::$html->template_exists("`$current_class->table`-title.tpl.js")}
						,{include file="`$current_class->table`-title.tpl.js" infos=$requests[$current_class->table]}
					{else}
						,{
							html:'	<div class="ficheTitle">
										{$current_class->nom($id)|escape:javascript|php:"strip_tags"|html_entity_decode:$smarty.const.ENT_QUOTES:"UTF-8"} ({$requests[$current_class->table].ref|default:$current_class->decryptId($id)})
									</div>
									<div>
										<small class="pull-right muted">
											{ATF::$usr->trans("creer")}
											{if $requests[$current_class->table]["date"]}
												&nbsp;
			                                    {ATF::$usr->trans("le")}
			                                    &nbsp;
			                                    {ATF::$usr->date_trans($requests[$current_class->table]["date"],false,true,true)}
		                                    {/if}
		                                    {if $requests[$current_class->table]["id_user"] || $requests[$current_class->table]["id_owner"]}
			                                    &nbsp;
			                                    {ATF::$usr->trans("par")}
			                                    &nbsp;
			                                    {if $current_class->createurField && $current_class->createurField == id_contact}
			                                    	{ATF::contact()->nom($requests[$current_class->table][$current_class->createurField])|escape:javascript}
			                                    {else}
			                                    	{ATF::user()->nom($requests[$current_class->table]["id_user"]|default:$requests[$current_class->table]["id_owner"])|escape:javascript}
			                                    {/if}
		                                	{/if}
		                                </small>
	                                </div>',
							style: {
								float: 'left'
							}
						}
					{/if}
				{/if}
				{if $requests[$current_class->table].etat}
					{if ATF::$html->template_exists("`$current_class->table`-etat.tpl.js")}
						,{include file="`$current_class->table`-etat.tpl.js" infos=$requests[$current_class->table]}
					{else}
						,{
							html:'<div class="label label-{$requests[$current_class->table].etat}">{ATF::$usr->trans($requests[$current_class->table].etat,$current_class->table)|escape:javascript}</div>',
							cls: "labelCtn"
						}
					{/if}
				{/if}
				{if ATF::$html->template_exists("`$current_class->table`-select-title-suffix.tpl.js") && $id}
					,{include file="`$current_class->table`-select-title-suffix.tpl.js" infos=$requests[$current_class->table]}
				{/if}

				,{
					xtype: 'buttongroup',
					id:'buttonGroupEdit{$id}',
					style: {
						float: 'right'
					},
					defaults: {
						xtype:'button',
						scale: 'medium',
						iconAlign:'top',
						style: {
							padding:'2px'
						}
					},
					listeners : {
						afterrender: function afterRenderfORM(el){
							{if $smarty.request.edit}
								editHandler(Ext.getCmp('btnEdit{$id}'));
								ATF.setFormIsActive();
							{/if}
						}
					},
					items: [
					{if ATF::$html->template_exists("`$current_class->table`-extraFormButtons.tpl.js")}
						{include file="`$current_class->table`-extraFormButtons.tpl.js"}
					{/if}
					{if !$current_class->noCollapse}
						{
							iconCls: 'iconCollapse',
							id:'btnCollapse{$id}',
							enableToggle: true,
							tooltip:'{ATF::$usr->trans("collapse")}',
							toggleHandler: collapseHandler ,
							pressed:true,
						},
					{/if}
					{if ATF::$usr->privilege($module,'insert')}
						{
							iconCls: 'iconAdd',
							id:'btnAdd{$id}',
							hidden:{if ATF::_r('event')!="insert" && ATF::_r('event')!="update"}false{else}true{/if},
							tooltip:'{ATF::$usr->trans("insert")}',
							handler: insertHandler
						},
					{/if}
					{if ATF::$usr->privilege($module,'update')}
						{
							iconCls: 'iconEdit',
							id:'btnEdit{$id}',
							handler: editHandler,
							tooltip:'{ATF::$usr->trans("mode_edition")}',
							hidden:{if ATF::_r('event')!="insert" && ATF::_r('event')!="update"}false{else}true{/if},
							listeners: {

							}
						},
						{
							iconCls: 'iconCancel',
							id:'btnEditCancel{$id}',
							hidden: true,
							tooltip:'{ATF::$usr->trans("annuler")}',
							handler: cancelEditHandler
						},
						{
							iconCls: 'iconSave',
							id:'btnEditSave{$id}',
							hidden: {if ATF::_r('event')=="insert"}false{else}true{/if},
							tooltip:'{ATF::$usr->trans("save")}',
							handler: saveHandler
						},
					{/if}
					{if ATF::$usr->privilege($module,'delete')}
						{
							iconCls: 'iconDelete',
							id:'btnDelete{$id}',
							tooltip:'{ATF::$usr->trans("delete")}',
							hidden:{if ATF::_r('event')!="insert" && ATF::_r('event')!="update"}false{else}true{/if},
							handler: deleteHandler
						}
					{/if}
					]
				}]
			}
			{if ATF::$html->template_exists("`$current_class->table`-select-suffix.tpl.js") && $id}
				{include file="`$current_class->table`-select-suffix.tpl.js" infos=$requests[$current_class->table]}
			{/if}

			,
				ATF.basicInfo
			,
			{if ATF::$html->template_exists("`$current_class->table`-select-prefix.tpl.js") && $id}
				{include file="`$current_class->table`-select-prefix.tpl.js" infos=$requests[$current_class->table]}
			{/if}



			{
				xtype: 'panel',
				anchor: "100%",
				border: false,
				id:"onglets{$current_class->table}{$id}"

			},


		]
	});

	{if $id}

		var tab = new Ext.TabPanel({
			id:"OngletsTabPanel{$current_class->table}{$id}",
			renderTo:'onglets{$current_class->table}{$id}'
		});
		{* Fonction d insertion *}
		{foreach from=$current_class->onglets key=key item=item}

			{if is_array($item)}
				{$mod = $key}
			{else}
				{$mod = $item}
			{/if}

			{$name_table = ATF::getClass($mod)->name()}


			{if ATF::$usr->privilege($name_table,'select')}


				{$fk=["{$name_table}.id_`$current_class->table`"=>$id]}
				{if $id}
				    {if $field}
				        {$fk=[$field=>$id]}
				    {elseif $table}
				        {$fk=["`$table`.id_`$current_class->table`"=>$id]}
				    {elseif $current_class}
				        {$fk=["{ATF::$name_table()->table}.id_`$current_class->table`"=>$id]}
				    {elseif $current_class->table==$name_table}
				        {$fk=["{ATF::$name_table()->table}.id_parent"=>$id]}
				    {/if}
				{/if}

				{if $fk}
					{$id_fk=$id}
					{$id_fk=$id_fk|cryptid}
					{$url_extra="id_`$current_class->table`=`$id_fk`"}
				{/if}


			    {$nom="gsa"}
			    {if $parent_class->table}
			    	{$nom="`$nom`_`$parent_class->table`"}
			    {/if}
			    {if $name_table}
			    	{$nom="`$nom`_`$name_table`"}
			    {/if}
			    {if $id}
			    	{$nom="`$nom`_`$id`"}
			    {/if}

				{* Fonction d insertion *}
				var insertNow = function() {
					if(!$("insertion").length){
						ATF.loadMask.show();
						Ext.Ajax.request({
						   url: '{$name_table},getUpdateForm.ajax',
						   params:{
							   event:'insert',
							   table:'{ATF::getClass($mod)->table}',
							   formName:'formulaire'
							   {if $url_extra},'{$url_extra|replace:"=":"':'"|replace:"&":"',"}'{/if} {* L"avoir plutôt en POST *}
						   },
						   success: function (response, opts) {
							   	ATF.loadMask.hide();
								eval(response.responseText);
								formulaire.closable=true;
								if (!formulaire.listeners) {
									formulaire.listeners={};
								}
								formulaire.listeners.close=function(){
									ATF.unsetFormIsActive();
								};
								formulaire.id = "insertion";
								var t = Ext.getCmp("OngletsTabPanel{$current_class->table}{$id}");
								{if !ATF::getClass($mod)->selectExtjs}
									ATF.basicInfo = new Ext.FormPanel(formulaire);
									var i = t.add(ATF.basicInfo);
								{else}
									var i = t.add(mainPanel);
								{/if}
								t.setActiveTab(i);
								t.getActiveTab().setTitle("{ATF::$usr->trans(ATF::getClass($mod)->table,module)} : Nouvel élément");
								/* Sinon le panel ne s'adapte pas en hauteur dans le cas de plusieurs cadres fermé */
								t.getActiveTab().setHeight("auto");
						   },
						   failure: function() {
								ATF.loadMask.hide();
								alert('erreur_inconnue');
						   }
						});
					}else{
						alert("{ATF::$usr->trans('form_ouvert')|escape:javascript}");
					}
				};


				{include file="generic-gridpanel.tpl.js"
					renderTo=null
					q=null
					pager=$nom
					search=tru
					current_class=ATF::getClass($mod)
					pager_parent="`$pager`"
					id=$mod
					autoHeight=true
					fk=$fk
					name="grid_`$mod`"
					fromTab=tab
					closable=false
					title=ATF::$usr->trans($name_table,'module')}
			{/if}
		{/foreach}
		tab.setActiveTab(0);
	{/if}
{/strip}
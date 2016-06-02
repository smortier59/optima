{*capture assign=toto*}{strip}
{*
@param string $key Nom du champ de base de données
@param int $anchor 
@param int $pageSize 0 Pour ne pas avoir de pagination dans un combo autocomplete
@param string $function Nom de la fonction à utiliser en AJAX pour le combo autocomplete
@param boolean $noHiddenField Ne pas ajouter de champ hidden automatiquement dans un combo autocomplete
@param boolean $always_display Affiche la combo même s''il n'y a pas d'éléments.
@param string $xtype Type extJS
@param string $requests Informations sur un enregistrement de référence
*}


{if $current_class->selectExtjs} 
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
	{if $event=="insert" || $event == "speedInsert"} 
		{$hidden=false}
	{else}
		{if $value}
			{$hidden = false}
		{else}
			{$hidden = true}
		{/if}
	{/if}

	{if $forceVisible} {$hidden=false} {$forceVisible=false} {/if}
	{if $forceHidden} {$hidden=true} {$forceHidden=false} {/if}
{else}
	{if $value!==false}
		{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
	{/if}
{/if}

{if $item.inputValue}
	{$inputValue = $item.inputValue}
{/if}
{$blankOnEdit=$blankOnEdit|default:$item.blankOnEdit|default:false}
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{$data=$data|default:$item.data}
{$anchor=$anchor|default:95}
{$listeners=$listeners|default:$item.listeners}

{if $item.autocomplete}	
	{$function=$function|default:$item.autocomplete.function|default:autocomplete}	
	{$pageSize=$pageSize|default:$item.autocomplete.pageSize}
{else}
	{$function=$function|default:autocomplete}	
{/if}
{if !$requests && $requests!==false}
	{$requests=ATF::_r()}
{/if}

{* gestion des champs obligatoires *}
{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{else}
	{$img_ob=null}
{/if}


{if ATF::getClass($current_class->fk_from($key))}	
    {$key_class=ATF::getClass($current_class->fk_from($key))}
    {$value=$key_class->decryptId($value)|default:$value}   
    {$key_class->q->reset()->end()}   

	{$f = 'id_'|cat:$current_class->table}

    {if $condition_field && $condition_value}    	
        {$condition_class=ATF::getClass($key_class->fk_from($condition_field))}
        {if $condition_class}
            {$key_class->q->addCondition("{$key_class->table}.{$condition_field}",$condition_class->decryptId($condition_value))->end()}            
        {else}
            {$key_class->q->addCondition("{$key_class->table}.{$condition_field}",$condition_value)->end()}
        {/if}
    {else if $current_class->table && $requests[$current_class->table][$f] && array_key_exists($f,$key_class->html_structure()) && $current_class->table!=$key_class->table && $item.allowFilter}
        {$key_class->q->addCondition("{$key_class->table}.id_{$current_class->table}",$current_class->decryptId($requests[$current_class->table][$f]))->end()}            
        {$condition_field="id_{$current_class->table}"}
        {$condition_value=$current_class->cryptId($requests[$current_class->table][$f])}

    {/if}
    {if !$surpasseCount}    		
		{$count=$key_class->count()}  
    {else}
		{$count = $key_class->count(NULL,true,$function,$condition_field,$condition_value)}    		
    {/if}
    
    
    

    {if $count>0||$always_display}
		{if !$noHiddenField}
			[
		{/if}

		jQuery.extend(
		{*if !$disabled && !$item.disabled*} 			
			 ATF.autocompleteConfig({			 	
				url:'{$key_class->name()},{$function}.ajax,{http_build_query($current_class->autocompleteConditions($key_class,$requests,$condition_field,$condition_value,$key))}{if $order_field && $order_sens}&order_field={$order_field}&order_sens={$order_sens}{/if}{$extra}'
				,mapping:
					{if $item.autocomplete && $item.autocomplete.mapping}
						{$item.autocomplete.mapping|json_encode}
					{elseif $key_class->getAutocompleteMapping()}
						{$key_class->getAutocompleteMapping(true)}
					{else}
						[{ name: 'id', mapping: 0 },{ name: 'nom', mapping: 1 },{ name: 'detail', mapping: 2, type:'string' },{ name: 'nomBrut', mapping: 'raw_1' }]
					{/if}
				,loadingText:'Recherche...'
				,template:'{include file="generic-autocomplete.tpl.htm" table=$key_class->name()}'
				,onSelect:function(record){
					Ext.getCmp('{$id}').setValue(record.data.id);
					{if !$noHiddenField}
						Ext.getCmp('label_{$id}').setValue(Ext.util.Format.stripTags(record.data.nom));
					{/if}
					this.collapse();

					{if ATF::$html->template_exists("{$current_class->table}-{$key}-oncomplete.tpl.js")}
						{include file="{$current_class->table}-{$key}-oncomplete.tpl.js"}
					{elseif ATF::$html->template_exists("generic-{$key}-oncomplete.tpl.js")}
						{include file="generic-{$key}-oncomplete.tpl.js"}
					{elseif $extJSGridComboboxSeparator} {* Lorsqu'on a besoin d'afficher un libellé spécial, et non la clé à stocker, on met la clé après le séparateur ATF.extJSGridComboboxSeparator, il sera traité par le renderer ensuite *}
						if (record.data.nom) {
							Ext.getCmp('{$id}').setValue(record.data.nom + ATF.extJSGridComboboxSeparator + record.data.id);
						}
					{/if}
				}
			},true),
		{*/if*}
		{ 
			xtype:'combo'
			{if $key_class->colonnes.speed_insert && ATF::$usr->privilege($key_class->name(),'insert') && !$key_class->no_insert && $key_class->table != $current_class->table}
				{capture assign=speedInsert}{strip}
					<a href="javascript:;" onclick="var w=new Ext.Window({
							layout: 'fit',
							title: '{ATF::$usr->trans($key_class->name(),module)|escape:javascript} | {if $identifiant}{$current_class->nom($identifiant)|replace:'"':""|escape:javascript}{else}Création{/if}', {* " *}
							width:800,
							id: 'speed_insert{$id}',
							monitorResize:true,
							autoLoad:{ url: '{$key_class->name()},speed_insert_template.ajax,id={$id}&parent_class={$current_class->table}
									{foreach from=$key_class->colonnes.speed_insert key=k_speed item=i_speed}
										{if ATF::_r($k_speed)}
											&{$k_speed}={ATF::_r($k_speed)}
										{elseif $requests[$current_class->table][$k_speed]}
											&{$k_speed}={$requests[$current_class->table][$k_speed]|cryptId}
										{/if}
									{/foreach}', scripts:true }
						}).show();
						w.setPosition(w.x,w.y-100);
						return false;">
						<img src="{ATF::$staticserver}images/icones/insert.png" height="16" width="16" alt="" />
					</a>
				{/strip}{/capture}
			{/if}
			{if $value}
				{capture assign=quickLink}{strip}
					<a href="#{$key_class->name()}-select-{$value|cryptid}.html" target="_blank" {tip text=ATF::$usr->trans("generic_select_all_select")|mt:[module=>ATF::$usr->trans($key_class->name(),module)]|escape:htmlall|cat:" ({$key_class->nom($value)})"}><img src="{ATF::$staticserver}images/icones/select.png" height="16" width="16" alt="" /></a>
				{/strip}{/capture}
			{/if}
			,fieldLabel: '{$fieldLabel|escape:javascript} {$speedInsert|escape:javascript} {$img_ob|escape:javascript}{$quickLink|escape:javascript}'
			,name: '{if !$noHiddenField}label_{/if}{$name}'
			,id: '{if !$noHiddenField}label_{/if}{$id}'
			,value: "{if $value}{$key_class->nom($value)|escape:javascript}{/if}"
			{if $readonly || $item.readonly},readOnly:true{/if}
			{if $disabled || $item.disabled},disabled:true{/if}
			,emptyText:'{$emptyText|default:ATF::$usr->trans(aucun)|escape:javascript}{if !$est_nul} ({ATF::$usr->trans(obligatoire)|escape:javascript}){/if}'
			,anchor:{if $anchor}'{$anchor}%'{else}"95%"{/if}
			,listeners :{
				{* pour éviter de conserver un id différent de l element mis en label *}
				expand: function(field, e){	
					if(this.id!=this.id.replace("label_","")){
						Ext.getCmp(this.id.replace("label_","")).setValue(null);
					}
				},
				change:function(obj){
					if(!this.value && $('#'+this.id.replace("label_",""))){
						Ext.getCmp(this.id.replace("label_","")).setValue(null);
					}

					{if ATF::$html->template_exists("`$current_class->table`-`$k`-onchange.tpl.js")}
						{include file="`$current_class->table`-`$k`-onchange.tpl.js"}
					{/if}
				},
				render: function (c) {
					{if ATF::$usr->trans("{$key}_info",$current_class->table)!="{$key}_info" || $item.quickTips}
						{* Gestion des tooltips *}
						{if $item.quickTips.url}
							var url = "{$item.quickTips.url}";
						{else}
							var url = "{$current_class->table},getQuickTips.ajax,field={$key}";
						{/if}
						 new Ext.ToolTip({
							target:c.getEl()
							,autoLoad: { url: url }
							{if $item.quickTips.cls},cls: "{$item.quickTips.cls}" {/if}
							{if $item.quickTips.title},title: "{$item.quickTips.title}" {/if}
							{if $item.quickTips.width},width: "{$item.quickTips.width}" 
							{else} ,width: 200{/if}
						});
					{/if}

					{* Additional render order *}


					{if ATF::$html->template_exists("`$current_class->table`-`$key`-render.tpl.js")}
						{include file="`$current_class->table`-`$key`-render.tpl.js"}
					{/if}

				}

			}
			{if $listeners},listeners :{
				{foreach from=$listeners key=event item=func}
					{$event}:{$func}{if !$func@last},{/if}
				{/foreach}
			}
			{/if}

		})
		{if !$noHiddenField}
			,{
				xtype:'hidden',
				fieldLabel: '{$fieldLabel|escape:javascript}',
				name: '{$name}',
				id: '{$id}',
				value: '{$value|escape:javascript}'
				{if $width},width:1{/if}
			}]
		{/if}
    {else}
		{
			xtype:'label'
			{if $fieldLabel},fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'{/if}
			,text:"{if !$fieldLabel}{ATF::$usr->trans($current_class->table,'module')} : {/if}{ATF::$usr->trans('aucune_donnee')}"
			{if $listeners},listeners :{
				{foreach from=$listeners key=event item=func}
					{$event}:{$func}{if !$func@last},{/if}
				{/foreach}
			}
			{/if}
		}
	{/if}
{elseif $xtype=="datefield" && ($item.type=="time" || $item.type=="datetime")}
	{$alternateName="date`$name`"}{$alternateId="date`$id`"}
	{
		xtype: 'compositefield'
		,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
		,items: [
			{if $item.type=="datetime" || $item.type=="date"}{
				xtype:'{$xtype}'
				{if $listeners},listeners :{
					{foreach from=$listeners key=event item=func}
						{$event}:{$func}{if !$func@last},{/if}
					{/foreach}
				}
				{else}
					,listeners :{
						change:function(){
							Ext.getCmp('{$id}').setValue(Ext.getCmp('{$alternateId}').value{if $item.type=="datetime"}+' '+Ext.getCmp('{$alternateId}_time').value{/if});
						}
						
					}
				{/if}
				,format:'d-m-Y'
				{if $value}
					{$alternateValue=$value|date_format:"%d-%m-%Y"}
				{else}
					,value: '{$smarty.now|date_format:"%d-%m-%Y"}'
				{/if}
				,width:120
				,name: '{$alternateName|default:$name}'
				,maxValue:new Date('01/01/2030')
				,id: '{$alternateId|default:$id}'
				{if $value || $alternateValue},value: '{$alternateValue|default:$value|escape:javascript}'{/if}
				{if $disabled || $item.disabled},disabled:true{/if}
				{if $readonly || $item.readonly},readOnly:true{/if}
			}{/if}
			{if $item.type=="datetime"},{/if}
			{if $item.type=="datetime" || $item.type=="time"}{ 
				xtype:'timefield'
				,name: '{$alternateName}_time'
				,id: '{$alternateId}_time'
				,value:'{$value|default:$smarty.now|date_format:"%H:%M"}'
				,format:'H:i'
				{if $item.type=="datetime"}
					,minValue: '9:00'
					,maxValue: '18:00'
					,increment: 30
				{else}
					,minValue: {if $item.min}'{$item.min}'{else} '8:00' {/if}
					,maxValue: '20:00'
					,increment: 15
				{/if}
				,width:65
				{if $listeners},listeners :{
					{foreach from=$listeners key=event item=func}
						{$event}:{$func}{if !$func@last},{/if}
					{/foreach}
				}
				{else}
					,listeners :{
						change:function(){
							Ext.getCmp('{$id}').setValue({if $item.type=="datetime"}Ext.getCmp('{$alternateId}').value+' '+{/if}Ext.getCmp('{$alternateId}_time').value);
						}
					}
				{/if}				
				
				{if $disabled || $item.disabled},disabled:true{/if}
				{if $readonly || $item.readonly},readOnly:true{/if}
			}{/if}
		]
	},{
		xtype:'hidden'
		,name: '{$name}'
		,id: '{$id}'
		,value:{if $item.type=="datetime" || $item.type=="date"}'{$alternateValue|default:$smarty.now|date_format:"%d-%m-%Y"}'{/if}{if $item.type=="datetime"}+' '+{/if}{if $item.type=="datetime" || $item.type=="time"}'{$value|default:$smarty.now|date_format:"%H:%M"}'{/if}
		{if $listeners},listeners :{
			{foreach from=$listeners key=event item=func}
				{$event}:{$func}{if !$func@last},{/if}
			{/foreach}
		}
		{/if}
	}
{elseif $xtype==="fieldset" && $item.panel_key}
	{include file="generic-panel.tpl.js" 
		colonnes=$current_class->colonnes($item.panel_key,$event,'true') 
		title=ATF::$usr->trans("cadre_{$item.panel_key}",$current_class->table) 
		requests=$requests 
		colspan=$current_class->panels[$item.panel_key].nbCols|default:2 
		collapsed=!$current_class->panels[$item.panel_key].visible
		checkable=$current_class->panels[$item.panel_key].checkboxToggle
		hidden=$current_class->panels[$item.panel_key].hidden
		readingDirection=$current_class->panels[$item.panel_key].readingDirection|default:false
		collapsible=$current_class->panels[$item.panel_key].collapsible|default:false
		panel_key=$item.panel_key
		border=$current_class->panels[$item.panel_key].border|default:false
		readonly=$readonly
		est_nul=null
		xtype=null
		anchor=null
		listeners=null
		function=null}
{elseif $xtype==="compositefield"}
	{
		xtype: 'compositefield'
		,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
		,defaults: {
			flex:1
		}
		,anchor:"95%"
		,combineErrors: false
		,hidden:false
		,items: [
			{foreach from=$item.fields item=i key=k}
				{if !$i@first},{/if}
				{* Afin de pouvoir gérer un template particulier pour un champ *}
				{if ATF::$html->template_exists("`$current_class->table`-`$k`-`$event`.tpl.js")} 
					{$nom_fichier="`$current_class->table`-`$k`-`$event`.tpl.js"}
				{elseif $event=='cloner' && ATF::$html->template_exists("`$current_class->table`-`$k`-insert.tpl.js")} 
					{$nom_fichier="`$current_class->table`-`$k`-insert.tpl.js"}
				{elseif $event!==update && ATF::$html->template_exists("`$current_class->table`-`$k`-update.tpl.js")}
					{$nom_fichier="`$current_class->table`-`$k`-update.tpl.js"}
				{elseif ATF::$html->template_exists("`$current_class->table`-`$k`.tpl.js")}
					{$nom_fichier="`$current_class->table`-`$k`.tpl.js"}
				{else}   
					{$nom_fichier="generic-field-textfield.tpl.js"}	
				{/if}
				{include file=$nom_fichier
					fieldLabel=null
					name="`$current_class->table`[`$k`]"
					id="`$current_class->table`[`$k`]"
					key=$k
					value=$requests["{$current_class->table}"][$k]|default:$requests[$k]
					est_nul=$i["null"]
					item=$i
					xtype=null
					anchor=null
					forceVisible=true
					readonly=$readonly
					value=null
					function=null
					listeners=null
					emptyText=ATF::$usr->trans($k,$current_class->table)}
			{/foreach}
		]
	}
{elseif $item.color}
	{
		xtype:'colorfield',
		fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}',
		id: '{$alternateId|default:$id}',
		{if $readonly || $item.readonly}readOnly:true,{/if}
		value:'{$value}',
		{if $anchor || $item.anchor}anchor:'{$anchor|default:$item.anchor}%'{/if}
	}
{elseif $xtype=="switchbutton"}
	{
		html: '{strip}
			<ul class="switchbutton {$key}_switch">
				<li {if $item.default=="oui" || !$item.default} class="on" {/if}>
					<a href="javascript:;">{$item.data[0]|default:"OUI"}</a>
				</li>
				<li {if $item.default=="non"} class="on"{/if} >
					<a href="javascript:;">{$item.data[1]|default:"NON"}</a>
				</li>
			</ul>
			{/strip}'
		,fieldLabel: '{$fieldLabel|escape:javascript}'
		,name: '{$alternateName|default:$name}_label'
		,id: '{$alternateId|default:$id}_label'
		{if $readonly || $item.readonly},readOnly:true{/if}
		,listeners :{
			'afterrender': function (c) {
				Ext.get(Ext.query("ul.{$key}_switch li")).on('click', function(e, t, eOpts) { 
				    var el = Ext.get(this), p;
				    el.addClass("on");
				    if((p = el.next())) {
				        p.removeClass("on");
				        Ext.getCmp("{$alternateId|default:$id}").setValue("{$item.data[0]|default:'oui'}");
				    } else if ((p = el.prev())){
				        p.removeClass("on");
				        Ext.getCmp("{$alternateId|default:$id}").setValue("{$item.data[1]|default:'non'}");
				    }
				});
			}
		}
	}
	,
	{
		xtype:"textfield"
		,name: '{$alternateName|default:$name}'
		,id: '{$alternateId|default:$id}'
		,hidden: true
		,value: '{$item.default|default:"oui"}'
	}
{else}	
	{
		xtype:'{$xtype}'
		{if $xtype=="combo"}
			,typeAhead:true
			,triggerAction:'all'
			,editable:false
			,lazyRender:true
			,mode:'local'
			,preventMark:true
			,hiddenName:'{$name}'{$alternateName="combo`$name`"}{$alternateId="combo`$id`"}
			,anchor: "95%"
			,store: new Ext.data.ArrayStore({
				fields: [
					'myId',
					'displayText'
				],			
				data: [
					{if $est_nul}
						['', '{$item.libNull|default:"-"}'],
					{/if}
					{foreach from=$data item=i}
						['{$i}', '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::preferences()->getNomFromTable($i,$key)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}']
						{if !$i@last},{/if}
					{/foreach}
				]
			})
			,value: "{$requests[$current_class->table][$key]|default:$item.default|escape:javascript}"
			,valueField: 'myId'
			,displayField: 'displayText'

		{elseif $xtype=="multiselect"}
			,width: 'auto'
			,allowBlank:true
			,minSelections:0
			,height: 150
			,store: [
				{foreach from=$data key=k item=i}				
					['{$i}', '{ATF::$usr->trans($i,"`$current_class->table`_`$k`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|default:ATF::$usr->trans($i)|escape:javascript}']
					{if !$i@last},{/if}
				{/foreach}
			]
		{elseif $xtype=="checkboxgroup"}  {* En construction... (YG) *}
			,defaultType:'checkbox'
			,columns: 2
			,itemCls: 'x-check-group-alt'
			,items: [{strip}
				{foreach from=$data item=i}
					{ 
						name: '{$current_class->table}[{$key}][]',
						boxLabel: '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}', 
						inputValue: '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}'
 					}
					{if !$i@last},{/if}
				{/foreach}
			{/strip}]
		{elseif $xtype=="radiogroup"}
			,columns: {$item.nbCols|default:2}
			,itemCls: 'x-check-group-alt'
			,items: [{strip}
				{foreach from=$data key=k item=i}
					{
						{if $i.xtype=="radiofield"}
							xtype: 'radiofield',
							boxLabel: '{ATF::$usr->trans($i.name,$current_class->table)|escape:javascript}', 
							name: '{$current_class->table}[{$key}]',
							value: '{$i}',
							checked: {if $i==$item.default && !$requests[$current_class->table][$key]}true{else}false{/if}
						{else}
							boxLabel: '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}', 
							name: '{$current_class->table}[{$key}]',
							inputValue: '{$i}',
							value: '{$i}',
							checked: {if $i==$requests[$current_class->table][$key] || ($i==$item.default && !$requests[$current_class->table][$key])}true{else}false{/if}
						{/if} 
					}
					{if !$i@last},{/if}
				{/foreach}
			{/strip}]
		{elseif $xtype=="htmleditor"}
			{if ATF::$html->template_exists("`$current_class->table`-`$key`-`$xtype`.tpl.js")}
				{include file="`$current_class->table`-`$key`-`$xtype`.tpl.js"}
			{else}
				,height:{$item.height|default:$height|default:200}
			{/if} 
		{elseif $xtype=="datefield"}
			,width:120
			{$anchor=null}
			{if $key=="date_debut"}
				,vtype: 'daterange'
				,endDateField: "{$current_class->table}[date_fin]"
				,listeners :{
					change:function(){
						if(!$('#{$current_class->table}\\[date_fin\\]').val()){
							$('#{$current_class->table}\\[date_fin\\]').val(this.value);
						}
					}
				}
			{elseif $key=="date_fin"}
				,vtype: 'daterange'
				,startDateField: "{$current_class->table}[date_debut]"
			{/if}
			,format:'d-m-Y'
		{elseif $xtype=="numberfield"}
			,decimalPrecision:20
		{elseif $xtype=="box"}
			{if ATF::$html->template_exists("`$current_class->table`-`$key`-`$xtype`.tpl.js")} 
				{include file="`$current_class->table`-`$key`-`$xtype`.tpl.js"}
			{else}
				,html:"{$item.html}"
			{/if}
		{else}
			{if $item.xtype == "textarea" && $item.placeholder} ,emptyText: "{$item.placeholder}" {/if}
			{if $item.maxlength}
				,maxLength:{$item.maxlength}
				,maxLengthText:'La taille maximum pour ce champ est de {$item.maxlength} caractères.'
			{/if}	
		{/if}
		,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
		,name: '{$alternateName|default:$name}'
		,id: '{$alternateId|default:$id}'
		,cls: "panel_{$panel_key}"
		{if $emptyText  || $item.emptyText},emptyText:'{$emptyText|default:$item.emptyText|escape:javascript}{if !$est_nul} ({ATF::$usr->trans(obligatoire)|escape:javascript}){/if}'{/if}
		{if $labelStyle  || $item.labelStyle},labelStyle: '{$labelStyle|default:$item.labelStyle}'{/if}
		{if $originalValue},originalValue:'{$originalValue|escape:javascript}'{/if}
		{if $inputValue},inputValue:'{$inputValue|escape:javascript}'{/if}
		{if $value || $value==='0' || $alternateValue || $alternateValue==='0'}
			{if $item.formatNumeric}
				{if $item.num_decimal_places}
					{$value_def=$current_class->formatNumeric($value,$item.num_decimal_places)}
				{else}
					{$value_def=$current_class->formatNumeric($value)}
				{/if}
			{else}
				{$value_def=$alternateValue|default:$value|escape:javascript}
			{/if}
			,value: {if $blankOnEdit} "" {else} '{$value_def}' {/if}
		{/if}
		{if $disabled || $item.disabled},disabled:true{/if}
		{if $readonly || $item.readonly},readOnly:true{/if}
		{if $hidden || $item.hidden}
			,hidden:true
		{/if}
		{if $renderTo  || $item.hidden},renderTo:'{$renderTo|escape:javascript}'{/if}
		{if $anchor || $item.anchor},anchor:'{$anchor|default:$item.anchor}%'{/if}
		{if $style || $item.style},style:'{$style|default:$item.style}'{/if}
		{if $width || $item.width},width:{$width|default:$item.width}{/if}
		{if $height || $item.height},height:{$height|default:$item.height}{/if}
		{if $checked || $item.checked},checked:{$checked|default:$item.checked}{/if}		
		,listeners :{
			{if ATF::$usr->trans("{$key}_info",$current_class->table)!="{$key}_info" || $item.quickTips}
				'render': function (c) {
					{if $item.quickTips.url}
						var url = "{$item.quickTips.url}";
					{else}
						var url = "{$current_class->table},getQuickTips.ajax,field={$key}";
					{/if}
					 new Ext.ToolTip({
						target:c.getEl()
						,autoLoad: { url: url }
						{if $item.quickTips.cls},cls: "{$item.quickTips.cls}" {/if}
						{if $item.quickTips.title},title: "{$item.quickTips.title}" {/if}
						{if $item.quickTips.width},width: "{$item.quickTips.width}" 
						{else} ,width: 200{/if}
					});
				},
			{/if}
			{if $listeners}
				{foreach from=$listeners key=event item=func}
					{$event}:{$func}{if !$func@last},{/if}
				{/foreach}
			{/if}
		}
		{if $inputType || $item.inputType},inputType:'{$inputType|default:$item.inputType}'{/if}
	}
	
{/if}

{/strip}
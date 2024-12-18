{
    title:'{ATF::$usr->trans("title_report","importer")|escape:"javascript"}',
    columnWidth: .5,
	style: {
		margin: '5px',
		border: '1px solid #d0d0d0',
		paddingBottom: '5px'
	},
	bodyStyle:'padding:5px 5px 0',
	tpl: new Ext.XTemplate(
		'<p>{ATF::$usr->trans("resultat","importer")|escape:"javascript"} : <span class="{literal}{etat}{/literal}">{literal}{etatLib}{/literal}</span></p>',
		'<br>',
		'<tpl if="etat == &quot;probleme&quot;">',
			'<p>{ATF::$usr->trans("erreur","importer")|escape:"javascript"} :',
				'<br><br>',
				'<span class="bold">',
					'{literal}{erreur}{/literal}',
				'</span>',
			'</p>',
			'<br><br><br>',
			'<div style="text-align: center; width:100%;">',
				'<div style="width:50%; float:left;">',
					'<div class="button" onclick="ATF.ajax(\'importer,restart.ajax\',\'id_importer={ATF::_r(id_importer)}\');">',
					    '<div id="left">',
					        '<img src="{ATF::$staticserver}images/icones/refresh2.png" align="" style="width:16px; height:16px" />',
					    '</div>',
					    '<div id="middle" style="width: 150px">',
					    	'<span>',
					        	'{ATF::$usr->trans("restart","importer")|escape:"javascript"}',
					        '</span>',
					    '</div>',
					    '<div id="right"></div>',
					'</div>',						
				'</div>',
				'<div style="width:50%; float:right;">',
					'<div class="button" onclick="ATF.ajax(\'importer,cancel.ajax\',\'id_importer={ATF::_r(id_importer)}\');">',
					    '<div id="left">',
					        '<img src="{ATF::$staticserver}images/icones/cancel.png" align="" style="width:16px; height:16px" />',
					    '</div>',
					    '<div id="middle" style="width: 150px">',
					    	'<span>',
					        	'{ATF::$usr->trans("cancel","importer")|escape:"javascript"}',
					        '</span>',
					    '</div>',
					    '<div id="right"></div>',
					'</div>',			
				'</div>',			
			'</div>',
		'</tpl>',		
		'<tpl if="etat == &quot;en_attente&quot;">',
			'<div style="text-align: center; width:100%;">',
				'<div style="width:50%; float:left;">',
					'<div class="button" onclick="window.location.reload();">',
					    '<div id="left">',
					        '<img src="{ATF::$staticserver}images/icones/synchronisation.png" align="" style="width:16px; height:16px" />',
					    '</div>',
					    '<div id="middle" style="width: 150px">',
					    	'<span>',
					        	'{ATF::$usr->trans("reload","importer")|escape:"javascript"}',
					        '</span>',
					    '</div>',
					    '<div id="right"></div>',
					'</div>',			
				'</div>',
				'<div style="width:50%; float:right;">',
					'<div class="button" onclick="ATF.ajax(\'importer,cancel.ajax\',\'id_importer={ATF::_r(id_importer)}\');">',
					    '<div id="left">',
					        '<img src="{ATF::$staticserver}images/icones/cancel.png" align="" style="width:16px; height:16px" />',
					    '</div>',
					    '<div id="middle" style="width: 150px">',
					    	'<span>',
					        	'{ATF::$usr->trans("cancel","importer")|escape:"javascript"}',
					        '</span>',
					    '</div>',
					    '<div id="right"></div>',
					'</div>',			
				'</div>',
			'</div>',
		'</tpl>',
		'<tpl if="etat == &quot;fini&quot;">',
			'<p>{ATF::$usr->trans("detail_de_limport","importer")|escape:"javascript"} :',
				'<br><br>',
				'<p class="bold">{ATF::$usr->trans("lignes_inserer","importer")|escape:"javascript"} : {literal}{lignes_inserer}{/literal}</p>',
				'<p class="bold">{ATF::$usr->trans("lignes_escape","importer")|escape:"javascript"} : {literal}{lignes_ignore}{/literal}</p>',
				'<p class="bold">{ATF::$usr->trans("lignes_update","importer")|escape:"javascript"} : {literal}{lignes_update}{/literal}</p>',
				'<p class="bold">{ATF::$usr->trans("lignes_total","importer")|escape:"javascript"} : {literal}{lignes_total}{/literal}</p>',
			'</p>',
		'</tpl>',
		'<br>'

	),
	data: {
		'etat': '{$requests["{$current_class->table}"]["etat"]}',
		'etatLib': '{ATF::$usr->trans($requests["{$current_class->table}"]["etat"],importer)}',
		'erreur': '{$requests["{$current_class->table}"]["erreur"]|escape:"javascript"}',
		'lignes_inserer': '{$requests["{$current_class->table}"]["lignes_inserer"]|escape:"javascript"}',
		'lignes_ignore': '{$requests["{$current_class->table}"]["lignes_ignore"]|escape:"javascript"}',
		'lignes_update': '{$requests["{$current_class->table}"]["lignes_update"]|escape:"javascript"}',
		'lignes_total': '{$requests["{$current_class->table}"]["nb"]|escape:"javascript"}'
	}
}
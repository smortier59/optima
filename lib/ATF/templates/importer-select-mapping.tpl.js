{
    title:'{ATF::$usr->trans("title_mapping","importer")|escape:"javascript"}',
    border:true,
    columnWidth: .5,
	style: {
		margin: '5px',
		border: '1px solid #d0d0d0',
		paddingBottom: '5px'
	},
	bodyStyle:'padding:5px 5px 0',
	tpl: new Ext.XTemplate(
		'<table>',
			'<tbody>',
				'<tr>',
					'<td class="field select">{ATF::$usr->trans("filename",$current_class->table)|escape:"javascript"} : </td>',
					'<td class="spacing"></td>',
					'<td class="value" style="min-width:100px;">',
						'<span style="padding-right: 10px">{literal}{filename}{/literal}</span>',
						'<a href="{literal}{table}{/literal}-select-fichier-{literal}{id}{/literal}.dl" target="_blank">',
							'<img alt="Download" title="Download" src="{ATF::$staticserver}images/icones/download.png" style="width:16px; height:16px; vertical-align: middle">',
						'</a>',
					'</td>',
					'<td class="spacing"></td>',
					'<td class="field select">{ATF::$usr->trans("separateur",$current_class->table)|escape:"javascript"} : </td>',
					'<td class="spacing"></td>',
					'<td class="value" style="min-width:100px;">{literal}{separateur}{/literal}</td>',
				'</tr>',
				'<tpl if="liaison">',
					'<tr>',
						'<td class="field select" colspan="7">{ATF::$usr->trans("liaison",$current_class->table)|escape:"javascript"} : </td>',
					'</tr>',
					'<tpl for="liaison">',
						'<tr>',
							'<td colspan="3">{literal}{tableName}{/literal}</td>',
							'<td class="spacing"></td>',
							'<td colspan="3">',
								'<a href="#{literal}{link}{/literal}">',
									'{literal}{val}{/literal}',
								'</a>',
							'</td>',
						'</tr>',
					'</tpl>',
				'</tpl>',
				
			'</tbody>',
		'</table>',
		'<hr>',
		'<h1>{ATF::$usr->trans("mapping",$current_class->table)|escape:"javascript"} : </h1>',
		'<table class="bordered" style="width: 100%">',
			'<thead>',
				'<th class="center" style="width:50%">{ATF::$usr->trans("champs_fichier",$current_class->table)|escape:"javascript"}</th>',
				'<th class="center" style="width:50%">{ATF::$usr->trans("champs_base",$current_class->table)|escape:"javascript"}</th>',
			'</thead>',
			'<tbody>',
				'<tpl for="mapping">',
					'<tpl if="col">',
						'<tr>',
							'<td class="field select center">{literal}{colXLS}{/literal}</td>',
							'<td class="value center" style="min-width:150px;">{literal}{col}{/literal}</td>',
						'</tr>',
					'</tpl>',
				'</tpl>',
			'</tbody>',
		'</table>'
		
	),
	data: {
		'filename': '{$requests["{$current_class->table}"]["filename"]|default:"{ATF::$usr->trans(inconnu)}"}',
		'table': '{$current_class->table}',
		'id': '{$requests["{$current_class->table}"]["id_{$current_class->table}"]|cryptId}',
		'separateur': '{ATF::$usr->trans($requests["{$current_class->table}"]["separateur"],$current_class->table)|escape:"javascript"|default:"{ATF::$usr->trans(inconnu)}"}',
		'liaison': {$current_class->getLiaisonForXTemplate($requests["{$current_class->table}"]["complement"])},
		'mapping': {$current_class->getMappingForXTemplate($requests["{$current_class->table}"]["mapping"])}
	}
}
{strip}
{ 
	text: 'Export des achats'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_data.ajax,onglet={$pager}';
	} 
},
{ 
	text: 'Export des immobilisations'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_cegid.ajax,onglet={$pager}';
	} 
},
{ 
	text: 'Export des immobilisations (y compris déja exporté)'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_cegid.ajax,onglet={$pager}&force=true';
	} 
}
{/strip}
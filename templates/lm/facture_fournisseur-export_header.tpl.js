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
},{
	text: '<hr />'
},
{ 
	text: 'Export fichier AP (ADEO)'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_ap.ajax,onglet={$pager}';
	} 
},
{ 
	text: 'Export Immo pour ADEO'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_immo_adeo.ajax,onglet={$pager}';
	} 
}
{/strip}
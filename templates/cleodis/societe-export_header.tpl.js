{strip}
{ 
	text: '{ATF::$usr->trans(export_societe_contact,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_societe_contact.ajax,onglet={$pager}';
	} 
}
{if ATF::$codename == "cleodis"},
{ 
	text: 'Export ATOL'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_atol.ajax';
	} 
}
{/if}
{/strip}
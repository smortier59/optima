{strip}
{ 
	text: '{ATF::$usr->trans(export_devis_loyer,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_devis_loyer.ajax,onglet={$pager}';
	} 
}
{/strip}
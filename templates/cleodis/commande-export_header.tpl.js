{strip}
{ 
	text: '{ATF::$usr->trans(export_loyer_assurance,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_loyer_assurance.ajax,onglet={$pager}';
	} 
},
{ 
	text: '{ATF::$usr->trans(export_contrat_refinanceur_loyer,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_contrat_refinanceur_loyer.ajax,onglet={$pager}';
	} 
}
{/strip}
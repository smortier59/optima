{strip}
{if ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" }
{ 
	text: '{ATF::$usr->trans(export_comptable,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_special.ajax,onglet={$pager}';
	}
},
{/if}
{if ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe" }
{ 
	text: '{ATF::$usr->trans(export_comptable_facture_rejet,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_special2.ajax,onglet={$pager}';
	}
} 
{/if}
{/strip}
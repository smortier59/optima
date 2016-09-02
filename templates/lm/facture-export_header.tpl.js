{strip}

{ 
	text: '{ATF::$usr->trans(export_tableau_autoportes,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_autoportes.ajax,onglet={$pager}';
	} 
},
{ 
	text: '{ATF::$usr->trans(export_tableau_autoportes_avec_refi,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_autoportes.ajax,onglet={$pager}&refi=true';
	} 
},
{ 
	text: '{ATF::$usr->trans(export_comptable,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_special.ajax,onglet={$pager}';
	}
},
{ 
	text: '{ATF::$usr->trans(export_comptable_facture_rejet,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_special2.ajax,onglet={$pager}';
	}
} 
{/strip}
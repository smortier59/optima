{strip}
{ 
	text: '{ATF::$usr->trans(export_marge_hotline,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_marge_hotline.ajax,onglet={$pager}';
	} 
}
{/strip}
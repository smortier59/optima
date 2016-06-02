{strip}
{
	text: '{ATF::$usr->trans(barcode,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},barcode.ajax,onglet={$pager}';
	}
}
{/strip}
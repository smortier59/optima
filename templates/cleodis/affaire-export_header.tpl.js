{strip}
{if ATF::$codename == "boulanger"}
{
	text: '{ATF::$usr->trans(export_boulanger_commande_client,$current_class->name())}'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_boulanger_commande_client.ajax';
	}
},
{/if}

{/strip}